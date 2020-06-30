<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\Airlines;
use App\Models\Ticket;
use App\Models\Package;
use App\Models\Airports;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;

class TicketsController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'tickets';
	static $per_page	= '10';

	public function __construct()
	{
		parent::__construct();
		$this->model = new Ticket();
    $this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
		$this->access['is_detail'] = 0;
    $this->data = array(
        'pageTitle'			=> 	$this->info['title'],
		'pageNote'			=>  $this->info['note'],
        'pageModule'		=> 'tickets',
        'pageUrl'			=>  url('tickets'),
        'return' 			=> 	self::returnUrl()
    );
	}


	public function getIndex()
	{

		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
    $this->data['access']		= $this->access;
    $this->data['items'] = Ticket::all();
    $this->data['airlines'] = Airlines::all();
    $this->data['airports'] = Airports::all();

    $this->data['tableGrid'] 	= $this->info['config']['grid'];
    $this->data['tableForm'] 	= $this->info['config']['forms'];
    $this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
    // Group users permission
    $this->data['access']		= $this->access;
    // Detail from master if any
    $this->data['setting'] 		= $this->info['setting'];
 
    // Master detail link if any
    $this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
		return view('tickets.index',$this->data);
	}


	public  function getUpdate(Request $request, $id = null){

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
			$this->data['row'] 		=  $row;
		} else {
			$this->data['row'] 		= $this->model->getColumnTable('tickets');
		}
		$this->data['setting'] 		= $this->info['setting'];
		$this->data['fields'] 		=  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);

		$this->data['id'] = $id;
		$this->data['airlines'] = Airlines::all();
		$this->data['airports'] = Airports::all();
		return view('tickets.form',$this->data);
  }
  function update(Request $request,$id){
    $ticket = Ticket::where('ticketID',$id)->first();
    $request['airlinesID'] = json_encode($request->airlinesID);
    $ticket->update($request->all());
    return response()->json(['status'=>'success','message'=>__('core.note_success')]);
  }
	function change_availableSeats(Request $request, $id){
		$ticket = Ticket::where('ticketID',$id)->first();
		$update_data['available_seats'] = $request->available_seats;
		$ticket->update($update_data);
		return response()->json(['status'=>'success','message'=>__('core.note_success')]);
	}
	public function postData( Request $request)
	{
		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
		$order = (!is_null($request->input('order')) ? $request->input('order') : $this->info['setting']['ordertype']);
		// End Filter sort and order for query
		// Filter Search for query
		$filter = '';
		$where = '';
		// if(!is_null($request->input('search')) && $request->input('search') != "")
		// {
		// 	$search = 	$request->input('search');
		// 	$searched_airlines = Airlines::select('airlineID')->where('airline', 'like', '%'.$search.'%')->get();
		// 	$searched_airports = Airports::select('airportID')->where('airport_name', 'like', '%'.$search.'%')->get();
		// 	$where = "arrFlightNO like '%$search%' or depFlightNO like '%$search%' or departing like '%$search%' or returning like '%$search%' or class like '%$search%'";
		// 	$airelinewhere = "";
		// 	$aireportwhere = "";
		// 	foreach ($searched_airlines as $airline) {
		// 		if($airelinewhere != "")
		// 			$airelinewhere .= " or ";
		// 		$airelinewhere .="airlinesID like '%\"$airline->airlineID\"%'";
		// 	}
		// 	foreach ($searched_airports as $airport) {
		// 		if($aireportwhere != "")
		// 			$aireportwhere .= " or ";
		// 		$aireportwhere .="depairportID = $airport->airportID or arrairportID = $airport->airportID";
		// 	}
		// 	if($airelinewhere != "")
		// 		$where .= "or ($airelinewhere)";
		// 	if($aireportwhere != "")
		// 		$where .= "or ($aireportwhere)";
		// }
		// $page = $request->input('page', 1);
		// $params = array(
		// 	'page'		=> $page,
		// 	'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : $this->info['setting']['perpage'] ) ,
		// 	'sort'		=> $sort,
		// 	'order'		=> $order,
		// 	'params'	=> $filter,
		// 	'where'  	=> $where,
		// 	'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		// );
		// Get Query
		// $results = $this->model->getRows( $params );

		// Build pagination setting
		// $page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
		// $pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
		// $pagination->setPath('tickets/data');
		// $this->data['rowData']	= Ticket::all();
		$this->data['current_date']	= date('Y-m-d H:i:s');
		$current_date	= date('Y-m-d H:i:s');
		// var_dump($current_date);
		$this->data['rowData']= \DB::table('tickets')
		->where('returning', '>', $current_date)
		->orWhere('returning', '=', NULL)
		->get();
		$this->data['airlines'] = Airlines::all();
		$this->data['airports'] = Airports::all();
		// Build Pagination
		// $this->data['pagination']	= $pagination;
		// Build pager number and append current param GET
		// $this->data['pager'] 		= $this->injectPaginate();
		// Row grid Number
		// $this->data['i']			= ($page * $params['limit'])- $params['limit'];
		// Grid Configuration
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
		// Group users permission
		$this->data['access']		= $this->access;
		// Detail from master if any
		$this->data['setting'] 		= $this->info['setting'];

		// Master detail link if any
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
		// Render into template
		return view('tickets.table',$this->data);

	}

	function store(Request $request){
	    $request->validate([
	        'airlinesID'=>'required',
	        'returnn'=>'required',
	        'depairportID'=>'required',
	        'arrairportID'=>'required',
	        'departing'=>'required',
        ]);
      $request['airlinesID'] = json_encode($request->airlinesID);
	    Ticket::create($request->all());
	    return response()->json(['status'=>'success','message'=>__('core.note_success')]);
    }

		public function postDelete( Request $request)
		{

			if($this->access['is_remove'] ==0) {
				return response()->json(array(
					'status'=>'error',
					'message'=> \Lang::get('core.note_restric')
				));
				die;

			}
			// delete multipe rows
			if(count($request->input('ids')) >=1)
			{
				$this->model->destroy($request->input('ids'));

				return response()->json(array(
					'status'=>'success',
					'message'=> \Lang::get('core.note_success_delete')
				));
			} else {
				return response()->json(array(
					'status'=>'error',
					'message'=> \Lang::get('core.note_error')
				));

			}

		}

		function postSave( Request $request, $id =0)
		{

			$rules = $this->validateForm();
			$validator = Validator::make($request->all(), $rules);
			if ($validator->passes()) {
				$data = $this->validatePost('def_tickets');
				$data['returnn'] = $request->input('returnn');
				$data['price'] = $request->input('price');
				$data['currencyID'] = $request->input('currencyID');
				$id = $this->model->insertRow($data , $request->input('ticketID'));

				return response()->json(array(
					'status'=>'success',
					'message'=> \Lang::get('core.note_success')
					));

			} else {

				$message = $this->validateListError(  $validator->getMessageBag()->toArray() );
				return response()->json(array(
					'message'	=> $message,
					'status'	=> 'error'
				));
			}

		}

		public static function display( )
		{
			$mode  = isset($_GET['view']) ? 'view' : 'default' ;
			$model  = new Ticket();
			$info = $model::makeInfo('tickets');

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
					return view('airlines.public.view',$data);
				}

			} else {

				$page = isset($_GET['page']) ? $_GET['page'] : 1;
				$params = array(
					'page'		=> $page ,
					'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
					'sort'		=> 'airlineID' ,
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
				return view('airlines.public.index',$data);
			}
		}

		function postCopy( Request $request)
		{

		    foreach(\DB::select("SHOW COLUMNS FROM tickets ") as $column)
	      {
						if( $column->Field != 'ticketID')
							$columns[] = $column->Field;
	      }
				if(count($request->input('ids')) >=1)
				{

					$toCopy = implode(",",$request->input('ids'));


					$sql = "INSERT INTO tickets (".implode(",", $columns).") ";
					$sql .= " SELECT ".implode(",", $columns)." FROM tickets WHERE ticketID IN (".$toCopy.")";
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
}
