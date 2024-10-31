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
function qcaptcha_wpforms_display(){
    global $qcaptcha;
    wp_register_style('qcaptchaStyle', plugins_url('../assets/css/captcha-wp-forms.css', __FILE__ ) );
    wp_enqueue_style('qcaptchaStyle');
    wp_register_script('qcaptchaScript', plugins_url('../assets/js/qcaptcha-ajax.js', __FILE__), array('jquery'), null, true);
	wp_enqueue_script('qcaptchaScript');
	wp_localize_script('qcaptchaScript', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    $qcaptcha->insertWPFormsCaptcha();
}

function qcaptcha_wpforms_check($field_id, $field_submit, $form_data){
    global $qcaptcha;
    $form_id = $form_data['id'];
    $fields  = $form_data['fields'];
    
    if(!$qcaptcha->isValid()){
        wpforms()->process->errors[$form_id][$field_id] = __("Das Captcha ist ungültig.", "qcaptcha");
		return;
    } else {
        return;
    }
}

class QCaptcha_WPForms_Field extends WPForms_Field
{
    public function init()
    {
        $this->name = 'QCaptcha';
        $this->type = 'qcaptcha';
        $this->icon = 'fa-shield';
        $this->order = 99;
        $this->defaults = array(
            0 => array(
                'label' => __('QCaptcha', 'qcaptcha'),
            )
        );
    }

    public function field_options($field){
        _e("Es sind keine Optionen verfügbar.", "qcaptcha");

    }

    public function field_preview($field)
    {
        _e("Keine Vorschau verfügbar: Hier wird das Captcha später angezeigt werden.", "qcaptcha");
    }

    public function field_display($field, $field_atts, $form_data)
    {
        qcaptcha_wpforms_display();
    }
}

new QCaptcha_WPForms_Field();
//add_filter('wpforms_process', 'qcaptcha_wpforms_check');
add_action('wpforms_process_validate_qcaptcha', 'qcaptcha_wpforms_check', 10, 3);

?>