<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use Illuminate\Support\Facades\DB;
use App\Models\Teams;
use App\Models\Teamtypes;
use App\Models\Guide;
use App\Models\Guidelanguages;
use App\Models\Cities;
use App\Models\Countries;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;

class TeamsController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'teams';
	static $per_page	= '10';

	public function __construct()
	{
		parent::__construct();
		$this->model = new Teams();

		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
		$this->info['setting']['form-method'] = "native";
		$this->access['is_detail'] = 0;
		$this->data = array(
			'pageTitle'			=> 	$this->info['title'],
			'pageNote'			=>  $this->info['note'],
			'pageModule'		=> 'teams',
			'pageUrl'			=>  url('teams'),
			'return' 			=> 	self::returnUrl()
		);

	}

	public function getIndex()
	{
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');

		$this->data['access']		= $this->access;
		return view('teams.index',$this->data);
	}

	public function postData( Request $request)
	{
		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
		$order = (!is_null($request->input('order')) ? $request->input('order') : $this->info['setting']['ordertype']);
		// End Filter sort and order for query
		// Filter Search for query
		$filter = '';
		$this->data['rowData']		= DB::select('select teams.*,
																						if (counts.cnt>0, counts.cnt, 0) travellers
																						from teams
																						left JOIN (
																						    SELECT teams.id, COUNT(bookingsID) cnt
																						    FROM `bookings`
																						    RIGHT JOIN `teams` on teams.id = bookings.team
																						    ORDER BY teams.id) counts
																						on counts.id = teams.id');
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
		$this->data['tableGrid'] = $temp;

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
		return view('teams.table',$this->data);

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
			$this->data['row'] 		=  $row;
		} else {
			$this->data['row'] 		= $this->model->getColumnTable('teams');
		}
		$this->data['guides']		= Guide::all();
		$this->data['cities']		= Cities::all();
		$this->data['countries']		= Countries::all();
		$this->data['teamtypes']		= Teamtypes::all();
		$this->data['languages']		= Guidelanguages::all();
		$this->data['setting'] 		= $this->info['setting'];
		$this->data['fields'] 		=  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);

		$this->data['id'] = $id;

		return view('teams.form',$this->data);
	}

	public function getShow( Request $request , $id = null)
	{

		if($this->access['is_detail'] ==0)
			return Redirect::to('dashboard')
				->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$row = $this->model->getRow($id);
		if($row)
		{
			$this->data['row'] =  $row;

			$this->data['id'] = $id;
			$this->data['access']		= $this->access;
			$this->data['setting'] 		= $this->info['setting'];
			$this->data['fields'] 		= \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
			$this->data['subgrid']		= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
			$this->data['prevnext'] = $this->model->prevNext($id);

			$print = (!is_null($request->input('print')) ? 'true' : 'false' ) ;
			if($print =='true')
			{
				$data['html'] = view('teams.view', $this->data)->render();
				return view('layouts.blank',$data);
			} else {
				return view('teams.view',$this->data);
			}

		} else {

			return response()->json(array(
				'status'=>'error',
				'message'=> \Lang::get('core.note_error')
			));
		}
	}


	function postCopy( Request $request)
	{

	    foreach(\DB::select("SHOW COLUMNS FROM teams ") as $column)
        {
			if( $column->Field != 'id')
				$columns[] = $column->Field;
        }
		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO teams (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM teams WHERE id IN (".$toCopy.")";
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

	function postSave( Request $request, $id =0)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('teams');
			$data["guides"] = json_encode($request->input("guides"));
			$data["team_type"] = $request->input("team_type");
			$data["tourID"] = $request->input("tourID");
			$data["packageID"] = $request->input("packageID");
			$data["tour_category"] = $request->input("tour_category");
			$id = $this->model->insertRow($data , $request->input('id'));
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

	public static function display( )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Teams();
		$info = $model::makeInfo('teams');

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
				return view('teams.public.view',$data);
			}

		} else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> 'id' ,
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
			return view('teams.public.index',$data);
		}


	}

	function postSavepublic( Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('teams');
			 $this->model->insertRow($data , $request->input('id'));
			return  Redirect::back()->with('messagetext','<p class="alert alert-success">'.\Lang::get('core.note_success').'</p>')->with('msgstatus','success');
		} else {

			return  Redirect::back()->with('messagetext','<p class="alert alert-danger">'.\Lang::get('core.note_error').'</p>')->with('msgstatus','error')
			->withErrors($validator)->withInput();

		}

	}


}
