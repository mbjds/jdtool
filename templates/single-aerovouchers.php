<?php
/**
 * The blog template file.
 *
 * @flatsome-version 3.16.0
 */
get_header();

?>

  <div id="primary">
    <div id="content" role="main">
      <div class="row">
		  <?php
          while (have_posts()) {
              the_post();
              $voucher = new \JDCustom\voucher\voucherInst(get_the_ID());
            echo  $voucher->getVoucherCode();
        //      echo get_post_meta(get_the_ID(), 'vStatus', true);
          } // End of the loop.?>
      </div>
    </div><!-- #content -->
  </div><!-- #primary -->
<?php get_footer();