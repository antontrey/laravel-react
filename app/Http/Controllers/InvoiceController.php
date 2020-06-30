<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\Invoice;
use App\Models\Bookings;
use App\Models\Booktour;
use App\Models\Package;
use App\Models\Roomfeature;
use App\Models\Tours;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\App;
use Validator, Input, Redirect ;

class InvoiceController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'invoice';
	static $per_page	= '100000';

	public function __construct()
	{

		$this->model = new Invoice();

		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);

		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'invoice',
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

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : 'invoiceID');
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


		$page = $request->input('page', 1);
		$params = array(
			'page'		=> $page ,
			'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : static::$per_page ) ,
			'sort'		=> $sort ,
			'order'		=> $order,
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		// Get Query
		$results = $this->model->getRows( $params );

		// Build pagination setting
		$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
		$pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
		$pagination->setPath('invoice');

		$this->data['rowData']		= $results['rows'];
		// Build Pagination
		$this->data['pagination']	= $pagination;
		// Build pager number and append current param GET
		$this->data['pager'] 		= $this->injectPaginate();
		// Row grid Number
		$this->data['i']			= ($page * $params['limit'])- $params['limit'];
		// Grid Configuration
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
		// Group users permission
		$this->data['access']		= $this->access;
		// Detail from master if any
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
		// Master detail link if any
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
		// Render into template
		return view('invoice.index',$this->data);
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
		$travellerID = \DB::table('invoice')->where('invoiceID', $id)->value('travellerID');


		// var_dump($column_value);exit;

		// $bookeds = \DB::table('bookings')->select( 'bookingno', 'travellerID', 'created_at', 'bookingsID')->where('travellerID', $travellerID)->get();
		// $bookeds_length = count($bookeds);

		$bookings = \DB::table('bookings')->select( 'bookingno', 'travellerID', 'created_at', 'bookingsID')->get();
		$bookings_length = count($bookings);		
		$travellerIDs = \DB::table('travellers')
		->get('travellerID');
		$i = 0;
		foreach($travellerIDs as $travellerID){
			$temp[$i] = $travellerIDs[$i]->travellerID;
			$traveller = $temp[$i]; 
			$bookeds[$traveller] = \DB::table('bookings')
			->select('bookingsID')
			->where('travellerID', '=', $temp[$i])
			->get();
			$bookeds[$traveller] = \DB::table('bookings')
			->select('bookings.bookingsID')
			->leftJoin('invoice_products', 'bookings.bookingsID', '!=', 'invoice_products.bookingID')
			->groupBy('bookings.bookingsID')
			->get();
			$bookeds_cout[$i] = count($bookeds[$traveller]);
			$tranveller_bookeds_cout[$traveller] = count($bookeds[$traveller]);
			$i++;
		}


		$travellerIDs_length = count($temp);
		// var_dump($bookeds_cout[0]);
		// var_dump($bookeds[9][0]);
		// exit;

		$this->data['bookeds_cout'] =  $bookeds_cout;
		$this->data['tranveller_bookeds_cout'] =  $tranveller_bookeds_cout;
		$this->data['traveller_IDs'] =  $temp;
		$this->data['bookings'] =  $bookings;
		$this->data['travellerIDs_length'] =  $travellerIDs_length;
		// $this->data['bookeds_length'] =  $bookeds_length;
		$this->data['bookeds'] =  $bookeds;
		$this->data['bookings_length'] =  $bookings_length;
		if($row)
		{
			$this->data['row'] =  $row; 
		} else { 
			$this->data['row'] = $this->model->getColumnTable('invoice');
		}
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);

        $this->data['items'] = \DB::table('invoice_products')->where('InvID', $this->data['row']['invoiceID'])->get();
