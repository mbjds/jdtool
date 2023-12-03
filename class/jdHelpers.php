<?php

namespace JDCustom;

class jdHelpers {


	public static function checkNip($nip){
		$nipWithoutDashes = preg_replace("/[\s-]+/","",$nip);


		$reg = '/^[0-9]{10}$/';
		if(preg_match($reg, $nipWithoutDashes)==false)
			return false;
		else
		{
			$digits = str_split($nipWithoutDashes);
			$checksum = ( 6 * (int) $digits[0] + 5 * (int) $digits[1] + 7 * (int) $digits[2] + 2 * (int) $digits[3] + 3 * (int) $digits[4] + 4 * (int) $digits[5] + 5 * (int) $digits[6] + 6 * (int) $digits[7] + 7 * (int) $digits[8] ) % 11;

			return ( (int) $digits[9] === $checksum);
		}
	}

	public static function table_exists( $table ) {
		global $wpdb;
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
		if ( \is_wp_error( $table_exists ) || \is_null( $table_exists ) ) {
			return false;
		}

		return true;
	}
}