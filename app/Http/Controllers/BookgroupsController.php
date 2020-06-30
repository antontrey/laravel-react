<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use Illuminate\Support\Facades\DB;
use App\Models\Teams;
use App\Models\Teamtypes;
use App\Models\Guide;
use App\Models\Guidelanguages;
use App\Models\Cities;
use App\Models\Bookgroup;
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

class BookgroupsController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'bookinggroups';
	static $per_page	= '10';

	public function __construct()
	{
		parent::__construct();
		$this->model = new Bookgroup();
		$this->info = $this->model->makeInfo( $this->module);
		$this->access = $this->model->validAccess($this->info['id']);
		$this->info['setting']['form-method'] = "native";
		// $this->access['is_detail'] = 0;
		$this->data = array(
			'pageTitle'			=> 	$this->info['title'],
			'pageNote'			=>  $this->info['note'],
			'pageModule'		=> 'bookinggroups',
			'pageUrl'			=>  url('bookinggroups'),
			'return' 			=> 	self::returnUrl()
		);

	}

	public function getIndex()
	{
        $this->data['page_singlename'] = 'bookinggroups';
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		$this->data['access']		= $this->access;
		return view('bookgroup.index',$this->data);
	}
	public function board()
	{
		$this->data['page_singlename'] = 'bookinggroups';
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		$this->data['access']		= $this->access;
		$guides = \DB::table('guides')
			->select('name')
			->get();
		$this->data['guides']		= $guides;

			// var_dump($guides);exit;
		return view('bookgroup.group_view',$this->data);
	}

	public function postData( Request $request)
	{
		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
		$order = (!is_null($request->input('order')) ? $request->input('order') : $this->info['setting']['ordertype']);
		// End Filter sort and order for query
		// Filter Search for query
		$filter = '';
		$this->data['rowData'] = DB::select('select booking_groups.* from booking_groups');
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
        $this->data['page_singlename'] = 'bookinggroups';
		return view('bookgroup.table',$this->data);

	}


	function getUpdate(Request $request, $id = null)
	{
// var_dump('tes');exit;
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
		return view('bookgroup.form',$this->data);
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
			// $roomtypeIDs = \DB::table('booking_groups')
			// 	->where('group_id', '=', $id)
			// 	->value('roomtypes');
			// 	$roomtypeIDs_array = explode(",", $roomtypeIDs);
			// 	$roomtypes = [];
			// 	$i = 0;
			// 	$roomtypeIDs_array = array_unique($roomtypeIDs_array);
			// foreach($roomtypeIDs_array as $roomtypeIDs){
			// 	$roomtypes[$i] = \DB::table('def_room_types')
			// 	->where('roomtypeID', '=', $roomtypeIDs)
			// 	->value('room_type');
			// 	$i++;

			// }
			$formula = \DB::table('booking_groups')
				->where('group_id', '=', $id)
				->value('formula');
				if($formula == "1" ){

					$packageID = \DB::table('booking_groups')
						->where('group_id', '=', $id)
						->value('packageID');
					$roomtypeIDs_array = \DB::table('room_features')
						->select('roomtypeID')
						->where('packageID', '=', $packageID)
						->get();
						$i = 0;
					foreach($roomtypeIDs_array as $roomtypeIDs){
						$roomtypeIDs = $roomtypeIDs->roomtypeID;
						$roomtypes[$i] = \DB::table('def_room_types')
						->where('roomtypeID', '=', $roomtypeIDs)
						->value('room_type');
						$i++;

					}
				// var_dump($roomtypeIDs);exit;
					$this->data['id'] = $id; 
					$this->data['roomtypes'] = $roomtypes; 
					$this->data['access']		= $this->access;
					$this->data['setting'] 		= $this->info['setting'];
					$this->data['fields'] 		= \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
					$this->data['subgrid']		= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
					// $this->data['prevnext'] = $this->model->prevNext($id);

					$print = (!is_null($request->input('print')) ? 'true' : 'false' ) ;
					if($print =='true')
					{
						$data['html'] = view('bookgroup.view', $this->data)->render();
						return view('layouts.blank',$data);
					} else {
						return view('bookgroup.view',$this->data);
					}
				}else{

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
        // var_dump('test');exit;
		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('booking_groups');
			$data["guides"] = json_encode($request->input("guides"));
			$data["team_type"] = $request->input("team_type");
			$data["tourID"] = $request->input("tourID");
			$tourID = $request->input("tourID");
			$data["tourdateID"] = $request->input("tourdateID");
			$tourdateID = $request->input("tourdateID");
			$packageID = $request->input("packageID");
			$data["packageID"] = $request->input("packageID");
            // $data["tour_category"] = $request->input("tour_category");
            $data["tourcategoriesID"] = $request->input("tourcategoriesID");
            $tourcategoriesID = $request->input("tourcategoriesID");
			$tour_category = $request->input("tour_category");
			$tourcategories = \DB::table('def_tour_categories')
			->where('tourcategoriesID', '=', $tourcategoriesID)
			->value('tourcategoryname');
            
            if($data["formula"] == "1"){
                $all_travellers = \DB::table('book_room')
                    ->select('book_room.travellers','book_room.roomtype','book_room.bookingID')
                    ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
                    ->where('packageID', '=', $packageID)
					->get();
					
				$Name = \DB::table('packages')
				->where('packageID', '=', $packageID)
				->value('package_name');

				$date = \DB::table('packages')
				->where('packageID', '=', $packageID)
				->value('created_at');
				
            }elseif($data["formula"] == "0"){
                $all_travellers = \DB::table('book_room')
                ->select('book_room.travellers','book_room.roomtype','book_room.bookingID')
                ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
                ->where('tourID', '=', $tourID)
                ->where('tourdateID', '=', $tourdateID)
				->get();
				$Name = \DB::table('tours')
				->where('tourID', '=', $tourID)
				->value('tour_name');
				$date = \DB::table('tours')
				->where('tourID', '=', $tourID)
				->value('created_at');
				
            }else{
                $message = $this->validateListError(  $validator->getMessageBag()->toArray() );
                return response()->json(array(
                    'message'	=> "Please select formula.",
                    'status'	=> 'error'
                ));
			}
            if(!count($all_travellers) >0){
                return response()->json(array(
                    'message'	=> "The Traveller doesn't exist.",
                    'status'	=> 'error'
                ));
			}
			$date = strtotime($date);
			$date = date("Y-m-d", $date);
            $i =0;
            foreach ($all_travellers as $all_traveller) {
                $travellersID[$i] = $all_traveller->travellers;
                $roomtype[$i] = $all_traveller->roomtype;
                $travellers[$i] = \DB::table('travellers')
                ->where('travellerID', '=', $travellersID[$i])
                ->value('nameandsurname');
                $i++;
            }
            $travellercount= count($travellers);
            // $traveller_list = implode(",", $travellers);
            $roomtype = implode(",", $roomtype);
            // $roomtype = explode(",", $roomtype);
// var_dump($roomtype);exit;

            if($data["formula"] == "1"){ 
                           
				$groupData = array('groupnameID' => $Name, 'formula' => '1', 'status' => '1', 'travellerCount' => $travellercount, 'categoryID' => $tourcategories,'date' => $date, 'roomtypes' => $roomtype,'packageID' => $packageID);
                \DB::table('booking_groups')->insert($groupData);
            }else{
               
                $groupData = array('groupnameID' => $Name, 'formula' => '0', 'status' => '1','travellerCount' => $travellercount, 'date' => $date, 'categoryID' => $tourcategories);
                \DB::table('booking_groups')->insert($groupData);
            }
            // exit;
			// $id = $this->model->insertRow($data , $request->input('id'));
			return response()->json(array(
				'status'=>'success',
				'message'=> $data
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
