<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\Hotels;
use App\Models\Hotelrates;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;


class HotelsController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'hotels';
	static $per_page	= '100000';

	public function __construct()
	{

		$this->model = new Hotels();

		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);

		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'hotels',
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

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : 'hotelID');
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
		$pagination->setPath('hotels');

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
		return view('hotels.index',$this->data);
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
		} else {
			$this->data['row'] = $this->model->getColumnTable('hotels');
		}
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);

		$this->data['id'] = $id;
		return view('hotels.form',$this->data);
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
			$this->data['prevnext'] = $this->model->prevNext($id);


    $roomrates 		=  \DB::table('hotel_rates')->where('hotelID',$id)->orderBy('season','ASC')->get();
		$room = array();
		$first = 0;
		foreach($roomrates as $rr)
		{
			$room[] = array(
				'hotelrateid'	    =>$rr->hotelrateid ,
				'hotelID'	        =>$rr->hotelID,
				'roomtypeID'	    =>$rr->roomtypeID,
				'season'	        =>$rr->season,
				'rate'	            =>$rr->rate,
				'currency'	        =>$rr->currency,
				'images'            =>$rr->images,
				'created_at'       	=>$rr->created_at,
				'updated_at'       	=>$rr->updated_at
			);
			++$first;
		}
		$this->data['room']  = $room;


    $hotelnotes 		=  \DB::table('hotels_note')->where('hotelID',$id)->orderBy('created_at','ASC')->get();
		$notes = array();
		$second = 0;
		foreach($hotelnotes as $hn)
		{
			$notes[] = array(
				'hotel_noteID'	    =>$hn->hotel_noteID ,
				'hotelID'	        =>$hn->hotelID,
				'title'	            =>$hn->title,
				'note'	            =>$hn->note,
				'style'	            =>$hn->style,
				'updated_at'        =>$hn->updated_at,
				'created_at'    	=>$hn->created_at,
				'entry_by'	       	=>$hn->entry_by
			);
			++$second;
		}
		$this->data['notes']  = $notes;



            return view('hotels.view',$this->data);
		} else {
			return Redirect::to('hotels')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
	}

	function postCopy( Request $request)
	{
	    foreach(\DB::select("SHOW COLUMNS FROM hotels ") as $column)
        {
			if( $column->Field != 'hotelID')
				$columns[] = $column->Field;
        }

		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO hotels (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM hotels WHERE hotelID IN (".$toCopy.")";
			\DB::insert($sql);
			return Redirect::to('hotels')->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {

			return Redirect::to('hotels')->with('messagetext',\Lang::get('core.note_selectrow'))->with('msgstatus','error');
		}

	}

	function postSave( Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('tb_hotels');

			$id = $this->model->insertRow($data , $request->input('hotelID'));

			if(!is_null($request->input('apply')))
			{
				$return = 'hotels/update/'.$id.'?return='.self::returnUrl();
			} else {
				$return = 'hotels?return='.self::returnUrl();
			}

			// Insert logs into database
			if($request->input('hotelID') =='')
			{
				\App\Library\SiteHelpers::auditTrail( $request , 'New Data with ID '.$id.' Has been Inserted !');
			} else {
				\App\Library\SiteHelpers::auditTrail($request ,'Data with ID '.$id.' Has been Updated !');
			}

			return Redirect::to($return)->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');

		} else {

			return Redirect::to('hotels/update/'. $request->input('hotelID'))->with('messagetext',\Lang::get('core.note_error'))->with('msgstatus','error')
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
			return Redirect::to('hotels')
        		->with('messagetext', \Lang::get('core.note_success_delete'))->with('msgstatus','success');

		} else {
			return Redirect::to('hotels')
        		->with('messagetext',\Lang::get('core.note_noitemdeleted'))->with('msgstatus','error');
		}

	}

	public static function display( )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Hotels();
		$info = $model::makeInfo('hotels');

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
				return view('hotels.public.view',$data);
			}

		} else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> 'hotelID' ,
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
			return view('hotels.public.index',$data);
		}


	}

	function postSavepublic( Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('hotels');
			 $this->model->insertRow($data , $request->input('hotelID'));
			return  Redirect::back()->with('messagetext','<p class="alert alert-success">'.\Lang::get('core.note_success').'</p>')->with('msgstatus','success');
		} else {

			return  Redirect::back()->with('messagetext','<p class="alert alert-danger">'.\Lang::get('core.note_error').'</p>')->with('msgstatus','error')
			->withErrors($validator)->withInput();

		}

	}


        static public function hotelFacilities( $facilities = '')
	{
		$hotelFacilities='';
		if($facilities !='')
		{
			$sql3 = \DB::table('def_hotel_facilities')->whereIn('hotelfacilityID',explode(',',$facilities))->get();
			foreach ($sql3 as $v3) {

				$hotelFacilities .= "<span class='label label-primary'>".$v3->facility."</span>&nbsp; ";
			}
		}
		return $hotelFacilities;
	}


    	public function getNotedelete( Request $request , $hotelID ,  $hotel_noteID=0)
	{
		\DB::table('hotels_note')->where('hotel_noteID',$hotel_noteID)->delete();
			return Redirect::to('hotels/show/'.$hotelID)
	        	->with('messagetext','Note has been deleted !')->with('msgstatus','success');
	}



  static function similarHotel( $ho = '')
	{
		$hotel='';
		if($ho !='')
		{
			$sqlho = \DB::table('hotels')->whereIn('hotelID',explode(',',$ho))->get();
			foreach ($sqlho as $h) {
				$hotel .= "<a href=".asset('hotels/show/'.$h->hotelID.'')."><span class='label label-default'>".$h->hotel_name."</span></a>&nbsp;";
			}
		}
		return $hotel;
	}

	static function get_roomtypes_from_hotel($id){

		$hotelrates = \DB::table('hotel_rates')->join('def_room_types', 'hotel_rates.roomtypeID', '=', 'def_room_types.roomtypeID')
		->where('hotelID','=', $id)->get();
		return $hotelrates->toJson();
	}

}
