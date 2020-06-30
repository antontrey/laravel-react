<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\Package;
use App\Models\Tours;
use App\Models\Tourfeature;
use App\Models\Roomfeature;
use App\Models\Roomtypes;
use App\Models\Ticket;
use App\Models\Airlines;
use App\Models\Airports;
use App\Models\Countries;
use App\Models\Currency;
use App\Models\Cities;
use App\Models\Vehicletypes;
use App\Models\Hotels;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;
// use SiteHelpers;
use App\Library\FormHelpers;
use App\Library\SiteHelpers;
use App\Library\GeneralStatuss;
use App\Library\InvoiceStatus;
use Carbon\Carbon;



class PackagesController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'packages';
	static $per_page	= '10';
	public function __construct()
	{
		$this->model = new Package();
		$this->info = $this->model->makeInfo( $this->module);
		// var_dump($this->info);exit;

		$this->access = $this->model->validAccess($this->info['id']);
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'packages',
			'pageUrl'	=>  url('packages'),
			'return'	=> self::returnUrl()
		);
		\App::setLocale(CNF_LANG);
		if (defined('CNF_MULTILANG') && CNF_MULTILANG == '1') {

		$lang = (\Session::get('lang') != "" ? \Session::get('lang') : CNF_LANG);
		\App::setLocale($lang);
		}
	}

	public function getIndex( Request $request )
	{
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')
				->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : 'packageID');
		$order = (!is_null($request->input('order')) ? $request->input('order') : 'asc');
		// End Filter sort and order for query
		// Filter Search for query
		$filter = '';
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		}
    $today = date("Y-m-d");
    $running_tours = \DB::table('packages')
            ->where('start','<=',$today)
            ->where('end','>=',$today)
            ->where('status',1)
            ->count();
    $upcoming_tours = \DB::table('packages')
          ->where('start','>',$today)
            ->where('status',1)
            ->count();
    $old_tours = \DB::table('packages')
            ->where('end','<',$today)
            ->where('status',1)
            ->count();
    $cancelled_tours = \DB::table('packages')
            ->where('status',2)
            ->count();
		$page = $request->input('page', 1);
		$params = array(
			'page'		=> $page ,
			// 'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : static::$per_page ) ,
			'limit'		=> 50 ,
			'sort'		=> $sort ,
			'order'		=> $order,
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		$results = $this->model->getRows( $params );

		$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
		$pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
		$pagination->setPath('packages');
        $this->data['running_tours']        = $running_tours;
		$this->data['upcoming_tours']       = $upcoming_tours;
		$this->data['old_tours']            = $old_tours;
		$this->data['cancelled_tours']      = $cancelled_tours;
		$this->data['today']                 = $today;
		$this->data['tours']                 = Tours::all();
		$this->data['rowData']		= $results['rows'];
		$this->data['pagination']	= $pagination;
		$this->data['pager'] 		= $this->injectPaginate();

		$this->data['i'] = ($page * $params['limit'])- $params['limit'];
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
		$this->data['access']		= $this->access;
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());

		return view('packages.index',$this->data);
	}

	function getUpdate(Request $request, $id = null)
	{
		if($id =='')
		{
			if($this->access['is_add'] ==0 )
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		}
		if($id !='')
		{
			if($this->access['is_edit'] ==0 )
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		}
		$row = $this->model->find($id);
		if($row)
		{
			$this->data['row'] =  $row;
			$this->data['roomfeatures'] = Roomfeature::where("packageID", $id)->get();
		} else {
			$this->data['row'] = $this->model->getColumnTable('packages');
			$this->data['roomfeatures'] = Roomfeature::where("packageID", $id)->get();
			$this->data['row']['cost_count'] = 1;
		}
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);
		$this->data['tickets'] = Ticket::all();
		// $this->data['rowData']	= Ticket::all();
		$current_date	= date('Y-m-d H:i:s');
		// var_dump($current_date);
		$this->data['rowData']= \DB::table('tickets')
		->where('returning', '>', $current_date)
		->orWhere('returning', '=', NULL)
		->get();
		$this->data['roomTypes'] = Roomtypes::all();
		$this->data['airlines'] = Airlines::all();
		$this->data['airports'] = Airports::all();
		// $this->date['tourfeatures'] = Tourfeature::all();
		$this->data['parts'] = Tourfeature::where("packageID", $id)->get();
		$hotelID_for_roomtypes = \DB::table('hotels')
		->get('hotelID');
		$i = 0;
		// var_dump($hotelID_for_roomtypes[3]->hotelID);var_dump($hotelID_for_roomtypes);exit;
		foreach($hotelID_for_roomtypes as $hotelID_for_roomtype){
			$temp[$i] = $hotelID_for_roomtypes[$i]->hotelID;
			$hotel_roomtype[$i] = \DB::table('hotel_rates')
			->select('roomtypeID')
			->where('hotelID', '=', $temp[$i])
			->get();
			$hotel_roomtype_cout[$i] = count($hotel_roomtype[$i]);
			$i++;
		}
		$hotel_Ids_length = count($temp);

		// var_dump($hotel_roomtype);var_dump($temp);var_dump($hotel_Ids_length);var_dump($hotel_Ids_length);
		// var_dump($hotel_roomtype_cout);exit;
		$this->data['hotel_roomtypes'] = $hotel_roomtype;
		$this->data['hotel_IDs'] = $temp;
		$this->data['hotel_Ids_length'] = $hotel_Ids_length;
		$this->data['hotel_roomtype_cout'] = $hotel_roomtype_cout;
		$this->data['hotel_roomtypes_count'] = $i;
		$this->data['id'] = $id;
		return view('packages.form',$this->data);
	}

	public function getShow( Request $request, $id = null)
	{
		// $room_feature = \DB::insert('insert into room_features (roomtypeID, cost,seat,cost_count,packageID) values (1, 2,4,5,7)');
		// exit;
		if($this->access['is_detail'] ==0)
		return Redirect::to('dashboard')
			->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$row = $this->model->getRow($id);
		if($row == NULL){
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
		$total = \DB::table('book_room')
        ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
        ->where('packageID', '=', $id)
        ->where('book_room.status','=',1)
		->count();
		// var_dump($id);var_dump($total);exit;
		$booked_number = \DB::table('book_tour')
        ->where('packageID', '=', $id)
		->count();
		$booking_roomtypes_values = \DB::table('book_room')
		->select('book_room.roomtype')
        ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
        ->where('packageID', '=', $id)
        ->where('book_room.status','=',1)
		->get();
		$all_travellers = \DB::table('book_room')
		->select('book_room.travellers','book_room.roomtype','book_room.bookingID')
        ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
        ->where('packageID', '=', $id)
		->get();
		$all_travellers_count = count($all_travellers);
		if($all_travellers_count > 0){
			$i = 0;
			foreach($all_travellers as $all_traveller){
				$travellerID[$i] = $all_traveller->travellers;
				$roomtypeID[$i] = $all_traveller->roomtype;
				$bookingID[$i] = $all_traveller->bookingID;
				$roomtype[$i] = \DB::table('def_room_types')
				->where('roomtypeID', '=', $roomtypeID[$i])
				->value('room_type');
				$name_surename[$i] = \DB::table('travellers')
				->where('travellerID', '=', $travellerID[$i])
				->value('nameandsurname');
				$bookingno[$i] = \DB::table('bookings')
				->where('bookingsID', '=', $bookingID[$i])
				->value('bookingno');
				$i++;
			}	
		$this->data['booking_IDs'] = $bookingID;
		$this->data['traveller_IDs'] = $travellerID;
		$this->data['bookingno'] = $bookingno;
		$this->data['all_travellers_count'] = $all_travellers_count;
		$this->data['roomtype'] = $roomtype;
		$this->data['name_surename'] = $name_surename;
		}



 
		$i = 0;
		$toureatures_values = \DB::table('tour_features')
		->select('hotelID','total_nights')
        ->where('packageID', '=', $id)
		->get();
 

		$sum = 0;
		$i = 0;
		if($booked_number > 0){
			foreach($booking_roomtypes_values as $booking_roomtypes){
							foreach($toureatures_values as $sub_values){
								$hotelID = $sub_values->hotelID;
								$hotelnights = $sub_values->total_nights;
								$hotel_values = \DB::table('hotel_rates')
								->select('rate', 'currency')
								->where('hotelID', '=', $hotelID)
								->where('roomtypeID', '=', $booking_roomtypes->roomtype)
								->get();
								

							foreach($hotel_values as $hotel_value){
								
								$currency = $hotel_value->currency;
								
								$hotel_rate = $hotel_value->rate;

							}	
								if(CNF_DEF_CURRENCY){
									$currency_value = \DB::table('def_currency')
									->where('currencyID', '=', $currency)
									->value('default_rate');
								}else{
									$currency_value = \DB::table('def_currency')
									->where('currencyID', '=', $currency)
									->value('realtime_rate');
								}
								$room_features = \DB::table('room_features')
								->where('packageID', '=', $id)
								->where('roomtypeID', '=', $booking_roomtypes->roomtype)
								->value('seat');
								$sum += (float)$hotel_rate/(float)$currency_value*(float)$hotelnights/(float)$room_features;
								$i++;
							}
			}

		}
				// var_dump($booking_roomtypes);
				// var_dump($hotel_rate);
				// exit;
		// var_dump($id);var_dump($id);var_dump($hotelnights);var_dump($single_rate);var_dump($booking_roomtype_value);var_dump($single_roomtype);var_dump("++++++++");var_dump($sum);var_dump("++++++++");
		$package_roomtype_id = \DB::table('packages')
		->select('roomtypes')
        ->where('packageID', '=', $id)
		->get();
		$roomteatures_values = \DB::table('room_features')
		->select('roomtypeID')
        ->where('packageID', '=', $id)
		->get();
		$roomfeatures = Roomfeature::where('packageID', $id)->get();
		$currency = Currency::find($row->currencyID);
		$rate = $currency->default_rate;
		if(!CNF_DEF_CURRENCY){
			$rate = $currency->realtime_rate;


		}

		$cost_price = 0.00;
		foreach ($roomfeatures as $roomfeature) {
			$cost_price += $roomfeature->cost * $roomfeature->seat;
		}
		$extra_cost_price = 0.00;
		if(CNF_DEF_CURRENCY){
				$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$id.' WHERE book_tour.packageID = '.$id){0}->sum;
		}
		else {
				$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$id.' WHERE book_tour.packageID = '.$id){0}->sum;
		}
		$cost_price += $extra_cost_price * $rate;
		// $ticketIDs = json_decode($row->flight);
		$ticketIDs = [];
		// $ticketIDstr = "(".implode(", ", $ticketIDs).")";
		// var_dump($ticketIDs);
		// var_dump($ticketIDstr);
		// exit;
		$booking_flight_ticketID = \DB::table('book_flight')
        ->leftJoin('book_tour', 'book_flight.bookingID', '=', 'book_tour.bookingID')
        ->where('packageID', '=', $id)
        ->where('book_flight.status','=',1)
		->get('ticketID');
		foreach ($booking_flight_ticketID as $key => $value) {
		 	array_push($ticketIDs, $value->ticketID);
		}
		// $ticketIDs = implode(",", $ticketIDs);
		// var_dump($ticketIDs);

		// $ticketIDs_last = substr($ticketIDs,-1);

		// if($ticketIDs_last == ","){
		// 	$ticketIDs = substr($ticketIDs,0,-1);
		// }
	
		// $ticketIDstr = "(".$ticketIDs.")";
		// var_dump($ticketIDstr);
		// exit;
		$tickets_cost = 0;
		if(CNF_DEF_CURRENCY){
			foreach ($ticketIDs as $ticketID) {
				$tickets_cost_sum = \DB::select('select sum(tickets.price / default_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
				$tickets_cost += (float)$tickets_cost_sum[0]->sum;
				}
			if($booked_number>0){
				$transfer_cost = \DB::select('select sum(def_vehicle.cost / default_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$id){0}->sum;
			}
			else{
				$transfer_cost = 0;
			}

			// $hotel_cost = \DB::select('select sum(hotel_rates.rate / def_room_types.capacity / default_rate) sum from tour_features left join hotels on tour_features.hotelID = hotels.hotelID left join hotel_rates on hotel_rates.hotelID = hotels.hotelID left join def_currency on def_currency.currencyID = hotel_rates.currency LEFT JOIN def_room_types ON def_room_types.roomtypeID = hotel_rates.roomtypeID where packageID = '.$id){0}->sum;
			$hotel_cost = $sum;
			$expense_cost = \DB::select('select sum(def_extra_expenses.cost / default_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$id){0}->sum;

			$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.default_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$id){0}->sum;
			$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$id.' WHERE book_tour.packageID = '.$id){0}->sum;
			$currencyID = $row->currencyID;
			$capacity = $row->total_capacity;
			// var_dump($tickets_cost[0]->sum);var_dump($total);exit;

			$turnover = ($tickets_cost +$transfer_cost*$booked_number+$hotel_cost+$expense_cost+$visa_cost+$extra_cost)*$rate;
			$earning = $cost_price - $turnover;
		}else{
			foreach ($ticketIDs as $ticketID) {
			$tickets_cost_sum = \DB::select('select sum(tickets.price / realtime_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
			$tickets_cost += (float)$tickets_cost_sum[0]->sum;
			}

			if($booked_number>0){
				$transfer_cost = \DB::select('select sum(def_vehicle.cost / realtime_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$id){0}->sum;
			}
			else{
				$transfer_cost = 0;
			}
			$hotel_cost = $sum; 
			// $hotel_cost = \DB::select('select sum(hotel_rates.rate / def_room_types.capacity / realtime_rate) sum from tour_features left join hotels on tour_features.hotelID = hotels.hotelID left join hotel_rates on hotel_rates.hotelID = hotels.hotelID left join def_currency on def_currency.currencyID = hotel_rates.currency LEFT JOIN def_room_types ON def_room_types.roomtypeID = hotel_rates.roomtypeID where packageID = '.$id){0}->sum;
			$expense_cost = \DB::select('select sum(def_extra_expenses.cost / realtime_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$id){0}->sum;
			$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.realtime_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$id){0}->sum;
			$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$id.' WHERE book_tour.packageID = '.$id){0}->sum;
			$currencyID = $row->currencyID;
			$capacity = $row->total_capacity;
			$turnover = ($tickets_cost + $hotel_cost + ($transfer_cost + $expense_cost + $visa_cost+$extra_cost)*$booked_number)*$rate;
			$earning = $cost_price - $turnover;
			// var_dump("test");
			// var_dump($hotel_cost);
			// var_dump($transfer_cost);
			// var_dump($turnover);
		}
		// $tickets_cost = \DB::select('select sum(tickets.price / realtime_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID in '.$ticketIDstr){0}->sum;

		// var_dump($tickets_cost);
		// var_dump($ticketIDs);
		// var_dump($turnover);
		// var_dump($booked_number);
		// var_dump($total);
		// var_dump($transfer_cost);
		// var_dump($hotel_cost);
		// var_dump($expense_cost);
		// var_dump($visa_cost);
		// var_dump($extra_cost);
		// var_dump($rate);
		// exit;

		if($row)
		{
			$this->data['currency'] = \DB::table('def_currency')
            ->where('currencyID', $currencyID)
			->value('symbol');
			$this->data['rate'] = $rate;
			$this->data['row'] =  $row;
			$this->data['fields'] 		=  \App\Library\SiteHelpers::fieldLang($this->info['config']['grid']);
			$this->data['id'] = $id;
			$this->data['turnover'] = $turnover;
			$this->data['earning'] = $earning;
			$this->data['cost_price'] = $cost_price;
			$this->data['access']		= $this->access;
			$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
			$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
			$this->data['prevnext'] = $this->model->prevNext($id);
			$this->data['packageID'] = $id;

        $bookinglist = \DB::table('bookings')
            ->leftJoin('book_tour', 'bookings.bookingsID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('status','=',1)
			->orderBy('bookingsID','ASC')
			->groupBy('travellerID')
			->get();

		$bookinglist_count = \DB::table('bookings')
            ->leftJoin('book_tour', 'bookings.bookingsID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('status','=',1)
			->orderBy('bookingsID','ASC')
			->groupBy('travellerID')
			->count();
		$expenselist = \DB::table('def_extra_expenses')
		->where([['packageID', '=', $id],['formula', '=', '1']])
		->get();
		$bkList = array();
		$first = 0;
		foreach($bookinglist as $bl)
		{
			$bkList[] = array(
				'travellers'	    =>$bl->travellerID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$eList = array();
		$first = 0;
		foreach($expenselist as $el)
		{
			$eList[] = array(
				'expenseID'	    =>$el->expenseID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$visa_List = array();
		$visaList = \DB::table('visaapplications')
		->where('formula', '=', '1')
		->where('packageID', '=', $id)
		->get();

		$first = 0;
		foreach($visaList as $visal)
		{
			$visa_List[] = array(
				'applicationID'	    =>$visal->applicationID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$this->data['bkList']  = $bkList;
		$this->data['eList']  = $eList;
		$this->data['visa_List']  = $visa_List;
		// var_dump($this->data['visa_List']);exit;

			$totals = $row->total_capacity;
			$this->data['room_triple']          = (int)$totals - (int)$total;
			$this->data['total']                = $total;
			$this->data['packageID'] 						= $id;
			$this->data['roomlists']  = \DB::table('room_features')
			->select('roomtypeID','seat')
			->where('packageID', '=', $id)
			->get();
			$roomlists  = \DB::table('room_features')
			->select('roomtypeID','seat')
			->where('packageID', '=', $id)
			->get();
			// var_dump($roomlists);exit;
			$i =0;
			foreach($roomlists as $roomlist)
			{
				$roomIDs = $roomlist->roomtypeID;
				$roomlist_IDs[$i]  = \DB::table('def_room_types')
				->where('roomtypeID', '=', $roomIDs)
				->value('room_type');
				$i++;
			}
			$this->data['roomcount'] = count($roomlists);
			$this->data['roomlist_IDs'] = $roomlist_IDs;
     if(!is_null($request->input('bookinglist')))
			{
				$html = view('packages.pdfbookinglist', $this->data)->render();
			// var_dump($html);exit;
				$pdf = \App::make('dompdf.wrapper');
				$pdf->loadHTML($html);
				return $pdf->stream();
				
			
				
			}

     if(!is_null($request->input('passportlist')))
			{
				$html = view('packages.pdfpassportlist', $this->data)->render();
				// return \PDF::load($html)->filename('PassportList-'.$id.'.pdf')->show();
				$pdf = \App::make('dompdf.wrapper');
				$pdf->loadHTML($html);
				return $pdf->stream();
			}

     if(!is_null($request->input('emergencylist')))
			{

				$html = view('packages.pdfemergencylist', $this->data)->render();
				$pdf = \App::make('dompdf.wrapper');
				$pdf->loadHTML($html);
				return $pdf->stream();
				// return \PDF::load($html, $size = 'A4', $orientation = 'landscape')->filename('PassportList-'.$id.'.pdf')->show();
			}


            return view('packages.view',$this->data);

		} else {
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
	}

	function postCopy( Request $request)
	{
	    foreach(\DB::select("SHOW COLUMNS FROM packages ") as $column)
        {
			if( $column->Field != 'packageID')
				$columns[] = $column->Field;
        }

		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO packages (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM packages WHERE packageID IN (".$toCopy.")";
			\DB::insert($sql);
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.note_selectrow'))->with('msgstatus','error');
		}

	}
	function image($request)
	{
		return $request->file('tourimage');
	}
	function postSave( Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('tb_packages');
			if(!is_null($request->file('tourimage')))
			{
				$image = $this->image_upload($request);
				$data['packageimage'] = $image;
			}
			if(!is_null($request->file('gallery')))
			{
				$multi_image = $this->multi_image_upload($request);
				$data['gallery'] = $multi_image;
			}
			$data['currencyID'] = $request->input('currencyID');
			$data['flight'] = json_encode($request->input('flight'));
			$data['status'] = $request->input('status');
			$data['package_name'] = $request->input('package_name');
			if($request->input('inclusions') !== NULL){
					$data['inclusions'] = implode(",", $request->input('inclusions'));
			}
			if($request->input('similartours') !== NULL){
					$data['similarpackage'] = implode(",", $request->input('similartours'));
			}
			if($request->input('payment_options') !== NULL){
			$data['payment_options'] = implode(",", $request->input('payment_options'));
			}

			for($i = 0; $i<=4;$i++){
				// $aa = "roomTypes_".$i;
				$roomtype_is_null = $request->input('roomTypes_'.$i);

					$roomTypes[$i] = $roomtype_is_null;

			}
			$data['roomTypes'] = json_encode($roomTypes);
			$roomTypes = json_encode($roomTypes);
			// $id = $this->model->insertRow($roomTypes , $id);
			// $test = json_decode($data['flight']);
			// $ba = json_decode($roomTypes);
			// var_dump($ba[0]);var_dump($test);exit;

			$data['remarks'] = $request->input('remarks');
			$data['policyandterms'] = $request->input('policyandterms');
			$id = $this->model->insertRow($data , $request->input('packageID'));

			$asd = $request->input('packageID');
			// var_dump($id);var_dump($ba);var_dump($roomTypes);exit;
			if($id){

				//---tour features
				$part_count = $request->input('parts');
				$countryIDs = $request->input('countryID');
				$cityIDs = $request->input('cityID');
				// $part_starts = $request->input('part_start');
				// $part_ends = $request->input('part_end');
				$total_nights = $request->input('total_nights');
				$vehicleIDs = $request->input('vehicleID');
				$hotelIDs = $request->input('hotelID');
				$tour_feature_ids = $request->input('tour_feature_id');
				$tourfeature = new Tourfeature();
				for($i=0;$i<$part_count;$i++){

					$tour_feature_id = $tour_feature_ids[$i];
					if($countryIDs[$i] > 0)
						$data_part['countryID'] = $countryIDs[$i];
					else
						break;
					if($cityIDs[$i] > 0)
						$data_part['cityID'] = $cityIDs[$i];
					else
						break;
					// $data_part['part_start'] = $part_starts[$i];
					// $data_part['part_end'] = $part_ends[$i];
					$data_part['total_nights'] = $total_nights[$i];
					if($vehicleIDs[$i] > 0)
						$data_part['vehicleID'] = $vehicleIDs[$i];
					else
						$data_part['vehicleID'] = 0;
					if($hotelIDs[$i] > 0)
						$data_part['hotelID'] = $hotelIDs[$i];
					else
						$data_part['hotelID'] = 0;
					$data_part['packageID'] = $id;
					$tourfeature->insertRow($data_part, $tour_feature_id);
				}
				for($i=$part_count;$i<5;$i++){
					$tour_feature_id = $tour_feature_ids[$i];
					// var_dump($tour_feature_ids[1]);var_dump($tour_feature_ids[2]);var_dump($tour_feature_ids[3]);var_dump($tour_feature_ids[4]);exit;
					if($tour_feature_id > 0 ){
						if($tour_feature_id !== "NULL"){
							$tourfeature->find($tour_feature_id)->delete();
						}

					}

				}

				//---room room_features
				$counter = $request->input('counter');
				$room_typeIDs = $request->input('room_typeID');
				$costs = $request->input('cost');
				$seats = $request->input('seat');
				$room_feature_ids = $request->input('room_feature_id');
				$roomfeature = new Roomfeature();
				for($i=0;$i<$counter;$i++){
					$room_feature_id = $room_feature_ids[$i];
					$data_room_feature['roomtypeID'] = $room_typeIDs[$i];
					$data_room_feature['cost'] = $costs[$i];
					$data_room_feature['seat'] = $seats[$i];
					$data_room_feature['packageID'] = $id;
					$roomfeature->insertRow($data_room_feature, $room_feature_id);
				}
				for($i=$counter;$i<5;$i++){
					$room_feature_id = $room_feature_ids[$i];
					if($room_feature_id > 0)
						$roomfeature->find($room_feature_id)->delete();
				}
			}
			var_dump($part_count);var_dump($countryIDs);var_dump($cityIDs);

			if(!is_null($request->input('apply')))
			{
				$return = 'packages/update/'.$id.'?return='.self::returnUrl();
			} else {
				$return = 'packages?return='.self::returnUrl();
			}
			// Insert logs into database
			if($request->input('packageID') =='')
			{
				\App\Library\SiteHelpers::auditTrail( $request , 'New Data with ID '.$id.' Has been Inserted !');
			} else {
				\App\Library\SiteHelpers::auditTrail($request ,'Data with ID '.$id.' Has been Updated !');
			}
			return Redirect::to($return)->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {
			return Redirect::to('packages/update/'. $request->input('packageID'))->with('messagetext',\Lang::get('core.note_error'))->with('msgstatus','error')
			->withErrors($validator)->withInput();
		}
	}

	public function postDelete( Request $request)
	{

		if($this->access['is_remove'] ==0)
			return Redirect::to('dashboard')
				->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');
		// delete multipe rows
		if(count($request->input('ids')) >=1)
		{
			$this->model->destroy($request->input('ids'));

			\App\Library\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfully");
			// redirect
			return Redirect::to('packages')
        		->with('messagetext', \Lang::get('core.note_success_delete'))->with('msgstatus','success');

		} else {
			return Redirect::to('packages')
        		->with('messagetext',\Lang::get('core.note_noitemdeleted'))->with('msgstatus','error');
		}

	}

	public static function display( )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Packages();
		$info = $model::makeInfo('packages');

		$data = array(
			'pageTitle'	=> 	$info['title'],
			'pageNote'	=>  $info['note']

		);

		if($mode == 'view')
		{
			$id = $_GET['view'];
			$row = $model::getRow($id);
			if($row)
			{
				$data['row'] =  $row;
				$data['fields'] 		=  \App\Library\SiteHelpers::fieldLang($info['config']['grid']);
				$data['id'] = $id;
				return view('packages.public.view',$data);
			}

		} else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> 'packageID' ,
				'order'		=> 'asc',
				'params'	=> '',
				'global'	=> 1
			);

			$result = $model::getRows( $params );
			$data['tableGrid'] 	= $info['config']['grid'];
			$data['rowData'] 	= $result['rows'];

			$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
			$pagination = new Paginator($result['rows'], $result['total'], $params['limit']);
			$pagination->setPath('');
			$data['i']			= ($page * $params['limit'])- $params['limit'];
			$data['pagination'] = $pagination;
			return view('packages.public.index',$data);
		}
	}

	function postSavepublic(Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('packages');
			 $this->model->insertRow($data , $request->input('packageID'));
			return  Redirect::back()->with('messagetext','<p class="alert alert-success">'.\Lang::get('core.note_success').'</p>')->with('msgstatus','success');
		} else {

			return  Redirect::back()->with('messagetext','<p class="alert alert-danger">'.\Lang::get('core.note_error').'</p>')->with('msgstatus','error')
			->withErrors($validator)->withInput();

		}
	}

  static public function travelersDetail( $traveler = '', $packageID)
	{
		$travelersDetail='';
		$travellerID_bytra='';
		if($traveler !='')
		{
			$nameandsurname = \DB::table('travellers')
			->where('travellerID',$traveler)->value('nameandsurname');
			$InvTotal = \DB::table('invoice')
				->where('travellerID', '=', $traveler)
				->value('InvTotal');
			$currency_id = \DB::table('invoice')
				->where('travellerID', '=', $traveler)
				->value('currency');
			$currency = \DB::table('def_currency')
				->where('currencyID', '=', $currency_id)
				->value('currency_sym');
			$currency_sym = \DB::table('def_currency')
			->where('currencyID', '=', $currency_id)
			->value('symbol');
			$invoice_total = \DB::table('invoice')
			->where('travellerID', '=', $traveler)
			->get();
			$InvID = \DB::table('invoice')
			->where('travellerID', '=', $traveler)
			->value('invoiceID');
			$booking_IDs = \DB::table('invoice_products')
					->select('bookingID')
					->get();
					$i=0;
					$InvID_bytra="";
					foreach($booking_IDs as $bookingIDs){
						$booking_ID[$i] = $bookingIDs->bookingID;
						$tranvellerID[$i] = \DB::table('bookings')
						->where('bookingsID', '=', $booking_ID[$i])
						->value('travellerID');
						if($traveler == $tranvellerID[$i]){
							$InvID_bytra = \DB::table('invoice_products')
							->where('bookingID', '=', $booking_ID[$i])
							->value('InvID');
							$travellerID_bytra = \DB::table('invoice')
							->where('invoiceID', '=', $InvID_bytra)
							->value('travellerID');
							$InvoiceID = \DB::table('invoice')
							->where('travellerID', '=', $traveler)
							->value('invoiceID');
						

						}
						$i++;
					}
					
			// $payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');
			// var_dump($InvID_bytra);exit;
			$package = Package::find($packageID);
			$currency = Currency::find($package->currencyID);
			$traveller_bytra  = $travellerID_bytra;
			if($traveller_bytra == $traveler){
				$traveller_bytra = 0;
				$InvID = "";
			}else{
				$InvID = $InvID_bytra;
			}
			if(CNF_DEF_CURRENCY)
				$payment = \DB::select('select sum(invoice_payments.amount / default_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum * $currency->default_rate;
			else
				$payment = \DB::select('select sum(invoice_payments.amount / realtime_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum * $currency->realtime_rate;

			if(CNF_DEF_CURRENCY){var_dump("test");
				$InvTotal = \DB::select('select sum(invoice.InvTotal / default_rate) sum from invoice left join def_currency on invoice.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum * $currency->default_rate;
			}else{
				$InvTotal = \DB::select('select sum(invoice.InvTotal / realtime_rate) sum from invoice left join def_currency on invoice.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum * $currency->realtime_rate;
			}
				
			$unpaid_sum = (float)$InvTotal - (float)$payment;
// var_dump($InvTotal);exit;
			$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
			$payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
			if($traveller_bytra > 0 ){
				$travelername = \DB::table('travellers')
				->where('travellerID',$traveller_bytra)->value('nameandsurname');
				$travelersDetail .= "<div class='col-md-3'><a href='".url('travellers/show')."/".$traveler."'>".$nameandsurname."</a></div><div class='col-md-3'></div><div class='col-md-3'></div><div class='col-md-3'>".InvoiceStatus::package_payments($payment, $InvTotal, $traveller_bytra, $InvID, $travelername)."</div>";

			}else{
				$travelername = "";
				$travelersDetail .= "<div class='col-md-3'><a href='".url('travellers/show')."/".$traveler."'>".$nameandsurname."</a></div><div class='col-md-3'>".number_format((float)$payment, 2)." ".$currency->symbol."</div><div class='col-md-3'>".number_format((float)$unpaid_sum, 2)." ".$currency->symbol."</div><div class='col-md-3'>".InvoiceStatus::package_payments($payment, $InvTotal, $traveller_bytra, $InvID, $travelername )."</div>";

			}
		}
		return $travelersDetail;
	}

	static public function travelersDetail_visa($id, $traveler = '')
	{
		$visaDetail = '';
			$visas = \DB::table('visaapplications')
			->where('applicationID',$id)->get();
			foreach($visas as $visa){
				$travellers = VisaapplicationController::visaApplicants($visa->travellersID);
				$applicationdate = SiteHelpers::TarihFormat($visa->applicationdate);
				$status = GeneralStatuss::Visa($visa->status);
				$processtime = $visa->processintime;

			$visaDetail .= "<div class='col-md-3'>".$travellers."</div><div class='col-md-3'>".date("d-m-Y", strtotime($applicationdate))."</div>
			<div class='col-md-3'>".$processtime."  Days</div>
			<div class='col-md-3'>".$status."</div>";
			}
		return $visaDetail;
	}

	static public function travelersDetail_expense($id, $traveler = '')
	{
		$expenseDetail = '';
			$expense = \DB::table('def_extra_expenses')
			->where('expenseID',$id)->get();
			foreach($expense as $ex){
				$expenseID = $ex->expenseID;
				$extra_expenses = $ex->extra_expenses;
				$cost = $ex->cost;
				$currencyID = $ex->currencyID;
				$status = $ex->status;
				$formula = $ex->formula;
				$tourcategoriesID = $ex->tourcategoriesID;
				$packageID = $ex->packageID;
				$data = $ex->data;
				$remarks = $ex->remarks;
				$paymenttypeID = $ex->paymenttypeID;
				$attached = $ex->attached;
				$staff = $ex->staff;

				$staff = \DB::table('staffs')
				->where('staffID',$staff)->value('name');
				$def_payment_types = \DB::table('def_payment_types')
				->where('paymenttypeID',$paymenttypeID)->value('payment_type');
				$def_currency = \DB::table('def_currency')
				->where('currencyID',$currencyID)->value('symbol');
				$category = '';
				if($formula == '1'){
					$category = "Package";
				}else{
					$category = "Simple";
				}
			$expenseDetail .= "<div class='col-md-2'>".$staff."</div><div class='col-md-1'>".$def_currency." ".number_format((float)$cost, 2)."</div><div class='col-md-1'>".$def_payment_types."</div>
			<div class='col-md-2'>".date("d-m-Y", strtotime($data))."</div><div class='col-md-2'>".$extra_expenses."</div><div class='col-md-1'>".$category."</div>
			<div class='col-md-2'>".$remarks."</div><div class='col-md-1'><a href='".asset('storage')."/files/".$attached."' class='text-red' target='_blank'><i class='fa fa-file-pdf-o fa-2x'></i></a></div>";
			}
		return $expenseDetail;
	}
	static public function travelersDetail_paid( $traveler = '', $packageID = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{
					$nameandsurname = \DB::table('travellers')
					->where('travellerID',$traveler)->value('nameandsurname');
					$InvTotal = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('InvTotal');
					$currency_id = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('currency');
					$currency = \DB::table('def_currency')
						->where('currencyID', '=', $currency_id)
						->value('currency_sym');
					$currency_sym = \DB::table('def_currency')
					->where('currencyID', '=', $currency_id)
					->value('symbol');
					$invoice_total = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->get();
					$InvID = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->value('invoiceID');
					$payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');

					$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
					$payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
					if(CNF_DEF_CURRENCY){
						$InvTotal = \DB::select('select sum(invoice.InvTotal / default_rate) sum from invoice left join def_currency on invoice.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;
						$payments = \DB::select('select sum(invoice_payments.amount / default_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;

					}else{
						$InvTotal = \DB::select('select sum(invoice.InvTotal / realtime_rate) sum from invoice left join def_currency on invoice.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;
						$payments = \DB::select('select sum(invoice_payments.amount / realtime_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;

					}
					
					// $payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
					$travelersDetail = (float)$InvTotal - (float)$payments;
		}
		return $travelersDetail;
	}

	static public function travelersDetail_unpaid( $traveler = '', $packageID = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{

					$nameandsurname = \DB::table('travellers')
					->where('travellerID',$traveler)->value('nameandsurname');
					$InvTotal = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('InvTotal');
					$currency_id = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('currency');
					$currency = \DB::table('def_currency')
						->where('currencyID', '=', $currency_id)
						->value('currency_sym');
					$currency_sym = \DB::table('def_currency')
					->where('currencyID', '=', $currency_id)
					->value('symbol');
					$invoice_total = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->get();
					$InvID = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->value('invoiceID');
					$payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');

					$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
					// $payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
					if(CNF_DEF_CURRENCY)
						$payments = \DB::select('select sum(invoice_payments.amount / default_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;
					else
						$payments = \DB::select('select sum(invoice_payments.amount / realtime_rate) sum from invoice_payments left join def_currency on invoice_payments.currency = def_currency.currencyID where travellerID = '.$traveler){0}->sum;
					$travelersDetail =$payments;
					
		}
		return $travelersDetail;
	}


  static public function travelersDetailpdf( $traveler = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{
			$sqltrv = \DB::table('travellers')->whereIn('travellerID',explode(',',$traveler))->get();

            foreach ($sqltrv as $v2) {

				$travelersDetail .= "<tr><td style='border:0px;'> ".$v2->nameandsurname."</td><td style='width:5%;'> ".SiteHelpers::formatLookUp($v2->countryID,'countryID','1:def_country:countryID:country_code')."</td></tr>";
			}
		}
		return $travelersDetail;
	}

  static public function travelersDetailpassport( $travelerpass = '')
	{
		$travelersDetailpassport='';
		if($travelerpass !='')
		{
			$sqltrvpass = \DB::table('travellers')->whereIn('travellerID',explode(',',$travelerpass))->get();

            foreach ($sqltrvpass as $v3) {

				$travelersDetailpassport .= "<tr>
                <td style='width:20%'> ".$v3->nameandsurname."</td>
                <td style='width:15%'> ".$v3->passportno."</td>
                <td style='width:20%'> ".SiteHelpers::formatLookUp($v3->passportcountry,'countryID','1:def_country:countryID:country_name')."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->dateofbirth)."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->passportissue)."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->passportexpiry)."</td>
                </tr>";
			}
		}
		return $travelersDetailpassport;
	}


  static public function travelersDetailemergency( $traveleremr = '')
	{
		$travelersDetailemergency='';
		if($traveleremr !='')
		{
			$sqltrvemr = \DB::table('travellers')->whereIn('travellerID',explode(',',$traveleremr))->get();

            foreach ($sqltrvemr as $v4) {

				$travelersDetailemergency .= "<tr>
        <td> ".$v4->nameandsurname."</td>
        <td> ".$v4->emergencycontactname."</td>
        <td> ".$v4->emergencycontactemail."</td>
        <td> ".$v4->emergencycontanphone."</td>
        <td> ".$v4->insurancecompany."</td>
        <td> ".$v4->insurancepolicyno."</td>
        <td> ".$v4->insurancecompanyphone."</td>
        <td> ".$v4->bedconfiguration."</td>
        <td> ".$v4->dietaryrequirements."</td>
        </tr>";
			}
		}
		return $travelersDetailemergency;
	}



}
