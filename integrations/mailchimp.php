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

//Display Captcha
function qcaptcha_mc_display(){
    global $qcaptcha;
    qcaptcha_register_assets();
    
    return $qcaptcha->insertMCCaptcha();
}

//Valdiate Captcha
function qcaptcha_mc_check($errors){
    global $qcaptcha;

	if(!$qcaptcha->isValid() && isset($_POST['qcaptcha'])){
        $errors[] = 'qcaptcha';
    }
    return $errors;
}

//Registers an additional MailChimp for WP error message
function qcaptcha_mc_error_message($messages) {
    $messages['qcaptcha'] = __("Das Captcha ist ungültig.", "qcaptcha");
    return $messages;
}

add_filter('mc4wp_form_messages', 'qcaptcha_mc_error_message');
add_filter('mc4wp_form_errors', 'qcaptcha_mc_check', 10, 2);
add_shortcode('qcaptcha_mc', 'qcaptcha_mc_display');

?>