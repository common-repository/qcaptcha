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

class QCaptcha {

    function __construct(){
        $db = $this->getdb();
        global $qcaptcha_lang;
        global $qcaptcha_subtract_sign;
        global $qcaptcha_sum_sign;
        global $qcaptcha_question_beginning;
        global $qcaptcha_question_beginning_double;
        $qcaptcha_lang = $this->getLanguage();
        $qcaptcha_subtract_sign = $this->getBasic('subtract_sign');
        $qcaptcha_sum_sign = $this->getBasic('sum_sign');
        $qcaptcha_question_beginning = $this->getBasic('question_beginning');
        $qcaptcha_question_beginning_double = $this->getBasic('question_beginning_double');

    }

    private function getdb(){
        return new SQLite3(__DIR__ . "/data/Questions.sqlite");
    }

    private function getNewQuestion(){
        $db = $this->getdb();
        global $qcaptcha_lang;
        global $qcaptcha_subtract_sign;
        global $qcaptcha_sum_sign;
        global $qcaptcha_question_beginning;
        global $qcaptcha_question_beginning_double;
        $qcaptcha_subtract_sign = $this->getBasic('subtract_sign');
        $qcaptcha_sum_sign = $this->getBasic('sum_sign');
        $qcaptcha_question_beginning = $this->getBasic('question_beginning');
        $qcaptcha_question_beginning_double = $this->getBasic('question_beginning_double');

        $random = rand(1, 4);
        if($random <= 2){
            $id =  $db->querySingle('SELECT id FROM questions_' . $qcaptcha_lang . ' ORDER BY RANDOM() LIMIT 1;');
            $question['question'] =  $db->querySingle('SELECT question FROM questions_' . $qcaptcha_lang . ' WHERE id =\'' . $id . '\'');
            $question['answer'] =  $db->querySingle('SELECT answer FROM questions_' . $qcaptcha_lang . ' WHERE id =\'' . $id . '\'');
            $db->close();
        } else if($random == 3){
            if(rand(1, 2) == 1){
                $number = rand(9, 20);
                $question['question'] = $qcaptcha_question_beginning . " " . $this->num2text($number);
                $number2 = rand(1, 20);
                if($number2 > $number){
                    $number2 = $number2 - $number;
                }
                $question['question'] =  $question['question'] . " " . $qcaptcha_subtract_sign . " " . $this->num2text($number2) . "?";
                $question['answer'] = $this->num2text($number - $number2);
            } else {
                $number = rand(2, 10);
                $question['question'] = $qcaptcha_question_beginning . " " . $this->num2text($number);
                $number2 = rand(1, 10);
                $question['question'] =  $question['question'] . " " . $qcaptcha_sum_sign . " " .  $this->num2text($number2) . "?";
                $question['answer'] = $this->num2text($number + $number2);
            }
        } else if($random == 4){
            $number = rand(2, 10);
            $question['question'] = $qcaptcha_question_beginning_double . " " . $this->num2text($number) . "?";
            $question['answer'] = $this->num2text($number*2);
        }
       
        return $question;
    }

    private function getQuestion(){
        global $wpdb;

        $question = $this->getNewQuestion();
        $key  = hash('sha512', time() . rand(8, 18));
        
        $wpdb->insert( 
            $wpdb->prefix.'qcaptcha', 
            array( 
                'id' => $key, 
                'answer' =>  $question['answer'],
                'time' => date('Y-m-d H:i:s')
            ), 
            array( 
                '%s', 
                '%s',
                '%s'
            ) 
        );
        $question['key'] = $key;
        return $question;
    }

    private function num2text($number){
        $db = $this->getdb();
        global $qcaptcha_lang;
        return $db->querySingle('SELECT name FROM numbers_' . $qcaptcha_lang . ' WHERE id = \'' . $number . '\'');
    }

