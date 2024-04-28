<?php
/**
 * The blog template file.
 *
 * @flatsome-version 3.16.0
 */

use JDCustom\voucher\voucherInst;
use JDCustom\templateParts\jdOrderDetails;

$voucher = new voucherInst(get_the_ID());
if (! current_user_can('manage_woocommerce') && get_current_user_id() != $voucher->getCustomerId()) {
    \JDCustom\jdHelpers::accessDenied();
}

$msg = jdOrderDetails::renderInformation($voucher);
get_header();
if ($voucher->getMeta('vStatus') == 1 || $voucher->getMeta('vStatus') == 2) {

    $d     = null;
    $s     = "send";
    $r     = "render";
    $save  = "save";
    $text  = null;
    $saved = null;

} elseif ($voucher->getMeta('vStatus') == 0) {
    $d     = 'disabled';
    $s     = null;
    $r     = null;
    $save  = "save";
    $text  = null;
    $saved = null;
} else {
    $d     = 'disabled';
    $s     = null;
    $r     = null;
    $save  = null;
    $saved = 'disabled';
    $text  = 'disabled';
}

if (! current_user_can('manage_woocommerce')) {

    $url = $voucher->getOrderViewLink();
} else {
    $url = $voucher->getOrderLink();
}
?>
  <script>
    var ajaxurl = '<?php echo admin_url('admin-ajax.php', 'relative'); ?>'

  </script>
  <div id="primary">
    <div id="content" role="main">
      <div class="row">
		  <?php
          while (have_posts()) {
              the_post();

              ?>
        <script>   var customer_email = '<?php echo $voucher->getEmailFromOrdeer(); ?>'</script>
        <div class="jd-container jd-title-container">

          <div class="jd-block" style="width: 100%">
            <div class="jd-heading">
              <h1 style="font-weight: 200; font-style: italic; font-size: 33px;">Szczegóły vouchera</h1>
              <h2 style="color: #ed6516;font-weight: 800;font-size: 32px;width: 100%;"
                  class="section-title section-title-center">
                <b></b>
                <span class="section-title-main"><?php echo $voucher->getVoucherCode(); ?></span>
                <b></b>
              </h2>
            </div>
          </div>
        </div>
        <div class="jd-container">
          <div class="jd-block jd-col-50">
            <div class="jd-details">
              <div class="jd-row">
                <h3>Szczegóły</h3>
              </div>
              <div class="jd-row">
                <div class="jd-left">
                  <span>Status: </span></div>
                <div class="jd-right"> <?php $voucher->renderStatus(); ?>
                </div>
              </div>
              <div class="jd-row">
                <div class="jd-left">

                  <span>Produkt: </span></div>
                <div class="jd-right"> <?php echo $voucher->getItemTitle(); ?> </div>
              </div>
              <div class="jd-row">
                <div class="jd-left">
                  <span>Zamówienie: </span>
                </div>
                <div class="jd-right"><?php echo $url; ?>
                </div>
              </div>

              <div class="jd-row">
                <div class="jd-left">
                  <span>Data zakupu: </span></div>
                <div class="jd-right"><?php echo $voucher->formatDates($voucher->getOrderDate()); ?>
                </div>
              </div>
              <div class="jd-row">
                <div class="jd-left">
                  <span>Data ważności: </span></div>
                <div class="jd-right"><?php echo $voucher->calculteVoucherExireDate(); ?>
                </div>
              </div>

            </div>
          </div>
          <div style="border-left: 2px solid #ed65161a; margin-bottom: 30px" class="jd-block jd-col-50">
            <div class="jd-row">
              <h3>Dedykacja</h3>
            </div>
            <div class="jd-row row-col">
              <div class="is-floating-label">

            <textarea id="dedication" width="100%" name="dedication" rows="4" cols="50"
                      placeholder="Podaj treść dedykacji... (opcjonalnie)"
           <?php echo $text ?> ><?php echo $voucher->convertDedication(); ?></textarea>

              </div>
              <div class="action-buttons">

                <button id="<?php echo $save; ?>" class="button button-small"<?php echo $saved ?>>Zapisz</button>
                <button id="<?php echo $r; ?>" class="button button-small" <?php echo $d ?>>Pobierz</button>
                <button id="<?php echo $s; ?>" class="button button-small" <?php echo $d ?>>Wyślij</button>
              </div>
            </div>
          </div>
			<?php
          } ?>
          <script>


            var $ = jQuery
            document.addEventListener('DOMContentLoaded', function () {
              var toastMixin = Swal.mixin({
                toast: true,
                icon: 'success',
                title: 'General Title',
                animation: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                customClass: {
                  container: 'container-jd',
                  popup: 'popup-jd'
                }
              })

              var save = document.getElementById('save')
              save.addEventListener('click', function (e) {
                e.preventDefault()
                var load = document.getElementById('loading-state')
                load.style.display = 'flex'
                console.log('init')
                var ded = document.getElementById('dedication').value
                ded = ded.replace(/\n\r?/g, '<br>')
                let requestData = {
                  action: 'update_dedication',
                  id: <?php echo get_the_ID(); ?>,
                  dedication: ded
                }
                $.ajax({
                  url: ajaxurl,
                  method: 'post',
                  data: requestData,
                  success: function (data) {
                    load.style.display = 'none'
                    toastMixin.fire({
                      animation: true,
                      icon: 'success',
                      title: 'Zapisano zmiany',
                      timer: 3000
                    })
                  }
                })
              })
              var render = document.getElementById('render')
              render.addEventListener('click', function (e) {
                e.stopPropagation()
                var load = document.getElementById('loading-state')
                load.style.display = 'flex'
                let requestData = {
                  action: 'generatePDF',
                  id: <?php echo get_the_ID(); ?>,
                  code: '<?php echo $voucher->getCode(); ?>'
                }
                $.ajax({
                  url: ajaxurl,
                  method: 'post',
                  data: requestData,
                  xhrFields: {
                    responseType: 'blob'
                  },

                  success: function (data) {
                    var blob = new Blob([data])
                    var link = document.createElement('a')
                    link.href = window.URL.createObjectURL(blob)
                    link.download = requestData.code + '.pdf'
                    document.body.appendChild(link)
                    link.click()
                    document.body.removeChild(link)
                    load.style.display = 'none'
                  }
                }).done(function () {
                  toastMixin.fire({
                    animation: true,

                    icon: 'success',
                    title: 'PDF wygenerowany',
                    timer: 3000
                  })
                })
              })
            })
            document.addEventListener('DOMContentLoaded', function () {
              var send = document.getElementById('send')
              send.addEventListener('click', function (e) {
                e.stopPropagation()

                Swal.fire({
                  title: 'Podaj adres email',
                  input: 'text',
                  inputAttributes: {
                    autocapitalize: 'off'
                  },
                  customClass: { input: 'jd-input-ajax' },
                  showCancelButton: true,
                  confirmButtonText: 'Wyślij',
                  cancelButtonText: 'Anuluj',
                  showLoaderOnConfirm: true,
                  inputValue: customer_email,
                  preConfirm: async (email) => {

                    Swal.fire({
                      title: 'Wysłano!',
                      text: ' Voucher został wysłany na podany adres email',

                      icon: 'success'

                    })
                  }
                })

              })
            })
          </script>
        </div>
        <div class="jd-container">
          <div class="jd-block jd-reservation">
            <div class="jd-col" style="flex:70%">
              <h3><?php echo $msg['title']; ?></h3>
              <div>
				  <?php echo $msg['content']; ?>

              </div>

            </div>
            <div class="jd-col"
                 style="flex: 30%;display: flex;flex-direction: row;justify-content: flex-end;align-items: center;">
				<?php echo $msg['btn'] ?>
            </div>


          </div>
        </div><!-- #content -->
      </div><!-- #primary -->
<?php get_footer();
