<?php

namespace JDCustom;

class jd_admin_page
{
    public function __construct()
    {
        if (is_admin()) { // admin actions
            add_action('admin_menu', [$this, 'jdAdmin']);

            add_action('admin_init', [$this, 'register_jdtools']);
        }
    }

    public function register_jdtools()
    {
        // whitelist options
        register_setting('jd-tools', 'cookie_accept');
        register_setting('jd-tools', 'cookie_more');
        register_setting('jd-tools', 'phone_notice');
        register_setting('jd-tools', 'vat_empty');
        register_setting('jd-tools', 'vat_invalid');
        register_setting('jd-tools', 'company_invalid');
    }

    public function jdAdmin(): void
    {
        add_menu_page('JD Tools', 'JD Tools', 'manage_options', 'jd-tools', [$this, 'jdTools'], 'dashicons-tickets', 6);
    }

    public function jdTools()
    {
        ?>
		<div class="wrap">
			<h2>JD Tools</h2>

			<form method="post" action="options.php">
				<?php
                settings_fields('jd-tools');
        do_settings_sections('jd-tools');
        submit_button();
        ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Przycisk akceptuj</th>
						<td><input type="text" name="cookie_accept" value="<?php echo esc_attr(get_option('cookie_accept')); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">Przycisk więcej</th>
						<td><input type="text" name="cookie_more" value="<?php echo esc_attr(get_option('cookie_more')); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">Treść błędu dot. nr telefonu</th>
						<td><input type="text" name="phone_notice" value="<?php echo esc_attr(get_option('phone_notice')); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">Treść błędu - pusty nip</th>
						<td><input type="text" name="vat_empty" value="<?php echo esc_attr(get_option('vat_empty')); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">Treść błędu - błędny nip</th>
						<td><input type="text" name="vat_invalid" value="<?php echo esc_attr(get_option('vat_invalid')); ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">Treść błędu - błędny nip</th>
						<td><input type="text" name="company_invalid" value="<?php echo esc_attr(get_option('company_invalid')); ?>" /></td>
					</tr>
				</table>
			</form>
			</div>

		<?php
    }
}