    private function getLanguage(){
        if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            return 'en';
        }
        $acceptedLanguages = array ('de', 'en', 'nl'); //Languages which exists in the Database
        $array = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $language = 'en'; //Default Language
        foreach($array as $lang){
            if(strpos($lang, ';')){
                $parts = explode(';', $lang);
                $lang = $parts[0];
             }
            if(strpos($lang, '-')){
                $parts = explode('-', $lang);
                $lang = $parts[0];
             }
             if(strpos($lang, '_')){
                 $parts = explode('_', $lang);
                 $lang = $parts[0];
             }
             if(in_array($lang, $acceptedLanguages)){
                 $language = $lang;
                 break;
             }
        }
        return $language;
    }

    public function getBasic($basic){
        $db = $this->getdb();
        global $qcaptcha_lang;
        return $db->querySingle('SELECT value FROM basic_' . $qcaptcha_lang . ' WHERE key = \'' . $basic . '\'');
    }


    public function insertCaptcha(){                
        echo '<div id="qcaptcha" class="qcaptcha-loading"></div>';
    }
    public function getCaptcha(){                
        $question = $this->getQuestion();
        return '<input type="hidden" name="qcaptcha-key" value="' . htmlspecialchars($question['key']) . '">
        <div class="qcaptcha-question" tabindex="0"><span class="sr-only">' . $this->getBasic("captcha_title") . ' </span>' . htmlspecialchars($question['question']) . '</div>
        <label for="qcaptcha">' . $this->getBasic("answer_label") . '</label>
        <input type="text" autocomplete="off" class="qcaptcha-answer" name="qcaptcha" placeholder="' . $this->getBasic("answer_box") . '" required>';
    }
    public function getCaptchaWithLang($lang) {
        global $qcaptcha_lang;
        $qcaptcha_lang = $lang;
        $question = $this->getQuestion();
        return '<input type="hidden" name="qcaptcha-key" value="' . htmlspecialchars($question['key']) . '">
        <div class="qcaptcha-question" tabindex="0"><span class="sr-only">' . $this->getBasic("captcha_title") . ' </span>' . htmlspecialchars($question['question']) . '</div>
        <label for="qcaptcha">' . $this->getBasic("answer_label") . '</label>
        <input type="text" autocomplete="off" class="qcaptcha-answer" name="qcaptcha" placeholder="' . $this->getBasic("answer_box") . '" required>';
    }

    public function insertWPFormsCaptcha(){                
        echo '<div id="qcaptcha-wp-forms" class="qcaptcha-loading"></div>';
    }

    public function getWPFormsCaptcha(){                
        $question = $this->getQuestion();
        
        return '<input type="hidden" name="qcaptcha-key" value="' . htmlspecialchars($question['key']) . '">
        <div id="qcaptcha-question" tabindex="0"><span id="sr-only">' . $this->getBasic("captcha_title") . ' </span>' . htmlspecialchars($question['question']) . '</div>
        <label for="qcaptcha">' . $this->getBasic("answer_label") . '</label>
        <input type="text" autocomplete="off" id="qcaptcha-answer" name="qcaptcha" placeholder="' . $this->getBasic("answer_box") . '" required>';
    }

    public function insertCF7Captcha($lang){
        return '<div id="qcaptcha" class="qcaptcha-loading" data-lang="' . $lang . '"></div><span class="wpcf7-form-control-wrap qcaptcha-invalid"></span>';
    }

    public function insertMCCaptcha(){
        return '<div id="qcaptcha" class="qcaptcha-loading"></div>';
    }


    public function isValid(){
        global $wpdb;

        if(!isset($_POST['qcaptcha']) || empty($_POST['qcaptcha'])){
            return false;
        }
        if(!isset($_POST['qcaptcha-key']) || empty($_POST['qcaptcha-key'])){
            return false;
        }

        $ca = $wpdb->get_var( $wpdb->prepare("
                SELECT answer 
                FROM " . $wpdb->prefix . "qcaptcha 
                WHERE id = %s", 
           $_POST['qcaptcha-key']
        ));
    
        if(strpos($ca, ';')){
            $parts = explode(';', $ca);
            $found = 0;
            foreach ($parts as $part) { 
                if(strcasecmp($_POST['qcaptcha'], strip_tags(trim($part))) == 0){
                    $found = 1;
                }
            }
            if($found == 1){
               $wpdb->delete($wpdb->prefix . 'qcaptcha', array('id' => sanitize_text_field($_POST['qcaptcha-key'])), array('%s') );
               do_action('qcaptcha_is_valid');
               return true;
            } else {
                $wpdb->delete($wpdb->prefix . 'qcaptcha', array('id' => sanitize_text_field($_POST['qcaptcha-key'])), array('%s') );
               do_action('qcaptcha_is_invalid');
               return false;
            }
        } else {
            if(strcasecmp($_POST['qcaptcha'], strip_tags(trim($ca))) == 0){
                $wpdb->delete($wpdb->prefix . 'qcaptcha', array('id' => sanitize_text_field($_POST['qcaptcha-key'])), array('%s') );
                do_action('qcaptcha_is_valid');
                return true;
            } else {
                $wpdb->delete($wpdb->prefix . 'qcaptcha', array('id' => sanitize_text_field($_POST['qcaptcha-key'])), array('%s') );
                do_action('qcaptcha_is_invalid');
                return false;
            }
        }

    }
}

?>