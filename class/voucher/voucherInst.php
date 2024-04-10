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
        $this->vid = $id;
        $this->status = (int) get_post_meta($this->vid, 'vStatus', true);
    }

    /**
     * @param $meta - meta key
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
        $url = $order->get_edit_order_url();

        return '<a href="'.$url.'">'.$this->getMeta('order_id').' </a>';
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
            0 => 'Nieaktywny',
            1 => 'Aktywny',
            2 => 'Zarezerwowany',
            3 => 'Wykorzystany',
            4 => 'Anulowany',
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

            return ['status' => 'success', 'message' => 'Voucher aktywowany'];
        }

        return ['status' => 'error', 'message' => 'Voucher nie może być aktywowany'];
    }

    public function renderDate($date): void
    {
        if (!$date) {
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
        //	$this->getSalesItemID();
        return wc_get_order_item_meta($this->getSalesItemID(), 'name', true);
    }

    public function getVoucherCode(): string
    {
        return $this->getMeta('voucherCode');
    }

    public function calculteVoucherExireDate()
    {
        $created = $this->getMeta('created');
        $vip = $this->getMeta('vip');
        if (!$vip) {
            $date = date('Y-m-d', strtotime($created.' + 2 year'));
        } else {
            $date = 'bezterminowy';
        }

        return $date;
    }

    public function setStatus($statusCode)
    {
        upate_post_meta($this->vid, 'vStatus', $statusCode);
    }
}
