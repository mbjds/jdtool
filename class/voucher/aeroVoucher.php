<?php

namespace JDCustom\voucher;

use JDCustom\jdLog;
use JDCustom\order\wcOverride;

class aeroVoucher
{
    private jdLog $log;
    private $fields = [
        'order_id',
        'voucherCode',
        'vip',
        'used',
        'reservation',
        'reservationDate',
        'salesLineID',
        'created',
    ];
    private $posttype;

    public function __construct()
    {
        $this->posttype = 'aerovouchers';
        $this->log = new jdLog();
    }

    public function insertVoucher(
        $order_id,
        $voucherCode,
        $vip,
        $salesLineID,
        $status = 0,
        $dedication = null,
        $created = false,
        $closed = false,
        $reservationDate = false
    ) {
        if (!$this->voucherExists($voucherCode)) {
            $post_id = wp_insert_post([
                'post_status' => 'publish',
                'post_type' => $this->posttype,
            ]);

            if (!$created) {
                $created = date('Y-m-d H:i:s');
            }

            $this->log->logInfo('Voucher: '.$voucherCode.' został utworzony');

            update_post_meta($post_id, 'order_id', $order_id);
            update_post_meta($post_id, 'voucherCode', $voucherCode);
            update_post_meta($post_id, 'vip', $vip);
            update_post_meta($post_id, 'salesLineID', $salesLineID);
            update_post_meta($post_id, 'vStatus', $status);
            update_post_meta($post_id, 'reservation', $reservationDate);
            update_post_meta($post_id, 'created', $created);
            update_post_meta($post_id, 'closed', $closed);
            update_post_meta($post_id, 'dedication', $dedication);

            return $post_id;
        }

        return 'exists';
    }

    public function generateVoucher(int $order_id, $old = false): void
    {
        if (true == get_post_meta($order_id, 'hasVouchers', true)) {
            $this->log->logWarning('Vouchers for order '.$order_id.' already generated');
        } else {
            $order = new \WC_Order($order_id);
            $year = date('y');
            $orderNo = wcOverride::getOrderNo($order_id);
            $lp = 1;
            $stat = $order->get_status();
            if ('processing' === $stat) {
                $status = 1;
            } else {
                $status = 0;
            }
            foreach ($order->get_items() as $id => $item) {
                if (0 === $item->get_product_id()) {
                    $time = '';
                } else {
                    $prod = $item->get_product();

                    $time = $prod->get_weight();
                }

                $prefix = $time.'-'.$orderNo;

                $prefix .= '-'.$lp;
                $q = $item->get_quantity();
                $codes = [];
                if (true === $old) {
                    $codes[] = $orderNo;
                    $dedication = $order->get_customer_note();
                } else {
                    for ($i = 1; $i <= $q; ++$i) {
                        $codes[] = $prefix.$i;
                    }
                }

                wc_update_order_item_meta($id, '_vouchers', $codes);

                if (has_term('vip', 'product_cat', $item->get_product_id())) {
                    $vip = true;
                } else {
                    $vip = false;
                }
                if ($old) {
                    $date = date('Y-m-d H:i:s', strtotime($order->get_date_created()));
                } else {
                    $date = date('Y-m-d H:i:s');
                }
                foreach ($codes as $code) {
                    $this->insertVoucher($order_id, $code, $vip, $id, $status, $dedication, $date);
                }

                ++$lp;
            }
            self::hasVoucherGenerated($order_id);
        }
    }

    public function getVouchersIDByOrder($order_id)
    {
        $attrs = [
            'post_type' => $this->posttype,
            'meta_key' => 'order_id',
            'meta_value' => $order_id,
            'fields' => 'ids',
            'numberposts' => -1,
        ];

        return get_posts($attrs);
    }

    public function getVouchersIDBySalesLine($salesLine)
    {
        $attrs = [
            'post_type' => $this->posttype,
            'meta_key' => 'salesLineID',
            'meta_value' => $salesLine,
            'fields' => 'ids',
        ];

        return get_posts($attrs);
    }

    public function voucherExists($code)
    {
        global $wpdb;

        $sql = 'select post_id from dlaextremalnych_postmeta where meta_key = "voucherCode" and meta_value = "'.$code.'"';

        $res = [];

        $r = $wpdb->get_results($sql);

        if ($wpdb->num_rows > 0) {
            foreach ($r as $i) {
                $res['ids'][] = $i->post_id;
            }
            $res['exists'] = true;
            $this->log->logWarning('Voucher: '.$code.' istnieje w bazie danych.');

            return $res;
        }

        return false;
    }

    public static function hasVoucherGenerated($orderID)
    {
        return update_post_meta($orderID, 'hasVouchers', true);
    }

    public static function getOrdersWithoutVouchers()
    {
        $args = [
            'limit' => -1,
            'return' => 'ids',
            'meta_key' => 'hasVouchers',
            'meta_compare' => 'not exists',
            'status' => ['wc-processing', 'wc-pending'],
            'type' => 'shop_order', // filtered refunded orders
        ];

        return wc_get_orders($args);
    }
}
