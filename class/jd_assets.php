<?php

namespace JDCustom;

class jd_assets {

	public function __construct(){
		add_action( 'wp_enqueue_scripts', array($this, 'custom_enqueue_checkout_script' ));

	}

	public function custom_enqueue_checkout_script() {
		if ( is_checkout() ) {
			wp_enqueue_script( 'jd-checkout-js', plugin_dir_url( __DIR__ ) . 'assets/js/jdcustom.js', null, '45' );

		}
		wp_enqueue_script( 'js-general-js', plugin_dir_url( __DIR__ ) . 'assets/js/global.js', null, '2.1' );


		wp_enqueue_style( 'jd-style', plugin_dir_url( __DIR__ ) . 'assets/css/jd-checkout.css', false, '1.3', 'all' );

	}

}