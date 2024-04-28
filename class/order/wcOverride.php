<?php

namespace JDCustom\order;

use JDCustom\jdLog;
use JDCustom\voucher\aeroVoucher;
use JDCustom\voucher\voucherInst;

class wcOverride
{
    /**
     *  Method to close order after payment fired on woocommerce_order_status_changed hook
     * .
     *
     * @param  mixed  $order_id
     * @param  mixed  $old_status
     * @param  mixed  $new_status
     */
    public function closeAfterPayment($order_id, $old_status, $new_status): void
    {
        $aero = new aeroVoucher();

        if ('completed' != $old_status && 'processing' == $new_status) {
            $order = wc_get_order($order_id);
            $order->set_status('completed');
            $order->save();
            $log = new jdLog();
            $log->logInfo('Zamknięto zamówienie po zrealizowaniu płaności ' . $order_id);
            $v = $aero->getVouchersIDByOrder($order_id);
            foreach ($v as $id) {
                $instance = new voucherInst($id);
                $instance->activateVoucher();
                $log->logInfo('Status vouchera ' . $instance->getVoucherCode() . ' zmienieniono na aktywny');
            }
        }
    }

    public static function getOrderNo(int $id)
    {
        return wc_get_order($id)->get_meta('_order_number');
    }

    /**
     * @param $order \WC_Order
     *
     * @return int[]|\WP_Post[]
     */
    public static function getVouchersFromOrder(\WC_Order $order): array
    {
        $args = array(
            'meta_key'       => 'order_id',
            'meta_value'     => $order->get_id(),
            'post_type'      => 'aerovouchers',
            'post_status'    => 'any',
            'posts_per_page' => - 1,
            'fields'         => 'ids'
        );

        return get_posts($args);


    }
}
