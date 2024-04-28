<?php

namespace JDCustom\templateParts;

class jdOrdersAdmin
{


    public function __construct()
    {
        add_action('add_meta_boxes', [ $this, 'jdOrderAdmin_meta_box' ]);
    }

    public function jdOrderAdmin_meta_box()
    {
        add_meta_box(
            'custom_box',
            'Vouchery dla zamówienia',
            [ $this, 'jd_vouchers_list' ],
            'shop_order',
            'advanced',
            'high'
        );
    }

    public function jd_vouchers_list()
    {
        $args  = array(
            'meta_key'       => 'order_id',
            'meta_value'     => get_the_ID(),
            'post_type'      => 'aerovouchers',
            'post_status'    => 'any',
            'posts_per_page' => - 1
        );
        $posts = get_posts($args);
        ?>
      <table class="wp-list-table widefat fixed striped table-view-list posts" cellspacing="0">
        <thead>
        <tr>
          <th style="width:50%">Produkt</th>
          <th style="width:15%">Kod</th>
          <th style="width:25%">Status</th>
          <th style="width:2%; text-align: right"></th>
        </tr>
        </thead>
        <tbody>
		<?php
        foreach ($posts as $post) {
            $v = new \JDCustom\voucher\voucherInst($post->ID);
            ?>
          <tr>
            <td><?php echo $v->getItemTitle(); ?></td>
            <td><?php echo $v->getCode(); ?></td>
            <td><?php $v->renderStatus(); ?></td>
            <td style="text-align: right"><a href="<?php echo get_permalink($post) ?>"><i
                  style="font-size: 22px; padding: 0 7px"
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


}
