<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Accounting extends Mmb  {

	protected $table = 'accounting';
	protected $primaryKey = 'accountingID';

	public function __construct() {
		parent::__construct();

	}

	public static function querySelect(  ){

		return " SELECT accounting.date, accounting.price, accounting.currency, accounting.type, accounting.category FROM accounting ";
	}

	public static function queryWhere(  ){

		return "  WHERE accounting.accountingID IS NOT NULL ";
	}

	public static function queryGroup(){
		return "  ";
	}


}
