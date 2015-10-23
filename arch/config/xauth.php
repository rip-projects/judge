<?php

/**
 * xauth.php
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

$config = array(
    'session_id' => 'xauth.user',
    'schema' => array(''),
);

// HOOK AFTER JOIN AUTH
// $data = array(
//     'message' => $user['first_name'] . ' ' . $user['last_name'] . ' baru aja join ke Kafebrownies. Dapetin hadiah dengan ikutan join di Kafebrownies! ',
//     'picture' => theme_url('img/logo.png'),
//     'link' => base_url(),
//     'caption' => $CI->config->item('page_title'),
//     'name' => $CI->config->item('page_title'),
// );
// $status = $CI->facebook->call('post', 'me/feed', $data);

// $data = array(
//     'activation' => $CI->_model('user')->generate_activation_code($user['id']),
//     'name' => $user['first_name'] . ' ' . $user['last_name'],
// );
// $CI->load->library('xmailer');
// $CI->xmailer->send('activation', $data, $user['email']);
