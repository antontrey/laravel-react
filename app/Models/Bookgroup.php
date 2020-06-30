<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Bookgroup extends Mmb  {

	protected $table = 'booking_groups';
	protected $primaryKey = 'group_id';

	public function __construct() {
		parent::__construct();

	}

	public static function querySelect(  ){

		return "  SELECT booking_groups.groupnameID, booking_groups.categoryID, booking_groups.formula, booking_groups.date, booking_groups.travellerCount, booking_groups.status FROM booking_groups  ";
	}

	public static function queryWhere(  ){

		return "  WHERE booking_groups.group_id IS NOT NULL ";
	}

	public static function queryGroup(){
		return "  ";
	}


}
