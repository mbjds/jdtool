<?php

namespace JDCustom;

class jd_checkout {
	public function __construct() {
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_vat_checkbox' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_vat_inp' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'jd_billing_company' ) );
		add_filter( 'woocommerce_checkout_fields', array($this, 'jd_edit_checkout_fields'), 50 );
		add_filter( 'woocommerce_checkout_fields', array($this, 'jd_remove_checkout_fields'), 5 );
		add_filter( 'woocommerce_billing_fields', array($this,'bbloomer_reorder_checkout_fields' ));

	}

	public function bbloomer_reorder_checkout_fields( $fields ) {

		$fields['billing_email']['priority'] = 5;
		$fields['billing_phone']['priority'] = 25;
		$fields['billing_address_1']['priority'] = 30;
		$fields['billing_city']['priority'] = 25;

		$fields['billing_postcode']['priority'] = 55;

		$fields['billing_country']['priority'] = 90;

		return $fields;
	}
	public function jd_edit_checkout_fields( $fields ) {
		$fields['billing']['billing_email']['label'] = 'Twój adres e-mail (Aby wysyłka automatyczna przebiegła poprawnie staraj się nie używać adresów z:  wp.pl, onet, interia, poczta.fm. Zalecamy pocztę na gmail. :) Jeśli bilet nie dotrze w max 5 min sprawdź SPAM i OFERTY! )';
		$fields['billing']['billing_address_1']['class'] = array('form-row-wide', 'jd-row-wide');


		$fields['order']['order_comments']['label'] = 'Dane osoby obdarowanej (pozostaw pole puste jeśli chcesz voucher bez danych) ';
		$fields['order']['order_comments']['placeholder'] = 'Tu wpisz, imię, nazwisko, lub życzenia dla osoby obdarowanej.';
		return $fields;
	}

	public function jd_remove_checkout_fields( $fields ) {
		unset( $fields['billing']['billing_address_2'] );
		//unset( $fields['billing']['billing_country'] );
		return $fields;
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