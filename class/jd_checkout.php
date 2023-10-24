<?php

namespace JDCustom;

class jd_checkout {
	public function __construct() {
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_vat_checkbox' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_vat_inp' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_billing_company' ) );

	}

	public function jd_vat_checkbox( $checkout ) {

		woocommerce_form_field( 'vat_choose', array(
			'type'     => 'checkbox',
			'class'    => array( 'form-row-wide' ),
			'label'    => 'Chcę fakturę VAT',
			'required' => false,
			'id'       => 'vat_choose',
		), $checkout->get_value( 'vat_choose' ) );
	}

	public function jd_vat_inp( $checkout ) {

		woocommerce_form_field( 'vat_no', array(
			'type'     => 'text',
			'class'    => array( 'form-row-wide vat-hidden' ),
			'label'    => 'Numer NIP',
			'required' => true,
			'id'       => 'vat_no',
		), $checkout->get_value( 'vat_no' ) );
	}
	public function jd_billing_company( $checkout ) {

		woocommerce_form_field( 'billing_company2', array(
			'type'     => 'text',
			'class'    => array( 'form-row-wide vat-hidden' ),
			'label'    => 'Nazwa firmy',
			'required' => true,
			'id'       => 'billing_company',
		), $checkout->get_value( 'billing_company2' ) );
	}

}