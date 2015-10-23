<?php

/**
 * goo.php
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

class goo extends base_controller {

    function index() {
        xdebug($_GET, 1);
        exit;
    }

    function logout() {
        $this->load->library('yahoo');
        $this->yahoo->logout();
    }

    function get() {
        $this->load->library('yahoo');

        if (!$this->yahoo->logged_in()) {
            $this->yahoo->login();
        } else {
            exit;
            $url = 'https://www.google.com/m8/feeds/contacts/default/full';
            do {
                $result = $this->google->simple_call('get', $url);

                $data = $result->__resp->data;

                foreach ($data->entry as $entry) {
                    $email = $entry->xpath('gd:email/@address');
                    $email = (string) @$email[0]['address'];
                    if (!empty($email)) {
                        xdebug((string)$entry->title, 1);
                        xdebug($email, 1);
                    }
                }
                xdebug('page====');
                $url = '';
                foreach ($data->link as $link) {
                    if ($link['rel'] == 'next') {
                        $url = @$link['href'];
                        break;
                    }
                }
            } while ($url);
//            foreach($data->entry as $entry) {
//                xdebug($entry, 1);
//            }
        }
        exit;
    }

    public function _check_access() {
        return true;
    }

}
