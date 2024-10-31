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

//Display the QCaptcha-Form-Element
function qcaptcha_cf7_display($atts = []){
	global $qcaptcha;
	global $qcaptcha_lang;

	$atts = array_change_key_case((array)$atts, CASE_LOWER);

	$attr = shortcode_atts(['lang' => 'none',], $atts);
	
	if($attr['lang'] == "de" || $attr['lang'] == "en" || $attr['lang'] == "nl"){
		$qcaptcha_lang = $attr['lang'];
	}

	//$qcpl = get_option('qcaptcha_protection');
	$qcdfa = get_option('qcaptcha_disable_for_admins');
	$qcdscf7 = get_option('qcaptcha_support_cf7');
	
	//if($qcpl != 5 && $qcpl != 4){
		if(!$qcdfa && $qcdscf7 == 1){
			qcaptcha_register_assets();
			return $qcaptcha->insertCF7Captcha($qcaptcha_lang);
		} else if(!current_user_can('manage_options')){
			qcaptcha_register_assets();
			return $qcaptcha->insertCF7Captcha($qcaptcha_lang);
		}
		
	//}
	return;
}

//Check if the Captcha is solved correctly
function qcaptcha_cf7_check($result, $tag){
	global $qcaptcha;
	
	if(!class_exists('WPCF7_Submission')) {
		return $result;
	}
	$_wpcf7 = ! empty($_POST['_wpcf7']) ? absint($_POST['_wpcf7']) : 0;
	if (empty($_wpcf7)) {
		return $result;
	}

	$submission = WPCF7_Submission::get_instance();
	$data = $submission->get_posted_data();
	//if (empty($data['_wpcf7'])) {
	//	return $result;
	//}
	
	//TODO: Small Security Bug - Simply remove name of captcha element in browser to bypass Captcha - Solution not found yet because wpcf7_add_form_tag not working
	if(!isset($data['qcaptcha'])){
		return $result;
	}

    if(!$qcaptcha->isValid()){
		$result->invalidate('', __("Das Captcha ist ungültig.", "qcaptcha"));
		return $result;
    } else {
        return $result;
    }
}

function qcaptcha_cf7_form_elements($form) {
	$form = do_shortcode($form);
	return $form;
}


add_filter('wpcf7_form_elements', 'qcaptcha_cf7_form_elements');
add_filter('wpcf7_validate', 'qcaptcha_cf7_check', 20, 2);
add_shortcode('qcaptcha_cf7', 'qcaptcha_cf7_display'); 

?>