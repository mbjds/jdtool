<?php

namespace JDCustom\voucher;

class dbQuery {
	public function __construct(){
		global $wpdb;

		$this->sql = $wpdb;
		$this->table = $wpdb->prefix . 'aero_Vouchers';


	}


}