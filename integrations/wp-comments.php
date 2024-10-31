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

function qcaptcha_comment_check($comment){
    global $qcaptcha;
    if(!$qcaptcha->isValid()){
        //TODO Better Solution which does not clear the form - If you have any ideas?
		wp_die('<span style="font-size: 18px;"><span style="color: #c0392b;font-weight: 900;font-size: 20px;">&#215; </span>' . __("Das Captcha ist ungültig.", "qcaptcha") . '</span>');
    } else {
        return $comment;
    }
}


add_filter('comment_form_after_fields', 'qcaptcha_display');
add_filter('comment_form_logged_in_after', 'qcaptcha_display');
add_filter('pre_comment_on_post', 'qcaptcha_comment_check');


?>