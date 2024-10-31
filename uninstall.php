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

//This file will delete all Options on uninstall

if(!defined( 'WP_UNINSTALL_PLUGIN')) {
	exit;
}

$settings = array ( 'qcaptcha_disable_for_admins', 'qcaptcha_support_cf7', 'qcaptcha_support_wp_forms', 'qcaptcha_support_ff', 'qcaptcha_support_mc', 'qcaptcha_wp_login', 'qcaptcha_wp_register', 'qcaptcha_wp_lostpassword', 'qcaptcha_wp_comment', 'qcaptcha_disable_for_logged_in_users' );

foreach ($settings as $setting) {
    delete_option($setting);
}

global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "qcaptcha");

?>