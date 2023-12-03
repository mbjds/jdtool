<?php

namespace JDCustom\voucher;

class aeroVoucher {
	private $fields = array(
		'order_id',
		'voucherCode',
		'vip',
		'used',
		'reservation',
		'reservationDate',
		'salesLineID',
		'created'
	);
	private $posttype;

	public function __construct() {
		$this->posttype = 'aerovouchers';
	}

	public function insertVoucher($order_id, $voucherCode, $vip, $salesLineID, $created = false, $used = false, $reservation = false, $reservationDate = false ){
		$post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => $this->posttype,

		) );
		if($created === false){
			$created = date('Y-m-d H:i:s');
		}

		update_post_meta($post_id, 'order_id', $order_id);
		update_post_meta($post_id, 'voucherCode', $voucherCode);
		update_post_meta($post_id, 'vip', $vip);
		update_post_meta($post_id, 'salesLineID', $salesLineID);
		update_post_meta($post_id, 'used', $used);
		update_post_meta($post_id, 'reservation', $reservation);
		update_post_meta($post_id, 'reservationDate', $reservationDate);
		update_post_meta($post_id, 'created', $created);


		return $post_id;

	}
	public function generateVoucher(int $order_id){
		$order = new \WC_Order($order_id);
		$year = date('y');
		$lp = 1;
		foreach ($order->get_items() as $id => $item){
			$prefix = $year.'-'.$order_id;

			$prefix .= '-'.$lp;
			$q = $item->get_quantity();
			$codes = [];
			for ($i = 1; $i <= $q; $i++){
				$codes[] = $prefix.$i;
			}
			wc_update_order_item_meta($id, '_vouchers', $codes);

			if(has_term('vip', 'product_cat', $item->get_product_id())){
				$vip = true;
			}else{
				$vip = false;

			}
			$date = date('Y-m-d H:i:s');
			foreach ($codes as $code){
				$this->insertVoucher($order_id, $code, $vip, $id, $date);
			}


			++$lp;
		}
	}

}