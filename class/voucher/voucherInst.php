<?php

namespace JDCustom\voucher;
use EasyWPSMTP\WP;
use WP_Post;
class voucherInst {

	public function __construct($id){
		$this->vid = $id;
	//	$this->post = get_post($this->vid);
	}

	public function getMeta($meta){
		return get_post_meta($this->vid, $meta, true);
	}

	public function getOrderLink(){
		$order = wc_get_order($this->getMeta('order_id'));
		$url = $order->get_edit_order_url();
		return '<a href="'.$url.'">'.$this->getMeta('order_id').' </a>';
	}

	public function renderBool($val){
		if($val == 1){
			echo "Tak";
		}else{
			echo "Nie";
		}
	}

public function renderDate($date){
	if(!$date){
		echo '----';
	}else{
		echo $date;
	}
}



}