// var_dump($this->data['items']);exit;
		$this->data['id'] = $id;
		return view('invoice.form',$this->data);
	}

	public function getShow( Request $request, $id = null)
	{

		if($this->access['is_detail'] ==0)
		return Redirect::to('dashboard')
			->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$row = $this->model->getRow($id);
		if($row)
		{
			$this->data['row'] =  $row;
			$this->data['fields'] 		=  \App\Library\SiteHelpers::fieldLang($this->info['config']['grid']);
			$this->data['id'] = $id;
			$this->data['access']		= $this->access;
			$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
			$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
            $this->data['items']		= \DB::table('invoice_products')->where('InvID', $id)->get();
			$this->data['prevnext'] = $this->model->prevNext($id);

             if(!is_null($request->input('pdf')))
			{
				$html = view('invoice.pdf', $this->data)->render();
				// return \PDF::loadHtml($html)->save('Invoice-'.$id.'.pdf')->output();
				$pdf = App::make('dompdf.wrapper');
				$pdf->loadHTML($html);
				return $pdf->stream();
			}

			return view('invoice.view',$this->data);
		} else {
			return Redirect::to('invoice')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
	}

	function postCopy( Request $request)
	{
	    foreach(\DB::select("SHOW COLUMNS FROM invoice ") as $column)
        {
			if( $column->Field != 'invoiceID')
				$columns[] = $column->Field;
        }

		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO invoice (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM invoice WHERE invoiceID IN (".$toCopy.")";
			\DB::insert($sql);
			return Redirect::to('invoice')->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {

			return Redirect::to('invoice')->with('messagetext',\Lang::get('core.note_selectrow'))->with('msgstatus','error');
		}

	}
	public function getCreate( Request $request, $id = null)
	{
		// $row = $this->model->getRow($id);
		$data = \DB::table('quotation')->where('quotationID', $id)->get();
		$quotationid = $id;
		// $data = (array)$data;
		$data_array = array(
		'travellerID' => $data[0]->travellerID,
		// 'quotationID' => $data[0]->quotationID,
		'bookingID' => $data[0]->bookingID,
		'InvTotal' => $data[0]->InvTotal,
		'Subtotal' => $data[0]->Subtotal,
		'currency' => $data[0]->currency,
		'payment_type' => $data[0]->payment_type,
		'notes' => $data[0]->notes,
		'DateIssued' => $data[0]->DateIssued,
		'DueDate' => $data[0]->untildate,
		'discount' => $data[0]->discount,
		'tax' => $data[0]->tax,
	);
	$data_prod = \DB::table('quotation_products')->where('InvID', $id)->get();
	// var_dump($data_prod[0]->Items);exit;
	$id = $this->model->insertRow($data_array , $request->input('invoiceID'));
	if(isset($data_prod))
	{

		\DB::table('invoice_products')->where('InvID', $quotationid)->delete();
		// $Items = $data_prod[0]->Items ;

			$dataItems = array(
				'Code' 	    => $data_prod[0]->Code,
				'Items' 	=> $data_prod[0]->Items,
				'Qty' 		=> $data_prod[0]->Qty,
				'Amount' 	=> $data_prod[0]->Amount,
				'InvID'		=> $id
			);

			\DB::table('invoice_products')->insert($dataItems);

	}
	// var_dump($id);exit;
	\DB::table('quotation')->where('quotationID', $quotationid)->delete();
	$return = 'invoice';
	return Redirect::to($return)->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
	}
	function postSave( Request $request)
	{
		
		
		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('tb_invoice');
			$hidden_bookingID = $request->input('hidden_bookingID');
			$invoiceID = $request->input('invoiceID');
			$hidden_bookingID = $request->input('hidden_bookingID');
			$bookingID_product = $data['bookingID'];
			$data['bookingID'] = implode(",",$data['bookingID']);
			$id = $this->model->insertRow($data , $request->input('invoiceID'));
					
			// Subt Item Save
			if(isset($_POST['Items']))
			{

				\DB::table('invoice_products')->where('InvID', $id)->delete();
				$Items = $_POST['Items'] ;
				for($i=0; $i < count($Items); $i++)
				{
					$dataItems = array(
						'Code' 	    => $_POST['Code'][$i],
						'Items' 	=> $_POST['Items'][$i],
						'Qty' 		=> $_POST['Qty'][$i],
						'Amount' 	=> $_POST['Amount'][$i],
						'bookingID' 	=> $bookingID_product[$i],
						'InvID'		=> $id
					);

					\DB::table('invoice_products')->insert($dataItems);
				}
			}


			if(!is_null($request->input('apply')))
			{
				$return = 'invoice/update/'.$id.'?return='.self::returnUrl();
			} else {
				$return = 'invoice?return='.self::returnUrl();
			}

			// Insert logs into database
			if($request->input('invoiceID') =='')
			{
				\App\Library\SiteHelpers::auditTrail( $request , 'New Data with ID '.$id.' Has been Inserted !');
			} else {
				\App\Library\SiteHelpers::auditTrail($request ,'Data with ID '.$id.' Has been Updated !');
			}



			return Redirect::to($return)->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');

		} else {
			

			return Redirect::to('invoice/update/'. $request->input('invoiceID'))->with('messagetext',\Lang::get('core.note_error'))->with('msgstatus','error')
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
            \DB::table('invoice_products')->whereIn('InvID', $request->input('ids'))->delete();


			\App\Library\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfully");
			// redirect
			return Redirect::to('invoice')
        		->with('messagetext', \Lang::get('core.note_success_delete'))->with('msgstatus','success');

		} else {
			return Redirect::to('invoice')
        		->with('messagetext',\Lang::get('core.note_noitemdeleted'))->with('msgstatus','error');
		}

	}

	public static function display( )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Invoice();
		$info = $model::makeInfo('invoice');

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
				return view('invoice.public.view',$data);
			}

		} else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> 'invoiceID' ,
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
			return view('invoice.public.index',$data);
		}


	}

	function postSavepublic( Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('invoice');
			 $this->model->insertRow($data , $request->input('invoiceID'));
			return  Redirect::back()->with('messagetext','<p class="alert alert-success">'.\Lang::get('core.note_success').'</p>')->with('msgstatus','success');
		} else {

			return  Redirect::back()->with('messagetext','<p class="alert alert-danger">'.\Lang::get('core.note_error').'</p>')->with('msgstatus','error')
			->withErrors($validator)->withInput();

		}

	}

	function productfrom_bookingnsID(Request $request){

		$bookingsID = $request->input("bookingsID");
		$singleID = $request->input("singleID");
		// var_dump($singleID);exit;
		$booking = Bookings::find($bookingsID);
		$product = array();
		$product['productcode'] = $booking->bookingno;
		$booktour = Booktour::where("bookingID", $bookingsID)->first();
		$bookroomtypes = \DB::table('book_room')
			->select('roomtype')
            ->where('bookingID', '=', $bookingsID)
			->get();
		if($booktour!=null){
			if($booktour->formula==1){
				$package = Package::find($booktour->packageID);
				$product['itemname'] = $package->tour_code;
				$product['amount'] = 0;
				foreach ($bookroomtypes as $bookroomtype) {
					$roomfeatures = Roomfeature::where('packageID', $booktour->packageID)->where('roomtypeID', $bookroomtype->roomtype)->get();
					

					foreach ($roomfeatures as $roomfeature) {
						$product['amount'] += $roomfeature->cost;
					}
				}
				$product['currencyID'] = $package->currencyID;
			}
			else{
				$tour = Tours::find($booktour->tourID);
				$product['itemname'] = $tour->tour_name;
				$product['amount'] = $tour->cost;
				$product['currencyID'] = $tour->currencyID;
			}
		} 
		return response()->json(array(
			'status'=>'success',
			'product'=> $product,
			'test'=> $bookroomtypes,
			'singleID'=> $singleID,
			'bookingsID'=> $bookingsID,
		));
	}

	function product_from_bookingnsID(Request $request){

		$bookingsID = $request->input("bookingsID");
		$singleID = $request->input("singleID");

		$booking = Bookings::find($bookingsID);
		$product = array();
		$product['productcode'] = $booking->bookingno;
		$booktour = Booktour::where("bookingID", $bookingsID)->first();
		$bookroomtypes = \DB::table('book_room')
			->select('roomtype')
            ->where('bookingID', '=', $bookingsID)
			->get();
		if($booktour!=null){
			if($booktour->formula==1){
				$package = Package::find($booktour->packageID);
				$product['itemname'] = $package->tour_code;
				$product['amount'] = 0;
				foreach ($bookroomtypes as $bookroomtype) {
					$roomfeatures = Roomfeature::where('packageID', $booktour->packageID)->where('roomtypeID', $bookroomtype->roomtype)->get();
					

					foreach ($roomfeatures as $roomfeature) {
						$product['amount'] += $roomfeature->cost;
					}
				}
				$product['currencyID'] = $package->currencyID;
			}
			else{
				$tour = Tours::find($booktour->tourID);
				$product['itemname'] = $tour->tour_name;
				$product['amount'] = $tour->cost;
				$product['currencyID'] = $tour->currencyID;
			}
		}
		return response()->json(array(
			'status'=>'success',
			'product'=> $product,
			'test'=> $bookroomtypes,
			'singleID'=> $singleID,

		));
	}
}
