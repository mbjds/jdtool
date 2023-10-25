<?php

namespace JDCustom;

class jd_mods {
	public static function add_custom_script_to_wp_head() {?>
		<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                var  accept = "Akceptuj";
                var more = "Więcej...";
                document.getElementsByClassName('flatsome-cookies__accept-btn')[0].innerHTML = '<span> <?php echo get_option('cookie_accept') ?></span>';
                document.getElementsByClassName('flatsome-cookies__more-btn')[0].innerHTML = '<span> <?php echo get_option('cookie_more') ?></span>';
            });

		</script>
		<?php
	}
}