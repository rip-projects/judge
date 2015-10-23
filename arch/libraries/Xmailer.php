<?php

/**
 * Xmailer.php
 *
 * @package     arch-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

class Xmailer {

    var $name = '';
    var $from = '';
    var $to = '';
    var $debug = false;

    function __construct($params = array()) {
        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
        $CI = &get_instance();
        $user = $CI->_get_user();
        if (empty($this->to) && !empty($user['email'])) {
            $this->to = array($user['email']);
        } elseif (!is_array($this->to)) {
            $this->to = explode(',', $this->to);
        }
        $CI->load->library('email', array('mailtype' => 'html'));
    }

    function send($template_view, $data = '', $to = '') {
        $data['CI'] = $CI = &get_instance();
        $view = $CI->load->view('mail_templates/' . $template_view, $data, true);
        $view = explode("\n", $view, 2);

        $CI->email->from($this->from, $this->name);
        
        if (!empty($to)) {
            if (is_array($to)) {
                $this->to = $to;
            } else {
                $this->to = explode(',', $to);
            }
        }
        
        $CI->email->to($this->to);

        $CI->email->subject($view[0]);
        $CI->email->message($view[1]);
        $CI->email->send();
        if ($this->debug) {
            echo $CI->email->print_debugger();
        }
    }

}
