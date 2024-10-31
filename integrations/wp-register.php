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

//Check if the Captcha is solved correctly
function qcaptcha_register_check($allow){
    global $qcaptcha;
    if(!$qcaptcha->isValid()){
        return new WP_Error( 'broke', __("Das Captcha ist ungültig.", "qcaptcha") );
    } else {
        return $allow;
    }
}

add_filter('register_form', 'qcaptcha_display');
add_filter('registration_errors', 'qcaptcha_register_check', 10, 1);

?>