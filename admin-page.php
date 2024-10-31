<?php
/**
 * QCaptcha is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * QCaptcha is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with QCaptcha. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    QCaptcha
 * @author     Timo Kössler (https://timokoessler.de)
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, Timo Kössler
 */

defined('ABSPATH') or exit;


if (!is_admin() || !current_user_can('manage_options')) {
    exit;
}

wp_register_style('qcaptchaAdminStyle', plugins_url('assets/css/admin.css', __FILE__ ));
wp_enqueue_style('qcaptchaAdminStyle');
wp_register_style('qcaptchaBootstrap', plugins_url('assets/css/bootstrap.min.css', __FILE__ ));
wp_enqueue_style('qcaptchaBootstrap');
wp_register_script('qcaptchaBootstrapJS', plugins_url('assets/js/bootstrap.min.js', __FILE__ ));
wp_enqueue_script('qcaptchaBootstrapJS');
wp_register_script('qcaptchaAdminJS', plugins_url('assets/js/admin.js', __FILE__ ));
wp_enqueue_script('qcaptchaAdminJS');

add_action('admin_footer_text', 'qcaptcha_admin_footer');
function qcaptcha_admin_footer() {
    echo 'QCaptcha is licensed under the GPL-2.0+ License. Copyright (c) 2019, Timo Kössler (<a href="https://timokoessler.de" target="_blank" rel="noopener">https://timokoessler.de</a>)';
}
add_action('update_footer', 'qcaptcha_admin_footer_ver');
function qcaptcha_admin_footer_ver() {
    return;
}
function qcaptcha_valid_setting($key){
    if($key == 0 || $key == 1){
        return true;
    }
    return false;
}
?>
    <div class="qcaptcha-top">
        <img src="<?php echo plugins_url('assets/img/qcaptcha-head.png', __FILE__ ); ?>">
    </div>
    <?php
        if(isset($_POST['submit']))
        {
            $settings = array ( 'qcaptcha_disable_for_admins', 'qcaptcha_support_cf7', 'qcaptcha_support_wp_forms', 'qcaptcha_support_ff', 'qcaptcha_support_mc', 'qcaptcha_wp_login', 'qcaptcha_wp_register', 'qcaptcha_wp_lostpassword', 'qcaptcha_wp_comment', 'qcaptcha_disable_for_logged_in_users' );

            foreach ($settings as $setting) {
                if(!isset($_POST[$setting])){
                    $_POST[$setting] = 0;
                } else {
                    $_POST[$setting] = 1;
                }
            }
            $error = 0;
            foreach ($settings as $setting) {
                if(isset($_POST[$setting]) && qcaptcha_valid_setting($_POST[$setting])){
                    update_option($setting, sanitize_text_field($_POST[$setting]));
                } else {
                   $error = 1;
                }
            }
            if(!$error){
                echo '<div class="notice notice-success is-dismissible qcaptcha-notice"><p><span class="green-checkmark">&#10003; </span>' . __("Änderungen erfolgreich gespeichert", "qcaptcha") . '</p></div>';		
            } else {
                echo '<div class="notice notice-error is-dismissible qcaptcha-notice"><p>' . __("Es ist ein Fehler beim Speichern der Einstellungen aufgetreten", "qcaptcha") . '</p></div>';
            }
        } 
    ?>
    <div class="qcaptcha-settings">
        <div class="qcaptcha-title"><?php _e("Einstellungen", "qcaptcha"); ?></div>
        <form method="post">
        
            <div class="qcaptcha-plugins-title"><?php _e("Allgemein", "qcaptcha"); ?></div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="qcaptcha_wp_login" id="qcaptcha_wp_login" <?php if(get_option('qcaptcha_wp_login') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_wp_login"><?php _e('QCaptcha beim WP-Login aktivieren', "qcaptcha"); ?></label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="qcaptcha_wp_register" id="qcaptcha_wp_register" <?php if(get_option('qcaptcha_wp_register') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_wp_register"><?php _e('Bei der Registrierung ein Captcha anzeigen', "qcaptcha"); ?></label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="qcaptcha_wp_lostpassword" id="qcaptcha_wp_lostpassword" <?php if(get_option('qcaptcha_wp_lostpassword') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_wp_lostpassword"><?php _e('QCaptcha beim Zurücksetzen des Passwortes einblenden', "qcaptcha"); ?></label>
            </div>
            
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="qcaptcha_wp_comment" id="qcaptcha_wp_comment" <?php if(get_option('qcaptcha_wp_comment') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_wp_comment"><?php _e('Aktiviere QCaptcha für WP-Kommentare', "qcaptcha"); ?></label>
            </div>

            <div class="qcaptcha-plugins-title"><?php _e("Unterstützte Plugins", "qcaptcha"); ?></div>
            <?php
            if(is_plugin_active('contact-form-7/wp-contact-form-7.php')) {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_cf7" name="qcaptcha_support_cf7" <?php if(get_option('qcaptcha_support_cf7') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_support_cf7"><?php _e('Unterstützung für <a href="https://de.wordpress.org/plugins/contact-form-7/" rel="noopener" target="_blank" title="Contact Form 7 installieren">Contact Form 7</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Verwende den Shortcode <code>[qcaptcha_cf7]</code>, um QCaptcha zu einem Contact Form 7-Formular hinzuzufügen", "qcaptcha"); ?></p>
            <?php } else {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_cf7" disabled>
                <label class="custom-control-label" for="qcaptcha_support_cf7"><?php _e('Unterstützung für <a href="https://de.wordpress.org/plugins/contact-form-7/" rel="noopener" target="_blank" title="Contact Form 7 installieren">Contact Form 7</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Verwende den Shortcode <code>[qcaptcha_cf7]</code>, um QCaptcha zu einem Contact Form 7-Formular hinzuzufügen", "qcaptcha"); ?></p>
            <?php update_option('qcaptcha_support_cf7', 0); } ?>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="qcaptcha_support_wp_forms" id="qcaptcha_support_wp_forms" <?php if(get_option('qcaptcha_support_wp_forms') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_support_wp_forms"><?php _e('Unterstützung für <a href="https://de.wordpress.org/plugins/wpforms-lite/" rel="noopener" target="_blank" title="WP Forms Lite installieren">WP Forms Lite</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Du findest QCaptcha als Feld im WPForm-Builder", "qcaptcha"); ?></p>

            <?php if(is_plugin_active('formidable/formidable.php')) {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_ff" name="qcaptcha_support_ff" <?php if(get_option('qcaptcha_support_ff') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_support_ff"><?php _e('Unterstützung für <a href="https://de.wordpress.org/plugins/formidable/" rel="noopener" target="_blank" title="Formidable Forms installieren">Formidable Forms</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Du findest QCaptcha als Feld im Form-Builder", "qcaptcha"); ?></p>
            <?php } else {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_ff" disabled>
                <label class="custom-control-label" for="qcaptcha_support_ff"><?php _e('Unterstützung für <a href="https://de.wordpress.org/plugins/formidable/" rel="noopener" target="_blank" title="Formidable Forms installieren">Formidable Forms</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Du findest QCaptcha als Feld im Form-Builder", "qcaptcha"); ?></p>
            <?php update_option('qcaptcha_support_ff', 0); } ?>


            <?php if(is_plugin_active('mailchimp-for-wp/mailchimp-for-wp.php')) {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_mc" name="qcaptcha_support_mc" <?php if(get_option('qcaptcha_support_mc') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_support_mc"><?php _e('Unterstützung für <a href="https://wordpress.org/plugins/mailchimp-for-wp/" rel="noopener" target="_blank" title="Mailchimp for WordPress installieren">Mailchimp for WordPress</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Verwende den Shortcode <code>[qcaptcha_mc]</code> innerhalb des Formulareditors, um QCaptcha zu einem Mailchimp-Formular hinzuzufügen", "qcaptcha"); ?></p>
            <?php } else {?>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_support_mc" disabled>
                <label class="custom-control-label" for="qcaptcha_support_mc"><?php _e('Unterstützung für <a href="https://wordpress.org/plugins/mailchimp-for-wp/" rel="noopener" target="_blank" title="Mailchimp for WordPress installieren">Mailchimp for WordPress</a> aktivieren', "qcaptcha"); ?></label>
            </div>
            <p><?php _e("Verwende den Shortcode <code>[qcaptcha_mc]</code> innerhalb des Formulareditors, um QCaptcha zu einem Mailchimp-Formular hinzuzufügen", "qcaptcha"); ?></p>
            <?php update_option('qcaptcha_support_mc', 0); } ?>


            <p class="font-16"><span class="green-checkmark">&#10003; </span><?php _e('Unterstützt Login und Registrierung von <a href="https://wordpress.org/plugins/buddypress/" rel="noopener" target="_blank" title="buddyPress installieren">buddyPress</a> und  <a href="https://wordpress.org/plugins/bbpress/" target="_blank" title="bbpress installieren">bbPress</a>', "qcaptcha"); ?></p>

            <div class="qcaptcha-plugins-title"><?php _e("Erweiterte Optionen", "qcaptcha"); ?></div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_disable_for_admins" name="qcaptcha_disable_for_admins" <?php if(get_option('qcaptcha_disable_for_admins') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_disable_for_admins"><?php _e("QCaptcha für Admins deaktivieren", "qcaptcha"); ?></label>
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="qcaptcha_disable_for_logged_in_users" name="qcaptcha_disable_for_logged_in_users" <?php if(get_option('qcaptcha_disable_for_logged_in_users') == 1) { echo "checked"; } ?>>
                <label class="custom-control-label" for="qcaptcha_disable_for_logged_in_users"><?php _e("QCaptcha für eingeloggte Benutzer deaktivieren", "qcaptcha"); ?></label>
            </div>

            <br>
            <button type="submit" name="submit" class="btn btn-primary"><?php _e("Speichern", "qcaptcha"); ?></button>
        </form>
    </div>