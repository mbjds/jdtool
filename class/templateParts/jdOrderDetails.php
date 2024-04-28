<?php

namespace JDCustom\templateParts;

use JDCustom\order\wcOverride;
use JDCustom\voucher\voucherInst;

class jdOrderDetails
{


    public function renderVouchersTable($order): void
    {
        $ids = wcOverride::getVouchersFromOrder($order);

        ?>
      <h2 class="woocommerce-order-details__title">Vouchery przypisane do zamówienia</h2>
      <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

        <thead>
        <tr>
          <th class="woocommerce-table__product-name product-name">Produkt</th>
          <th class="woocommerce-table__product-table product-code">Kod</th>
          <th class="woocommerce-table__product-table product-status">Status</th>
          <th class="woocommerce-table__product-table product-actions"></th>
        </tr>
        </thead>

        <tbody>

		<?php
        foreach ($ids as $id) {
            $voucher = new voucherInst($id);
            ?>
          <tr class="woocommerce-table__line-item order_item">
            <td class="woocommerce-table__product-name product-name">
				<?php echo $voucher->getItemTitle(); ?>
            </td>

            <td class="woocommerce-table__product-code product-code">
				<?php echo $voucher->getCode() ?>
            </td>
            <td class="woocommerce-table__product-status product-status">    <?php $voucher->renderStatus() ?>
            </td>
            <td class="woocommerce-table__pproduct-actions product-actions">
              <a href="<?php echo get_permalink($id) ?>"><i style="font-size: 22px; padding: 0 7px"
                                                              class="rTrue editV  fa-regular  fa-pen-to-square"></i></a>
            </td>
          </tr>
			<?php
        }
        ?>
        </tbody>


      </table>
		<?php

    }


    /**
     * @param $voucher voucherInst
     *
     * @return array
     */
    public static function renderInformation(voucherInst $voucher): array
    {
        $msg = [];
        if ($voucher->getMeta('vStatus') == 0) {
            $msg['title']   = 'Przetważamy Twoją płatnośc!';
            $msg['content'] = 'Po zaksięgowaniu płatności voucher zmieni status na "Aktywny", ale już teraz możesz go spersonalizować o własną dedykację. Po aktywacji otrzymasz wiadomość e-mail voucherem. Do momentu zrealizwania rezerwacji możesz tu wrócić i dokonać edycji, pobrać czy wyslać plik na maila (swojego lub dowolnej osoby)';
            $msg['btn']     = false;
        } elseif ($voucher->getMeta('vStatus') == 1) {
            $msg['title']   = 'Zarezerwuj wizytę już teraz!';
            $msg['content'] = 'Pozostał tylko jeden krok aby przeżyć niezapomnianą przygodę w jednym z naszych tuneli. Wybierz dogodną dla Ciebie datę oraz jedna z trzech lokalizacji.';
            $msg['btn']     = '<button class="jd-button jd-button-primary" id="jd-reservation">Rezrwacja</button>';

        } elseif ($voucher->getMeta('vStatus') == 2) {
            $msg['title']   = 'Przyjelismy Twoją rezerwację!';
            $msg['content'] = 'Zapraszymy serdeczmie';
            $msg['btn']     = '';
        } else {
            $msg['title']   = 'Voucher został anulowany... :(';
            $msg['content'] = 'Być może stracił ważność lub został anulowany przez naszego pracownika na Towją prośbę. Ale nic straconego! Wystaczy złożyc nowe zamówienie!';
            $msg['btn']     = '<button class="jd-button jd-button-primary" id="jd-order">Zamów</button>';
            ;
        }

        return $msg;

    }

}
