<?php

/**
 * friend.php
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

class friend extends app_crud_controller {
    function invite($key) {
        if ($_POST) {
            $this->load->library('xmailer');
            $this->xmailer->send('invite', array(), $_POST['emails']);
            exit;
        } else {
            $key = strtolower(trim($key));
            switch($key) {
                case 'google':
                    $this->_invite_google();
                    break;
            }
        }
    }
    
    function _invite_google() {
        $this->load->library('google');
        
        $emails = array();

        if (!$this->google->logged_in()) {
            $this->google->login();
        } else {
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=100';
            do {
                $result = $this->google->simple_call('get', $url);

                $data = $result->__resp->data;

                foreach ($data->entry as $entry) {
                    $email = $entry->xpath('gd:email/@address');
                    $email = (string) @$email[0]['address'];
                    if (!empty($email)) {
                        
                        $emails[] = array(
                            'name' => (string)$entry->title,
                            'email' => $email,
                        );
                    }
                }
                $url = '';
                foreach ($data->link as $link) {
                    if ($link['rel'] == 'next') {
                        $url = @$link['href'];
                        break;
                    }
                }
            } while ($url);
        }
        
        $this->_data['emails'] = $emails;
    }
}
