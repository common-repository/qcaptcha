<?php
/**
 * Plugin Name: QCaptcha
 * Plugin URI:  https://timokoessler.de/qcaptcha/
 * Description: QCaptcha ist eine Anti-Spam Lösung für Webmaster, die sehgeschädigte Menschen nicht ausgrenzt und auf Datenschutz achtet
 * Version:     1.0.2
 * Author:      Timo Kössler
 * Author URI:  https://timokoessler.de/
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: qcaptcha
 *
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

include( plugin_dir_path( __FILE__ ) . 'Captcha.php');
$qcaptcha = new QCaptcha;

//Create required Options
add_option('qcaptcha_disable_for_admins', 0);
add_option('qcaptcha_support_cf7', 0);
add_option('qcaptcha_support_wp_forms', 0);
add_option('qcaptcha_support_ff', 0);
add_option('qcaptcha_support_mc', 0);
add_option('qcaptcha_wp_login', 1);
add_option('qcaptcha_wp_register', 1);
add_option('qcaptcha_wp_lostpassword', 1);
add_option('qcaptcha_wp_comment', 0);
add_option('qcaptcha_disable_for_logged_in_users', 0);

//Default display function
function qcaptcha_display(){
    global $qcaptcha;
    qcaptcha_register_assets();
    $qcaptcha->insertCaptcha();
}

function qcaptcha_register_assets(){
	wp_register_style('qcaptchaStyle', plugins_url('assets/css/captcha.css', __FILE__ ));
	wp_enqueue_style('qcaptchaStyle');
	wp_register_script('qcaptchaScript', plugins_url('assets/js/qcaptcha-ajax.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_script('qcaptchaScript');
	wp_localize_script('qcaptchaScript', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

function admin_menu() {
	add_menu_page('QCaptcha', 'QCaptcha', 'manage_options', 'qcaptcha-settings', 'admin_page', plugins_url('assets/img/qcaptcha-admin.png', __FILE__ ), 98);
}
function admin_page(){
	include_once "admin-page.php";
}

function load_qcaptcha_textdomain() {
    load_plugin_textdomain('qcaptcha', FALSE, basename( dirname( __FILE__ ) ) . '/languages/');
}

function qcaptcha_action_links($actions){
	$custom_actions = array(
		'setings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=qcaptcha-settings' ), __( 'Einstellungen', 'qcaptcha' ) ),
	);

	return array_merge( $custom_actions, $actions );
}

//Load all active necessary Integrations
function qcaptcha_init(){

	if(get_option('qcaptcha_wp_login')){
		include_once "integrations/wp-login.php";
	}
	if(get_option('qcaptcha_wp_register')){
		include_once "integrations/wp-register.php";
	}
	if(get_option('qcaptcha_wp_lostpassword')){
		include_once "integrations/wp-lostpassword.php";
	}
	if(get_option('qcaptcha_disable_for_logged_in_users') && is_user_logged_in() || get_option('qcaptcha_disable_for_admins') && current_user_can('manage_options')){
		if(!is_admin()){
			return;
		}
	}
	if(get_option('qcaptcha_wp_comment')){
		include_once "integrations/wp-comments.php";
	}
	if(get_option('qcaptcha_support_cf7') && function_exists('wpcf7_contact_form')){
		include_once "integrations/contact-form-7.php";
	}
	if(get_option('qcaptcha_support_wp_forms') && defined('WPFORMS_VERSION')){
		include_once "integrations/wpforms.php";
	}
	if(get_option('qcaptcha_support_ff')){
		include_once "integrations/formidable-forms.php";
	}
	if(get_option('qcaptcha_support_mc')){
		include_once "integrations/mailchimp.php";
	}
	if(class_exists('BuddyPress') && get_option('qcaptcha_support_bp')){
		include_once "integrations/buddypress.php";
	}
}

function qcaptcha_activation(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'qcaptcha';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$charset_collate = $wpdb->get_charset_collate();	
		$sql = "CREATE TABLE $table_name (
			id varchar(200) NOT NULL,
			answer tinytext NOT NULL,
			time datetime NOT NULL,
			PRIMARY KEY  (id)
		  ) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}
	if (!wp_next_scheduled('qcaptcha_clean_db')) {
		wp_schedule_event(time(), 'twicedaily', 'qcaptcha_clean_db');
	}
}

function qcaptcha_clean_db() {
	global $wpdb;

	$now = new DateTime();
	$back = $now->sub(DateInterval::createFromDateString('3600 seconds'));
	$date = $back->format('Y-m-d H:i:s');
	$result = $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'qcaptcha WHERE time  < \'' . $date . '\'');
}

function qcaptcha_ajax_get(){
	global $qcaptcha;
	global $qcaptcha_subtract_sign;
    global $qcaptcha_sum_sign;
    global $qcaptcha_question_beginning;
	global $qcaptcha_question_beginning_double;
		
	if(isset($_GET['lang']) && !empty($_GET['lang'])){
		echo $qcaptcha->getCaptchaWithLang($_GET['lang']);
		wp_die();
	}
	echo $qcaptcha->getCaptcha();
	wp_die();
}

function qcaptcha_ajax_get_wp_forms(){
	global $qcaptcha;
	echo $qcaptcha->getWPFormsCaptcha();
	wp_die();
}

function qcaptcha_deactivation(){
	wp_clear_scheduled_hook('qcaptcha_clean_db');
}

add_action('plugins_loaded', 'load_qcaptcha_textdomain');
add_action('admin_menu', 'admin_menu');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'qcaptcha_action_links' );
add_action('init', 'qcaptcha_init');

register_activation_hook(__FILE__, 'qcaptcha_activation');
register_deactivation_hook(__FILE__, 'qcaptcha_deactivation');

add_action('wp_ajax_get_QCaptcha', 'qcaptcha_ajax_get');
add_action('wp_ajax_nopriv_get_QCaptcha', 'qcaptcha_ajax_get');
add_action('wp_ajax_get_QCaptchaWPForms', 'qcaptcha_ajax_get_wp_forms');
add_action('wp_ajax_nopriv_get_QCaptchaWPForms', 'qcaptcha_ajax_get_wp_forms');
add_action('qcaptcha_clean_db', 'qcaptcha_clean_db');

?>