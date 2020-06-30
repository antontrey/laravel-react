<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Teams;
use App\Models\Teamtypes;
use App\Models\Guide;
use App\Models\Guidelanguages;
use App\Models\Cities;
use App\Models\Bookgroup;
use App\Models\Currency;
use App\Models\Accounting;
use App\Models\Countries;
use App\Models\Booktour;
use App\Models\Bookroom;
use App\Models\Package;
use App\Models\Bookflight;
use App\Models\Ticket;
use App\Models\Bookhotel;
use App\Models\Tours;
use App\Models\Roomfeature;
use App\Models\Bookings;
use App\Models\Tourfeature;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;

class AccountingController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'accounting';
	static $per_page	= '10';

	public function __construct()
	{
		parent::__construct();
		$this->model = new Accounting();
		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
		$this->info['setting']['form-method'] = "native";
		// $this->access['is_detail'] = 0;
		$this->data = array(
			'pageTitle'			=> 	$this->info['title'],
			'pageNote'			=>  $this->info['note'],
			'pageModule'		=> 'accounting',
			'pageUrl'			=>  url('accounting'),
			'return' 			=> 	self::returnUrl()
		);

	}

	public function getIndex()
	{
		$this->data['accounting'] = 'accounting';
		// $this->data['page_singlename'] = 'accounting';
		$this->access['is_clone'] ==0;
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		$this->data['access']		= $this->access;
		// $this->data['is_clone']		= 0;
		return view('accounting.index',$this->data);
	}
	public function export(Request $request,  $t = 'pdf')
	{
		$ids = $request->input('ids_hidden');
		$t = $request->input('format_hidden');
		$submit = $request->input('submit_value');
		$invoice_payment_total = $request->input('invoice_payment_total');
		$expensestotal = $request->input('expensestotal');
		$Box2_sum = $request->input('Box2_sum');
		$grandtotal = $request->input('grandtotal');
		$info 		= $this->model->makeInfo( $this->module);
		$filter = '';
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		}
		$params 	= array(
					'params'	=> $filter ,
					'fstart'	=> $request->input('fstart'),
					'flimit'	=> $request->input('flimit')
		);
		$results 	= $this->model->getRows( $params );
		$fields		= $info['config']['grid'];
		$rows		= $results['rows']; 
		if($this->data['pageTitle'] == "Accounting"){
			if($ids){
				$ids_array = explode(',', $ids);
				$rows = \DB::table('accounting')->whereIn('accountingID', $ids_array)->get();
			}


			$accounting_count = 0;
			foreach($rows as $row){
				$date[$accounting_count] = date("d-m-Y", strtotime($row->date));
				$rows[$accounting_count]->date = $date[$accounting_count];
				$accounting_count++;
			}
		}
		$content 	= array(
						'fields' => $fields,
						'rows' => $rows,
						'submit' => $submit,
						'invoice_payment_total' => $invoice_payment_total,
						'expensestotal' => $expensestotal,
						'Box2_sum' => $Box2_sum,
						'grandtotal' => $grandtotal,
						'title' => $this->data['pageTitle'],
					);
		if($t == 'word')
		{
			 return view('mmb.module.utility.word',$content);

		} else if($t == 'pdf') {
			 $html = view('mmb.module.utility.pdf', $content)->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            return $pdf->stream();
        } else if($t == 'csv') {
			return view('mmb.module.utility.csv',$content);
		} else if ($t == 'print') {
			
			$data['html'] = view('mmb.module.utility.print', $content)->render();
			return view('layouts.blank',$data);
		} else  {
			 return view('mmb.module.utility.excel',$content);
		}
	}

	function tickets_of_package(Request $request){
		$packageID = $request->input('packageID');
		$Ticket = $request->input('airlineID');
		if(!$packageID) return "[]";
		// return $packageID;
		$packageID = Package::find($packageID);
		$ticketIDs = json_decode( $packageID->flight);
		if(count($ticketIDs)==0) return "[]";
		// $tickets = \DB::table('tickets')->join('def_airlines', 'tickets.airlinesID', '=', 'def_airlines.airlineID')->whereIn('ticketID', $ticketIDs)->get();
		$tickets = \DB::table('tickets')->join('def_airlines', 'tickets.airlinesID', '=', 'def_airlines.airlineID')->where('airlineID', $Ticket)->whereIn('ticketID', $ticketIDs)->get();
		return $tickets->toJson();
	}
	function airlines_of_package(Request $request){
		$packageID = $request->input('packageID');
		if(!$packageID) return "[]";
		// return $packageID;
		$package = Package::find($packageID);
		$ticketIDs = json_decode( $package->flight);
		if(count($ticketIDs)==0) return "[]";
		// $tickets = \DB::table('tickets')->join('def_airlines', 'tickets.airlinesID', '=', 'def_airlines.airlineID')->whereIn('ticketID', $ticketIDs)->get();
		$tickets = \DB::table('tickets')->join('def_airlines', 'tickets.airlinesID', '=', 'def_airlines.airlineID')->whereIn('ticketID', $ticketIDs)->groupBy('airlineID')->get();
		return $tickets->toJson();
	}
	public function postData( Request $request)
	{
		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
		$order = (!is_null($request->input('order')) ? $request->input('order') : $this->info['setting']['ordertype']);
		// End Filter sort and order for query
		$this->DataTable();

		// get start date and end date
		$start_date = (!is_null($request->input('start_date')) ? $request->input('start_date') : "");
		$end_date = (!is_null($request->input('end_date')) ? $request->input('end_date') : "");
		// Filter Search for query
		$filter = '';

		$this->data['accounting'] = 'accounting';
		if($end_date !== "" && $start_date !== ""){
		$this->data['start_date']		= $start_date;
		$this->data['end_date']		= $end_date;
			$sql = "SELECT accounting.* FROM accounting WHERE accounting.date BETWEEN '".$start_date."' AND '".$end_date."'";
			$this->data['rowData'] = DB::select($sql);
			$search = "1";
		}else{
			$this->data['rowData'] = DB::select('select accounting.* from accounting');
			$this->data['start_date']		= "";
			$search = "0";
			$this->data['end_date']		= ""; 
		}
             //    var_dump($this->data['rowData']); exit;
		$this->data['types']		= Teamtypes::all();
		$this->data['guides']		= Guide::all();
		// Grid Configuration
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$temp = array();
		for($i=0;$i<count($this->data['tableGrid']);$i++){
			array_push($temp, $this->data['tableGrid'][$i]);
			if($this->data['tableGrid'][$i]['field'] == 'guides'){
				$field = $this->data['tableGrid'][6];
				$field['view'] = '1';
				$field['field'] = 'travellers';
				$field['label'] = 'Travellers';
				array_push($temp, $field);
			}
		}
		if($search == "1"){
			$InvTotals = \DB::table('invoice')
			->whereBetween('DateIssued', [$start_date, $end_date])
			->get('InvTotal');
			$expenses = \DB::table('def_expenses')
			->whereBetween('expense_date', [$start_date, $end_date])
			->get('amount');
			$invoice_payments = \DB::table('invoice_payments')
			->whereBetween('payment_date', [$start_date, $end_date])
			->get();
		}else{
			$InvTotals = \DB::table('invoice')
			->get('InvTotal');
			$expenses = \DB::table('def_expenses')
			->get('amount');
			$invoice_payments = \DB::table('invoice_payments')
			->get();
		}

		$expensestotal = 0;
		$subexpenses = 0;
		foreach($expenses as $expense)
		{
			$subexpenses = $expense->amount;
			$expensestotal +=(float)($subexpenses);

		}

		$grandtotal = 0;
		$subtotal = 0;
		foreach($InvTotals as $InvTotal_value)
		{
			$subtotal = $InvTotal_value->InvTotal;
			$grandtotal +=(float)($subtotal);

		}

		$invoice_payment_total = 0;
		$invoice_payment_subtotal = 0;
		foreach($invoice_payments as $invoice_payment)
		{
			$invoice_payment_subtotal = $invoice_payment->amount;
			$invoice_payment_subtotal_currency = $invoice_payment->currency;
			if(CNF_DEF_CURRENCY){
				$currency_value_payments = \DB::table('def_currency')
				->where('currencyID', '=', $invoice_payment_subtotal_currency)
				->value('default_rate');
			}else{
				$currency_value_payments = \DB::table('def_currency')
				->where('currencyID', '=', $invoice_payment_subtotal_currency)
				->value('realtime_rate');
			}
			$invoice_payment_total +=(float)($invoice_payment_subtotal)/(float)$currency_value_payments;

		}
		$this->data['grandtotal'] = $grandtotal;
		$this->data['invoice_payment_total'] = $invoice_payment_total;
		$this->data['expensestotal'] = $expensestotal;
		$this->data['tableGrid'] = $temp;







			$packageIDs = \DB::table('packages')
			->get();

// var_dump($packageIDs);exit;
		$v = 0;
		$turnover = 0;
		$hotel_costtest = 0;
		foreach($packageIDs as $packageID_value)
		{
			$single_packageID[$v] = $packageID_value->packageID;


			if($search == "1"){
				$booking_roomtypes_values = \DB::table('book_room')
				->select('book_room.roomtype')
				->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
				->where('packageID', '=', $single_packageID[$v])
				->where('book_room.status','=',1)
				->whereBetween('book_tour.created_at', [$start_date, $end_date])
				->get();
				$booked_number = \DB::table('book_tour')
				->where('packageID', '=', $single_packageID[$v])
				->whereBetween('created_at', [$start_date, $end_date])
				->count();
				$booking_flight_ticketID = \DB::table('book_flight')
				->leftJoin('book_tour', 'book_flight.bookingID', '=', 'book_tour.bookingID')
				->where('packageID', '=', $single_packageID[$v])
				->whereBetween('book_tour.created_at', [$start_date, $end_date])
				->where('book_flight.status','=',1)
				->get('ticketID');
			}else{
				$booking_roomtypes_values = \DB::table('book_room')
				->select('book_room.roomtype')
				->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
				->where('packageID', '=', $single_packageID[$v])
				->where('book_room.status','=',1)
				->get();
				$booked_number = \DB::table('book_tour')
				->where('packageID', '=', $single_packageID[$v])
				->count();
				$booking_flight_ticketID = \DB::table('book_flight')
				->leftJoin('book_tour', 'book_flight.bookingID', '=', 'book_tour.bookingID')
				->where('packageID', '=', $single_packageID[$v])
				->where('book_flight.status','=',1)
				->get('ticketID');
			}

			$toureatures_values = \DB::table('tour_features')
			->select('hotelID','total_nights')
			->where('packageID', '=', $single_packageID[$v])
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
										->where('packageID', '=', $single_packageID[$v])
										->where('roomtypeID', '=', $booking_roomtypes->roomtype)
										->value('seat');
										$sum += (float)$hotel_rate/(float)$currency_value*(float)$hotelnights/(float)$room_features;
									
									
										$i++;
								}

				}
			
			}
			$package_roomtype_id = \DB::table('packages')
			->select('roomtypes')
			->where('packageID', '=', $single_packageID[$v])
			->get();
			$roomteatures_values = \DB::table('room_features')
			->select('roomtypeID')
			->where('packageID', '=', $single_packageID[$v])
			->get();
			$roomfeatures = Roomfeature::where('packageID', $single_packageID[$v])->get();
			$currency = Currency::find($packageID_value->currencyID);
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
					$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
			}
			else {
					$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
			}
			$cost_price += $extra_cost_price * $rate;
			$ticketIDs = [];

			foreach ($booking_flight_ticketID as $key => $value) {
				 array_push($ticketIDs, $value->ticketID);
			}
			// var_dump($ticketIDs);exit;
			$tickets_cost = 0;
			if(CNF_DEF_CURRENCY){
				foreach ($ticketIDs as $ticketID) {
					$tickets_cost_sum = \DB::select('select sum(tickets.price / default_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
					$tickets_cost += (float)$tickets_cost_sum[0]->sum;

					}
				if($booked_number>0){
					$transfer_cost = \DB::select('select sum(def_vehicle.cost / default_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				}
				else{
					$transfer_cost = 0;
				}
				// $hotel_cost = \DB::select('select sum(hotel_rates.rate / def_room_types.capacity / default_rate) sum from tour_features left join hotels on tour_features.hotelID = hotels.hotelID left join hotel_rates on hotel_rates.hotelID = hotels.hotelID left join def_currency on def_currency.currencyID = hotel_rates.currency LEFT JOIN def_room_types ON def_room_types.roomtypeID = hotel_rates.roomtypeID where packageID = '.$single_packageID[$v]){0}->sum;
				$hotel_cost = $sum;
				$expense_cost = \DB::select('select sum(def_extra_expenses.cost / default_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.default_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
				$currencyID = $packageID_value->currencyID;
				$capacity = $packageID_value->total_capacity;
				// var_dump($tickets_cost[0]->sum);var_dump($total);exit;
				$turnover = $turnover + ($tickets_cost +$transfer_cost*$booked_number+$hotel_cost+$expense_cost+$visa_cost+$extra_cost)*$rate;
				$earning = $cost_price - $turnover;
			}else{
				foreach ($ticketIDs as $ticketID) {
					$tickets_cost_sum = \DB::select('select sum(tickets.price / realtime_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
					$tickets_cost += (float)$tickets_cost_sum[0]->sum;

				}
	
				if($booked_number>0){
					$transfer_cost = \DB::select('select sum(def_vehicle.cost / realtime_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				}
				else{
					$transfer_cost = 0;
				}

				$hotel_cost = $sum; 
				$expense_cost = \DB::select('select sum(def_extra_expenses.cost / realtime_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.realtime_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
				$currencyID = $packageID_value->currencyID;
				$capacity = $packageID_value->total_capacity;
				$turnover = $turnover + ($tickets_cost + $hotel_cost + ($transfer_cost + $expense_cost + $visa_cost+$extra_cost)*$booked_number)*$rate;
				$earning = $cost_price - $turnover;
			}
			$hotel_costtest = $hotel_costtest + $hotel_cost;
			$v++;
		}
		$this->data['turnover'] = $turnover;
		$this->data['currency'] = \DB::table('def_currency')
			 ->where('currencyID', $currencyID)
			 ->value('symbol');

		if($search == "1"){
			$bookingIDs = \DB::table('book_tour')
			->where('formula', '=', 0)
			->where('status','=',1)
			->whereBetween('created_at', [$start_date, $end_date])
			->get('bookingID');
		}else{
			$bookingIDs = \DB::table('book_tour')
			->where('formula', '=', 0)
			->where('status','=',1)
			->get('bookingID');
		}
		$bookingIDs = \DB::table('book_tour')
		->where('formula', '=', 0)
		->where('status','=',1)
		->get('bookingID');
		$t = 0;
		$hotel_rates_sum = 0;
		$t = 0;
		$total_ticket_price = 0;
		$total_extra = 0;
		foreach($bookingIDs as $bookingIDs_value)
		{
						$single_bookingID[$t] = $bookingIDs_value->bookingID;
						$book_hotelID = \DB::table('book_hotel')
						->where('bookingID', '=', $single_bookingID[$t])
						->where('status','=',1)
						->value('hotelID');
						$book_roomtypeID = \DB::table('book_room')
						->where('bookingID', '=', $single_bookingID[$t])
						->where('status','=',1)
						->value('roomtype');
						if($book_hotelID && $book_roomtypeID)
						{
							$hotel_rates = \DB::table('hotel_rates')
							->where('hotelID', '=', $book_hotelID)
							->where('roomtypeID', '=', $book_roomtypeID)
							->value('rate');
							$currencyID = \DB::table('hotel_rates')
							->where('hotelID', '=', $book_hotelID)
							->where('roomtypeID', '=', $book_roomtypeID)
							->value('currency');
							$currency = Currency::find($currencyID);
							$rate = $currency->default_rate;
							if(!CNF_DEF_CURRENCY){
								$rate = $currency->realtime_rate;
							}
							$hotel_rates_sum = $hotel_rates_sum + $hotel_rates/$rate;

						}
						
						
			$extraserviceIDs = \DB::table('book_extra')
			->where('status','=',1)
			->where('bookingID','=',$single_bookingID[$t])
			->get('extraserviceID');
			foreach($extraserviceIDs as $extraserviceIDs_value)
			{

				$extraserviceID = $extraserviceIDs_value->extraserviceID;
				$extraservice_value = \DB::table('def_extra_services')
				->where('status','=',1)
				->where('extraserviceID','=',$extraserviceID)
				->value('buy');
				$currencyID = \DB::table('def_extra_services')
				->where('status','=',1)
				->where('extraserviceID','=',$extraserviceID)
				->value('currencyID_buy');
				$currency = Currency::find($currencyID);
				$rate = $currency->default_rate;
				if(!CNF_DEF_CURRENCY){
					$rate = $currency->realtime_rate;
				}
				$total_extra = $total_extra + $extraservice_value/$rate;
			}



										$ticketIDsforaccount = \DB::table('book_flight')
										->where('bookingID','=',$single_bookingID[$t])
										->where('status','=',1)
										->get('ticketID');
										
										foreach($ticketIDsforaccount as $ticketIDforaccount_value)
										{
											$ticketIDforaccount = $ticketIDforaccount_value->ticketID;
											$priceforaccount = \DB::table('tickets')
											->where('status','=',1)
											->where('ticketID','=',$ticketIDforaccount)
											->value('price');
											$currencyID_ticket = \DB::table('tickets')
											->where('status','=',1)
											->where('ticketID','=',$ticketIDforaccount)
											->value('currencyID');
											$currency_ticket = Currency::find($currencyID_ticket);
											$rate = $currency_ticket->default_rate;
											if(!CNF_DEF_CURRENCY){
												$rate = $currency_ticket->realtime_rate;
											}
											$total_ticket_price = $total_ticket_price + $priceforaccount/$rate;

										}

			$t++;
		}
		
		$tour_sum = $hotel_rates_sum + $total_extra + $total_ticket_price;
		$this->data['tour_sum'] = $tour_sum;
		$this->data['Box2_sum'] = (float)$tour_sum + (float)$turnover;
		$this->data['Box2_sum'] = (float)$turnover;







































		// var_dump($this->data['tableGrid']); exit;
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
		// Group users permission
		$this->data['access']		= $this->access;

		// Detail from master if any
		$this->data['setting'] 		= $this->info['setting'];
		// Master detail link if any
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
        // Render into template
        // $this->data['page_singlename'] = 'accounting';
		return view('accounting.table',$this->data);

	}
	function DataTable()
	{
		$transfer_packageID = 0;

		//all the value of this table should be removed.
		DB::table('accounting')->delete();
		//Package amount adding on Accounting Table
		$packageIDs = \DB::table('packages')
		->get();
		$v = 0;
		foreach($packageIDs as $packageID_value)
		{
			$single_packageID[$v] = $packageID_value->packageID;
			$total = \DB::table('book_room')
			->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
			->where('packageID', '=', $single_packageID[$v])
			->where('book_room.status','=',1)
			->count();
			$booked_number = \DB::table('book_tour')
			->where('packageID', '=', $single_packageID[$v])
			->count();
			$booking_roomtypes_values = \DB::table('book_room')
			->select('book_room.roomtype')
			->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
			->where('packageID', '=', $single_packageID[$v])
			->where('book_room.status','=',1)
			->get();
			$all_travellers = \DB::table('book_room')
			->select('book_room.travellers','book_room.roomtype','book_room.bookingID')
			->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
			->where('packageID', '=', $single_packageID[$v])
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
			}

			$i = 0;
			$toureatures_values = \DB::table('tour_features')
			->select('hotelID','total_nights')
			->where('packageID', '=', $single_packageID[$v])
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
									->where('packageID', '=', $single_packageID[$v])
									->where('roomtypeID', '=', $booking_roomtypes->roomtype)
									->value('seat');
									$sum += (float)$hotel_rate/(float)$currency_value*(float)$hotelnights/(float)$room_features;
									$i++;
								}
				}
	
			}
			$package_roomtype_id = \DB::table('packages')
			->select('roomtypes')
			->where('packageID', '=', $single_packageID[$v])
			->get();
			$roomteatures_values = \DB::table('room_features')
			->select('roomtypeID')
			->where('packageID', '=', $single_packageID[$v])
			->get();
			$roomfeatures = Roomfeature::where('packageID', $single_packageID[$v])->get();
			$currency = Currency::find($packageID_value->currencyID);
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
					$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
			}
			else {
					$extra_cost_price = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = def_extra_services.currencyID LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
			}
			$cost_price += $extra_cost_price * $rate;
			$ticketIDs = [];
			$booking_flight_ticketID = \DB::table('book_flight')
			->leftJoin('book_tour', 'book_flight.bookingID', '=', 'book_tour.bookingID')
			->where('packageID', '=', $single_packageID[$v])
			->where('book_flight.status','=',1)
			->get('ticketID');
			foreach ($booking_flight_ticketID as $key => $value) {
				 array_push($ticketIDs, $value->ticketID);
			}
			// var_dump($ticketIDs);exit;
			$tickets_cost = 0;
			if(CNF_DEF_CURRENCY){
				foreach ($ticketIDs as $ticketID) {
					$tickets_cost_sum = \DB::select('select sum(tickets.price / default_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
					$tickets_cost += (float)$tickets_cost_sum[0]->sum;
					}
				if($booked_number>0){
					$transfer_cost = \DB::select('select sum(def_vehicle.cost / default_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				}
				else{
					$transfer_cost = 0;
				}
				// $hotel_cost = \DB::select('select sum(hotel_rates.rate / def_room_types.capacity / default_rate) sum from tour_features left join hotels on tour_features.hotelID = hotels.hotelID left join hotel_rates on hotel_rates.hotelID = hotels.hotelID left join def_currency on def_currency.currencyID = hotel_rates.currency LEFT JOIN def_room_types ON def_room_types.roomtypeID = hotel_rates.roomtypeID where packageID = '.$single_packageID[$v]){0}->sum;
				$hotel_cost = $sum;
				$expense_cost = \DB::select('select sum(def_extra_expenses.cost / default_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.default_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / default_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
				$currencyID = $packageID_value->currencyID;
				$capacity = $packageID_value->total_capacity;
				// var_dump($tickets_cost[0]->sum);var_dump($total);exit;
				$turnover = ($tickets_cost +$hotel_cost+$expense_cost+$visa_cost+$extra_cost)*$rate + $transfer_cost*$rate;
				$earning = $cost_price - $turnover;
			}else{
				foreach ($ticketIDs as $ticketID) {
				$tickets_cost_sum = \DB::select('select sum(tickets.price / realtime_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$ticketID);
				$tickets_cost += (float)$tickets_cost_sum[0]->sum;
				}
	
				if($booked_number>0){
					$transfer_cost = \DB::select('select sum(def_vehicle.cost / realtime_rate / def_vehicle.capacity) sum from tour_features left join def_vehicle on tour_features.vehicleID = def_vehicle.vehicleID left join def_currency on def_vehicle.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				}
				else{
					$transfer_cost = 0;
				}
				$hotel_cost = $sum; 
				$expense_cost = \DB::select('select sum(def_extra_expenses.cost / realtime_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.realtime_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
				$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy LEFT JOIN book_tour ON book_tour.bookingID = book_extra.bookingID AND book_tour.packageID = '.$single_packageID[$v].' WHERE book_tour.packageID = '.$single_packageID[$v]){0}->sum;
				$currencyID = $packageID_value->currencyID;
				$capacity = $packageID_value->total_capacity;
				$turnover = ($tickets_cost + $hotel_cost + $expense_cost + $visa_cost+$extra_cost)*$rate + $transfer_cost*$rate;
				$earning = $cost_price - $turnover;
			}
			if($turnover > 0){
				$date_createds = \DB::table('book_tour')
				->where('packageID', '=', $single_packageID[$v])
				->where('status','=',1)
				->get();
				$package_count = \DB::table('book_tour')
				->where('packageID', '=', $single_packageID[$v])
				->where('status','=',1)
				->count();
				if(count($date_createds) > 0)
				{
					
						foreach( $date_createds as $date_created)
						{
							$created_date = $date_created->created_at;
							$created_bookingID = $date_created->bookingID;
							$tickets_cost_ID = \DB::table('book_flight')
							->where('bookingID', '=', $created_bookingID)
							->where('status','=',1)
							->value('ticketID');
							$roomtype_ID = \DB::table('book_room')
							->where('bookingID', '=', $created_bookingID)
							->where('status','=',1)
							->value('roomtype');
							$toureatures_values = \DB::table('tour_features')
							->select('hotelID','total_nights')
							->where('packageID', '=', $single_packageID[$v])
							->get();
							$sum = 0;
						
							$hotel_cost = 0;
							foreach($toureatures_values as $sub_values){
									$hotelID = $sub_values->hotelID;
									$hotelnights = $sub_values->total_nights;
									$hotel_values = \DB::table('hotel_rates')
									->select('rate', 'currency')
									->where('hotelID', '=', $hotelID)
									->where('roomtypeID', '=', $roomtype_ID)
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
										->where('packageID', '=', $single_packageID[$v])
										->where('roomtypeID', '=', $roomtype_ID)
										->value('seat');
										$hotel_cost += (float)$hotel_rate/(float)$currency_value*(float)$hotelnights/(float)$room_features;
									
							}
							$currencyID = $packageID_value->currencyID;
							if(CNF_DEF_CURRENCY){
								$tickets_cost_sum = \DB::select('select sum(tickets.price / default_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$tickets_cost_ID);
								$tickets_cost = (float)$tickets_cost_sum[0]->sum;

								$expense_cost = \DB::select('select sum(def_extra_expenses.cost / default_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
								$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.default_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
								$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy WHERE book_extra.bookingID = '.$created_bookingID){0}->sum;

							}else{
								$tickets_cost_sum = \DB::select('select sum(tickets.price / realtime_rate) sum from tickets left join def_currency on tickets.currencyID = def_currency.currencyID where TicketID = '.$tickets_cost_ID);
								$tickets_cost = (float)$tickets_cost_sum[0]->sum;
								$expense_cost = \DB::select('select sum(def_extra_expenses.cost / realtime_rate) sum from def_extra_expenses left join def_currency on def_extra_expenses.currencyID = def_currency.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
								$visa_cost = \DB::select('select sum(visaapplications.applicationfee / currencys2.realtime_rate) sum from visaapplications left join def_currency currencys1 on visaapplications.currencyID = currencys1.currencyID left join def_currency currencys2 on visaapplications.currencyID2 = currencys2.currencyID where packageID = '.$single_packageID[$v]){0}->sum;
								$extra_cost = \DB::select('SELECT SUM(def_extra_services.buy / realtime_rate) sum FROM book_extra LEFT JOIN def_extra_services ON book_extra.extraserviceID = def_extra_services.extraserviceID LEFT JOIN def_currency ON def_currency.currencyID = currencyID_buy WHERE book_extra.bookingID = '.$created_bookingID){0}->sum;

							}
							$currency_symbol = \DB::table('def_currency')
							->where('currencyID', $currencyID)
							->value('symbol');
							$turnover = ((float)$tickets_cost + (float)$hotel_cost + (float)$expense_cost + (float)$visa_cost+(float)$extra_cost)*(float)$rate + $transfer_cost*(float)$rate;

							if($transfer_packageID !== $single_packageID[$v]){
								//transfer 
								if($transfer_cost > 0){
										$Accounting = new Accounting;
										$Accounting->date = $created_date;
										$Accounting->price = number_format((float)$transfer_cost, 2);
										$Accounting->currency = $currency_symbol;
										$Accounting->type = "OUT";
										$Accounting->category = "Transfer";
										$Accounting->save();
										$transfer_packageID = $single_packageID[$v];
								}
								//expense_cost
								if($expense_cost > 0){
									$Accounting = new Accounting;
									$Accounting->date = $created_date;
									$Accounting->price = number_format((float)$expense_cost, 2);
									$Accounting->currency = $currency_symbol;
									$Accounting->type = "OUT";
									$Accounting->category = "Expense";
									$Accounting->save();
								}
								//visa_cost
								if($visa_cost > 0){
									$Accounting = new Accounting;
									$Accounting->date = $created_date;
									$Accounting->price = number_format((float)$visa_cost, 2);
									$Accounting->currency = $currency_symbol;
									$Accounting->type = "OUT";
									$Accounting->category = "Visa";
									$Accounting->save();
								}
							}
							//Extra
							if($extra_cost > 0){
								$Accounting = new Accounting;
								$Accounting->date = $created_date;
								$Accounting->price = number_format((float)$extra_cost, 2);
								$Accounting->currency = $currency_symbol;
								$Accounting->type = "OUT";
								$Accounting->category = "Extra";
								$Accounting->save();
							}
							//ticket
								if($tickets_cost > 0){
									$Accounting = new Accounting;
									$Accounting->date = $created_date;
									$Accounting->price = number_format((float)$tickets_cost, 2);
									$Accounting->currency = $currency_symbol;
									$Accounting->type = "OUT";
									$Accounting->category = "Ticket";
									$Accounting->save();
								}	

							//hotel 
							if($hotel_cost > 0){
								$Accounting = new Accounting;
								$Accounting->date = $created_date;
								$Accounting->price = number_format((float)$hotel_cost, 2);
								$Accounting->currency = $currency_symbol;
								$Accounting->type = "OUT";
								$Accounting->category = "Hotel";
								$Accounting->save();
							}
							// package
							$Accounting = new Accounting;
							$Accounting->date = $created_date;
							$Accounting->price = number_format((float)$turnover, 2);
							$Accounting->currency = $currency_symbol;
							$Accounting->type = "OUT";
							$Accounting->category = "Package";
							$Accounting->save();
						}
				}
			}
			$v++;
		}
		// exit;
		//Tour amount adding on table.
		$bookingIDs = \DB::table('book_tour')
		->where('formula', '=', 0)
		->where('status','=',1)
		->get('bookingID');
		$t = 0;
		$hotel_rates_sum = 0;
		$t = 0;
		$total_ticket_price = 0;
		$total_extra = 0;
		foreach($bookingIDs as $bookingIDs_value)
		{
						$single_bookingID[$t] = $bookingIDs_value->bookingID;
						$book_hotelID = \DB::table('book_hotel')
						->where('bookingID', '=', $single_bookingID[$t])
						->where('status','=',1)
						->value('hotelID');
						$book_roomtypeID = \DB::table('book_room')
						->where('bookingID', '=', $single_bookingID[$t])
						->where('status','=',1)
						->value('roomtype');
						if($book_hotelID && $book_roomtypeID)
						{
							$hotel_rates = \DB::table('hotel_rates')
							->where('hotelID', '=', $book_hotelID)
							->where('roomtypeID', '=', $book_roomtypeID)
							->value('rate');
							$currencyID = \DB::table('hotel_rates')
							->where('hotelID', '=', $book_hotelID)
							->where('roomtypeID', '=', $book_roomtypeID)
							->value('currency');
							$currency = Currency::find($currencyID);
							$rate = $currency->default_rate;
							if(!CNF_DEF_CURRENCY){
								$rate = $currency->realtime_rate;
							}
							$hotel_rates_sum = $hotel_rates_sum + $hotel_rates/$rate;

						}else{
							$hotel_rates_sum = 0;
						}
				
						
			$extraserviceIDs = \DB::table('book_extra')
			->where('status','=',1)
			->where('bookingID','=',$single_bookingID[$t])
			->get('extraserviceID');
			if(count($extraserviceIDs) > 0){
				foreach($extraserviceIDs as $extraserviceIDs_value)
				{

					$extraserviceID = $extraserviceIDs_value->extraserviceID;
					$extraservice_value = \DB::table('def_extra_services')
					->where('status','=',1)
					->where('extraserviceID','=',$extraserviceID)
					->value('buy');
					$currencyID = \DB::table('def_extra_services')
					->where('status','=',1)
					->where('extraserviceID','=',$extraserviceID)
					->value('currencyID_buy');
					$currency = Currency::find($currencyID);
					$rate = $currency->default_rate;
					if(!CNF_DEF_CURRENCY){
						$rate = $currency->realtime_rate;
					}
					$total_extra = $total_extra + $extraservice_value/$rate;
				}
			}else{
				$total_extra = 0;
			}




										$ticketIDsforaccount = \DB::table('book_flight')
										->where('bookingID','=',$single_bookingID[$t])
										->where('status','=',1)
										->get('ticketID');
										if(count($ticketIDsforaccount) > 0){
											foreach($ticketIDsforaccount as $ticketIDforaccount_value)
											{
												$ticketIDforaccount = $ticketIDforaccount_value->ticketID;
												$priceforaccount = \DB::table('tickets')
												->where('status','=',1)
												->where('ticketID','=',$ticketIDforaccount)
												->value('price');
												$currencyID_ticket = \DB::table('tickets')
												->where('status','=',1)
												->where('ticketID','=',$ticketIDforaccount)
												->value('currencyID');
												$currency_ticket = Currency::find($currencyID_ticket);
												$rate = $currency_ticket->default_rate;
												if(!CNF_DEF_CURRENCY){
													$rate = $currency_ticket->realtime_rate;
												}
												$total_ticket_price = $total_ticket_price + $priceforaccount/$rate;

											}
										}else{
											$total_ticket_price = 0;
										}


		// var_dump($total_extra);
		// var_dump($single_bookingID[$t]);
		$tour_singlesum = $hotel_rates_sum + $total_extra + $total_ticket_price;
		if($tour_singlesum > 0){
			$date_created_tour = \DB::table('book_tour')
			->where('bookingID', '=', $single_bookingID[$t])
			->value('created_at');

			$Accounting = new Accounting;
			$Accounting->date = $date_created_tour;
			$Accounting->price = number_format((float)$tour_singlesum, 2);
			$Accounting->currency = $currency_symbol;
			$Accounting->type = "OUT";
			$Accounting->category = "Tour";
			$Accounting->save();
		}

		$t++;								

		}


		//Invoice payments adding on Accounting table.
		$invoice_payments = \DB::table('invoice_payments')
		->get();
		foreach($invoice_payments as $invoice_payment)
		{
			$invoice_payment_subtotal = $invoice_payment->amount;
			$invoice_payment_date = $invoice_payment->payment_date;
			$invoiceID_subtotal = $invoice_payment->invoiceID;
			$invoiceID_created_at = $invoice_payment->created_at;
			$invoiceID_currency = $invoice_payment->currency;
			$invoice_bookingID = \DB::table('invoice_products')
			->where('InvID', '=', $invoiceID_subtotal)
			->value('bookingID');
			$formula_value = \DB::table('book_tour')
			->where('bookingID', '=', $invoice_bookingID)
			->value('formula');
			if($formula_value == "0"){
				$category = "Tour";
			}else{
				$category = "Package";
			}

			$currency_ticket_invoiceID = Currency::find($invoiceID_currency);
			$Accounting = new Accounting;
			$Accounting->date = $invoice_payment_date;
			$Accounting->price = number_format((float)$invoice_payment_subtotal, 2);
			$Accounting->currency = $currency_ticket_invoiceID->symbol;
			$Accounting->type = "IN";
			$Accounting->category = $category;
			$Accounting->save();


		}

			// exit;














	}

    function postCopy( Request $request)
	{

	    foreach(\DB::select("SHOW COLUMNS FROM accounting ") as $column)
        {
			if( $column->Field != 'id')
				$columns[] = $column->Field;
        }
		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO accounting (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM accounting WHERE ID IN (".$toCopy.")";
			\DB::insert($sql);
			return response()->json(array(
				'status'=>'success',
				'message'=> \Lang::get('core.note_success')
			));

		} else {
			return response()->json(array(
				'status'=>'success',
				'message'=> \Lang::get('core.note_selectrow')
			));
		}


    }
    


    function getUpdate(Request $request, $id = null)
	{
exit;
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
		// $bookingID = $request->get('bookingID');
		$travellerID = \DB::table('bookings')->select('travellerID')->get();
		$travellerID = $travellerID[0]->travellerID;
		$row = Booktour::find($id);
		if($row)
		{
			$this->data['row'] 		=  $row;
		} else {
			$this->data['row'] 		= $this->model->getColumnTable('book_tour');
		}
		$this->data['setting'] 		= $this->info['setting'];
		$this->data['fields'] 		=  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);

		// $this->data['packages'] = Package::all();
		$this->data['packages'] = \DB::select('SELECT t.*
			FROM `packages` t
			WHERE t.`packageID` NOT IN (SELECT a.`packageID`
			FROM `packages` a
			JOIN `book_tour` b ON b.`packageID` = a.`packageID`
			JOIN `bookings` c ON c.`bookingsID` = b.`bookingID` AND c.`travellerID` = '.$travellerID.')');
		// $this->data['packages'] = \DB::table('packages')->where("packageID not in (select a.packageID from packages a join book_tour b on a.packageID = b.packageID JOIN bookings c ON c.bookingsID = b.bookingID AND c.travellerID = 2 JOIN bookings c ON c.bookingsID = b.bookingID AND c.travellerID = 2)")->select('packages.*')->get();
		$this->data['travellerID'] = $travellerID;
		$this->data['tours'] = Tours::all();
		$this->data['id'] = $id;
		$da = $this->data['tours'];
		return view('accounting.form',$this->data);
	}

}
