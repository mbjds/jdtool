<?php

namespace JDCustom\voucher;

use JDCustom\jdHelpers;
use mysql_xdevapi\SqlStatement;

class voucherInit {


	public function __construct() {
		add_action( 'init', array($this,'aeroVouchers') );
		$post_type = 'aeroVouchers';

// Register the columns.
		add_filter( "manage_aerovouchers_posts_columns", function ( $defaults ) {
			unset($defaults);
			$defaults['cb']= '<input type="checkbox" />';
			$defaults['orderid'] = 'Zamówienie';
			$defaults['voucherCode'] = 'Kod Vouchera';
			$defaults['vip'] = 'VIP';
			$defaults['used'] = 'Wykorzystany';
			$defaults['reservation'] = 'Rezerwacja';
			$defaults['reservationDate'] = 'Data rezerwacji';
			$defaults['created'] = 'Utworzono';

			return $defaults;
		} );

		add_filter( 'manage_edit-aerovouchers_sortable_columns', function ($columns){
			$columns['orderid'] = 'Zamówienie';
			$columns['voucherCode'] = 'Kod Vouchera';
			$columns['vip'] = 'VIP';
			$columns['used'] = 'Wykorzystany';
			$columns['reservation'] = 'Rezerwacja';
			$columns['reservationDate'] = 'Data rezerwacji';
			$columns['created'] = 'Utworzono';

			return $columns;
		} );



// Handle the value for each of the new columns.
		add_action( "manage_aerovouchers_posts_custom_column", function ( $column_name, $post_id ) {
			$inst = new voucherInst($post_id);
			if ( $column_name == 'orderid' ) {
				echo $inst->getOrderLink();
			}

			if ( $column_name == 'voucherCode' ) {
				// Display an ACF field
				echo $inst->getMeta('voucherCode');
			}

			if ( $column_name == 'vip' ) {
				// Display an ACF field
				$inst->renderBool($inst->getMeta('vip'));
			}
			if ( $column_name == 'used' ) {
				// Display an ACF field
				$inst->renderBool($inst->getMeta('used'));
			}
			if ( $column_name == 'reservation' ) {
				// Display an ACF field
				$inst->renderBool($inst->getMeta('reservation'));
			}
			if ( $column_name == 'reservationDate' ) {
				// Display an ACF field
				$inst->renderDate($inst->getMeta('reservationDate'));
			}
			if ( $column_name == 'created' ) {
				// Display an ACF field
				$inst->renderDate($inst->getMeta('created'));
			}


		}, 10, 2 );

	}



		// Register Custom Post Type
		public function aeroVouchers() {

			$labels = array(
				'name'                  => _x( 'Vouchery', 'Post Type General Name', 'jdtools' ),
				'singular_name'         => _x( 'Voucher', 'Post Type Singular Name', 'jdtools' ),
				'menu_name'             => __( 'Vouchery', 'jdtools' ),
				'name_admin_bar'        => __( 'Voucher', 'jdtools' ),
				'archives'              => __( 'Vouchers Archives', 'jdtools' ),
				'attributes'            => __( 'Item Attributes', 'jdtools' ),
				'parent_item_colon'     => __( 'Parent Item:', 'jdtools' ),
				'all_items'             => __( 'Wszystkie Vouchery', 'jdtools' ),
				'add_new_item'          => __( 'Dodaj', 'jdtools' ),
				'add_new'               => __( 'Dodaj', 'jdtools' ),
				'new_item'              => __( 'Nowy voucher', 'jdtools' ),
				'edit_item'             => __( 'Edytuj voucher', 'jdtools' ),
				'update_item'           => __( 'Aktualizuj voucher', 'jdtools' ),
				'view_item'             => __( 'View Item', 'jdtools' ),
				'view_items'            => __( 'View Items', 'jdtools' ),
				'search_items'          => __( 'Search Item', 'jdtools' ),
				'not_found'             => __( 'Not found', 'jdtools' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'jdtools' ),
				'featured_image'        => __( 'Featured Image', 'jdtools' ),
				'set_featured_image'    => __( 'Set featured image', 'jdtools' ),
				'remove_featured_image' => __( 'Remove featured image', 'jdtools' ),
				'use_featured_image'    => __( 'Use as featured image', 'jdtools' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'jdtools' ),
				'items_list'            => __( 'Items list', 'jdtools' ),
				'items_list_navigation' => __( 'Items list navigation', 'jdtools' ),
				'filter_items_list'     => __( 'Filter items list', 'jdtools' ),
			);
			$args = array(
				'label'                 => __( 'Voucher', 'jdtools' ),
				'description'           => __( 'Vouchery', 'jdtools' ),
				'labels'                => $labels,
				'supports'              => array( 'custom-fields' ),
				'taxonomies'            => array(  ),
				'hierarchical'          => false,
				'public'                => false,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'menu_icon'             => 'dashicons-tickets',
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => false,
				'exclude_from_search'   => true,
				'publicly_queryable'    => true,
				'rewrite'               => false,
				'capability_type'       => 'post',
				'show_in_rest'          => false,
			);
			register_post_type( 'aeroVouchers', $args );

		}




	
}