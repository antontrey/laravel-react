<?php namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DatePeriod;

class DashboardController extends Controller {

	public function __construct()
	{
		parent::__construct();
        $this->data = array(
            'pageTitle' =>  CNF_COMNAME,
            'pageNote'  =>  'Welcome to Dashboard',
            
        );			
	}

	public function getIndex( Request $request )
	{
     $graph = \DB::table('bookings')
    ->select(\DB::raw('MONTHNAME(created_at) as month'), \DB::raw("DATE_FORMAT(created_At,'%M %Y') as monthNum"),    \DB::raw('count(*) as totalbook'))
    ->groupBy('monthNum')
    ->orderBy('created_at', 'asc')
    ->get();
        $today=Carbon::today(); 
        $lastweek = Carbon::today()->subDays(7);
        $lastmonth = Carbon::today()->subDays(30);
        $lastyear = Carbon::today()->subDays(300);

        $monthlybookingreport = \DB::table('bookings')->select(\DB::raw('MONTHNAME(created_at) as month'), 
                                \DB::raw("DATE_FORMAT(created_At,'%M %Y') as monthNum"),    
                                \DB::raw('count(*) as totalbook'))->groupBy('monthNum')->orderBy('created_at', 'asc')->get();
    
        		 
        $this->data['online_users']       = \DB::table('tb_users')->orderBy('last_activity','desc')->limit(10)->get(); 
		$this->data['active'] = '';
		$this->data['activeagents']       = \DB::table('travel_agent')->where('status','1')->count('travelagentID'); ;
		$this->data['activeguides']       = \DB::table('guides')->where('status','1')->count('guideID');
		$this->data['activesupplier']     = \DB::table('def_supplier')->where('status','1')->count('supplierID');
		$this->data['activehotels']       = \DB::table('hotels')->where('status','1')->count('hotelID');
		$this->data['totalbookings']      = \DB::table('bookings')->count('bookingsID');
		$this->data['todaysbookings']     = \DB::table('bookings')->where('created_at','>',$today)->count('bookingsID');
		$this->data['lastweeksbookings']  = \DB::table('bookings')->where('created_at','>',$lastweek)->count('bookingsID');
		$this->data['lastmonthssbookings']= \DB::table('bookings')->where('created_at','>',$lastmonth)->count('bookingsID');
		$this->data['running_tours']      = \DB::table('tour_date')->where('start','<=',$today)->where('end','>',$today)->where('status',1)->count('tourID');
		$this->data['upcoming_tours']     = \DB::table('tour_date')->where('start','>',$today)->where('status',1)->count('tourID');
		$this->data['old_tours']          = \DB::table('tour_date')->where('start','<',$today)->where('end','<',$today)->where('status',1)->count('tourID');
		$this->data['monthlybookingreport']           = $monthlybookingreport;
		$this->data['graph']           = $graph;
		return view('dashboard.index',$this->data);
	}	

	public function getDashboard()
	{
		
	}

}