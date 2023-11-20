<?php

namespace JDCustom;
use FontLib\Table\Type\head;
use WC_Order_Query;
use DateTime;
class jd_toolset {

	public function __construct(){

	}

	public static function nicedump($var){

		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}

	public static function getCount($val){
		echo count($val);

	}

	public static function getOrdersWithoutVIPflag(){




		$args = array(
			'limit' => -1,
			'return' => 'ids',
			'meta_key'     => 'has_vip', // The postmeta key field
			'meta_compare' => 'NOT EXISTS', // The comparison argument
		//	'status' => array('wc-processing', 'wc-on-hold', 'wc-pending')


		);


			$ids = wc_get_orders( $args );

		$tagd = array();
		foreach ($ids as $i => $ii) {
			$tagd[] = $ii;
		}
		return $tagd;
	}


	public static function getVipIDs(){
		global $wpdb;
		$ids = self::getOrdersWithoutVIPflag();
		$i = implode(',',$ids);
		$sql = "select distinct order_id from dlaextremalnych_woocommerce_order_items where order_id in ($i) and order_item_type = 'line_item' and order_item_name LIKE '%vip%' ";
		$resp = $wpdb->get_results($sql, ARRAY_A);

		foreach ($resp as $r){
			if(!get_post_meta($r['order_id'], 'has_vip', true) ){
				update_post_meta($r['order_id'], 'has_vip', true);

			}
		}
		return count($resp);


	}

	public static function getNotVipIDs(){
		global $wpdb;

		$ids = self::getOrdersWithoutVIPflag();
		$i = implode(',',$ids);
		$sql = "select distinct order_id from dlaextremalnych_woocommerce_order_items where order_id in ($i) and order_item_type = 'line_item' and order_item_name NOT LIKE '%vip%' ";
		$resp = $wpdb->get_results($sql, ARRAY_A);

		foreach ($resp as $r){
			if(get_post_meta($r['order_id'], 'has_vip', true) !== null){
				update_post_meta($r['order_id'], 'has_vip', false);

			}


		}
		return count($resp);
	}
	public static function closeOutdated( ){

		$two_years_ago = new DateTime('-2 years');
		$two = $two_years_ago->format('d-m-Y');
		$query = new WC_Order_Query( array(
		'limit' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
		'status' => array('wc-processing', 'wc-pending'),
		'date_created' => '<' . $two ,
		'meta_key' => 'has_vip',
		'meta_value' => false,
		'meta_compare' => '=',

		'return' => 'ids'
		) );
		$orders = $query->get_orders();

		foreach ($orders as $id){
				$order = wc_get_order($id);

				$order->add_order_note('Mineła ważność vouchera',false);
				$order->set_status('wc-completed');
				$order->save();

			//	self::nicedump($order);

		}

		return $orders;
		//		self::nicedump($orders);
		 

	}
}