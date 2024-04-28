<?php

namespace JDCustom\voucher;

/**
 * Class voucher instance for single voucher.
 */
class voucherInst
{
    private int $vid;
    private int $status;

    public function __construct($id)
    {
        $this->vid    = $id;
        $this->status = (int) get_post_meta($this->vid, 'vStatus', true);
    }

    /**
     * @param $meta  - meta key
     *
     * Wrapper for get_post_meta
     */
    public function getMeta($meta): mixed
    {
        return get_post_meta($this->vid, $meta, true);
    }

    /**
     * @return string - url to order edit page wich contains voucher
     */
    public function getOrderLink(): string
    {
        $order = wc_get_order($this->getMeta('order_id'));
        $url   = $order->get_edit_order_url();

        return '<a href="' . $url . '">' . $this->getOrderNo() . ' </a>';
    }

    public function getOrderViewLink(): string
    {
        $order = wc_get_order($this->getMeta('order_id'));
        $url   = $order->get_view_order_url();

        return '<a href="' . $url . '">' . $this->getOrderNo() . ' </a>';
    }

    /**
     * @param $val bool - value to render
     */
    public function renderBool($val): void
    {
        echo match ($val) {
            0 => 'Nie',
            1 => 'Tak',
            default => 'Nieznany',
        };
    }

    public function renderStatus(): void
    {
        echo match ($this->status) {
            0 => '<span style="font-weight: bold; color: #a9a1a1">Nieaktywny</span>',
            1 => '<span style="font-weight: bold; color: green">Aktywny</span>',
            2 => '<span style="font-weight: bold; color: blue">Zarezerwowany</span>',
            3 => '<span style="font-weight: bold; color: #c17e0e">Wykorzystany</span>',
            4 => '<span style="font-weight: bold; color: red">Anulowany</span>',
            default => 'Nieznany',
        };
    }

    /**
     * Method to activate voucher ( change status 0 to 1).
     *
     * @return array - status of the activation
     */
    public function activateVoucher(): array
    {
        if (0 == $this->status) {
            update_post_meta($this->vid, 'vStatus', 1);

            return [ 'status' => 'success', 'message' => 'Voucher aktywowany' ];
        }

        return [ 'status' => 'error', 'message' => 'Voucher nie może być aktywowany' ];
    }

    public function renderDate($date): void
    {
        if (! $date) {
            echo '----';
        } else {
            echo $date;
        }
    }

    public function renderIsDedication()
    {
        if ($this->getMeta('dedication')) {
            echo 'Tak';
        } else {
            echo 'Nie';
        }
    }

    public function getCode(): string
    {
        return $this->getMeta('voucherCode');
    }

    public function getSalesItemID(): int
    {
        return $this->getMeta('salesLineID');
    }

    public function getItemTitle()
    {
        //	;
        global $wpdb;
        $sql = 'select order_item_name from dlaextremalnych_woocommerce_order_items where order_item_id = ' . $this->getSalesItemID();

        return $wpdb->get_results($sql, ARRAY_A)[0]['order_item_name'];
    }

    public function getVoucherCode(): string
    {
        return $this->getMeta('voucherCode');
    }

    public function calculteVoucherExireDate()
    {
        $created = $this->getMeta('created');
        $vip     = $this->getMeta('vip');
        if (! $vip) {
            $date = date('Y-m-d', strtotime($created . ' + 2 year'));
        } else {
            $date = 'bezterminowy';
        }

        return $date;
    }

    public function setStatus($statusCode)
    {
        update_post_meta($this->vid, 'vStatus', $statusCode);
    }

    public function getOrderFromVoucher(): \WC_Order
    {
        return wc_get_order($this->getMeta('order_id'));
    }

    public function getOrderNo(): string
    {
        return $this->getOrderFromVoucher()->get_meta('_order_number');
    }

    public function getDedication()
    {
        return $this->getMeta('dedication');
    }

    public function convertDedication()
    {
        $converted = $this->getDedication();
        $conv      = str_replace('<br>', '&#13;&#10;', $converted);

        return $conv;
    }

    public function setDedication($dedication)
    {
        return update_post_meta($this->vid, 'dedication', $dedication);
    }

    public function getOrderDate()
    {
        return $this->getOrderFromVoucher()->get_date_created('view');
    }

    public function formatDates($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function getEmailFromOrdeer(): string
    {
        return $this->getOrderFromVoucher()->get_billing_email('view');
    }

    public function getCustomerID()
    {
        return $this->getOrderFromVoucher()->get_customer_id('view');

    }
}
