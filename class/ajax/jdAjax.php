<?php

namespace JDCustom\ajax;

use JDCustom\pdf\makePDF;
use JDCustom\voucher\voucherInst;

class jdAjax {
	/**
	 *  Constructor initializes the ajax actions for the class instance.
	 */
	public function __construct() {
		add_action( 'wp_ajax_generatePDF', [ $this, 'generatePDF' ] );
		add_action( 'wp_ajax_nopriv_generatePDF', [ $this, 'generatePDF' ] );
		add_action( 'wp_ajax_activateV', [ $this, 'activateV' ] );
		add_action( 'wp_ajax_nopriv_activateV', [ $this, 'activateV' ] );
		add_action( 'wp_ajax_update_dedication', [ $this, 'update_dedication' ] );
		add_action( 'wp_ajax_nopriv_update_dedication', [ $this, 'update_dedication' ] );
	}

	/**
	 * @return string
	 */
	public function generatePDF() {
		$id  = $_POST['id'];
		$pdf = new makePDF( $id );
		echo $pdf->generatePDF( 'S' );

		exit;
	}

	/**
	 * @return JSON response back to an Ajax request
	 */
	public function activateV(): void {
		$id       = $_POST['id'];
		$instance = new voucherInst( $id );
		$instance->activateVoucher();

		wp_send_json_success(
			[
				'status'  => 'success',
				'message' => 'Voucher aktywowany',
			],
			200
		);
	}

	public function update_dedication(): void {
		$id         = $_POST['id'];
		$dedication = $_POST['dedication'];
		$voucher    = new voucherInst( $id );
		$voucher->setDedication( $dedication );
		wp_send_json_success(
			[
				'status'  => 'success',
				'message' => 'Dedykacja została zaktualizowana',
			],
			200
		);


	}
}