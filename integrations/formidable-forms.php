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
function qcaptcha_formidable_forms_display(){
	global $qcaptcha;
    qcaptcha_register_assets();
    $qcaptcha->insertCaptcha();
}

function qcaptcha_check_formidable_forms($errors, $posted_field, $posted_value){
	if ($posted_field->type == 'ffqcaptcha'){
		global $qcaptcha;
		if(!$qcaptcha->isValid()){
			$errors['field' . $posted_field->id] = __('<span style="color:#c0392b;">Das Captcha ist ungültig.</span>', "qcaptcha");
		}
	}
	return $errors;
}

function qcaptcha_formidable_forms_add_field($fields){
	$fields['ffqcaptcha'] = __('QCaptcha', 'qcaptcha');
	return $fields;
}

function qcaptcha_formidable_forms_field_settings($field_data){
    qcaptcha_register_assets();
    if($field_data['type'] == 'ffqcaptcha') {
        $field_data['name'] = '';
    }
    return $field_data;
}

//Add Qcaptcha to the Admin Preview
function qcaptcha_formidable_forms_show_admin_field($field){
    if($field['type'] != 'ffqcaptcha') {
        return;
    }
    _e('Keine Vorschau verfügbar: Hier wird das Captcha später angezeigt werden.', 'qcaptcha');
}

//Add Qcaptcha to the Form
function qcaptcha_formidable_forms_qcaptcha_display( $field, $field_name, $atts ) {
  if($field['type'] != 'ffqcaptcha') {
    return;
  }
  qcaptcha_formidable_forms_display();
}


add_filter('frm_available_fields', 'qcaptcha_formidable_forms_add_field');
add_filter('frm_before_field_created', 'qcaptcha_formidable_forms_field_settings');
add_action('frm_display_added_fields', 'qcaptcha_formidable_forms_show_admin_field');
add_action('frm_form_fields', 'qcaptcha_formidable_forms_qcaptcha_display', 10, 3);	
add_filter('frm_validate_field_entry', 'qcaptcha_check_formidable_forms', 10, 3 );	

?>