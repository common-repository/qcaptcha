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

jQuery(document).ready(function(){

    if (document.getElementById('qcaptcha')){

        if(jQuery("#qcaptcha").data('lang')){
            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajaxurl + "?lang=" + jQuery("#qcaptcha").data('lang'),
                data: {
                action: 'get_QCaptcha'
                },
                success: function (result) {
                    jQuery("#qcaptcha").removeClass("qcaptcha-loading");
                    jQuery("#qcaptcha").html(result);
                }
            });
            jQuery(".wpcf7").on('wpcf7:invalid', function(event){
                jQuery("#qcaptcha").html("");
                jQuery("#qcaptcha").addClass("qcaptcha-loading");
                jQuery.ajax({
                    type: "POST",
                    url: ajax_object.ajaxurl + "?lang=" + jQuery("#qcaptcha").data('lang'),
                    data: {
                    action: 'get_QCaptcha'
                    },
                    success: function (result) {
                        jQuery("#qcaptcha").removeClass("qcaptcha-loading");
                        jQuery("#qcaptcha").html(result);
                    }
                });
              });
        } else {
            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajaxurl,
                data: {
                action: 'get_QCaptcha'
                },
                success: function (result) {
                    jQuery("#qcaptcha").removeClass("qcaptcha-loading");
                    jQuery("#qcaptcha").html(result);
                }
            });
    
            jQuery(".wpcf7").on('wpcf7:invalid', function(event){
                jQuery("#qcaptcha").html("");
                jQuery("#qcaptcha").addClass("qcaptcha-loading");
                jQuery.ajax({
                    type: "POST",
                    url: ajax_object.ajaxurl,
                    data: {
                    action: 'get_QCaptcha'
                    },
                    success: function (result) {
                        jQuery("#qcaptcha").removeClass("qcaptcha-loading");
                        jQuery("#qcaptcha").html(result);
                    }
                });
              });
        }

        
    } else if (document.getElementById('qcaptcha-wp-forms')){
        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajaxurl,
            data: {
            action: 'get_QCaptchaWPForms'
            },
            success: function (result) {
                jQuery("#qcaptcha-wp-forms").removeClass("qcaptcha-loading");
                jQuery("#qcaptcha-wp-forms").html(result);
            }
        });
    }

});
