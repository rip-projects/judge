<?php

/**
 * Xauth.php
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

class Xauth {

    var $session_id = 'xauth.user';
    var $schema = array('ldap', '');
    var $hook_after_register = array();

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
    }

    function login($login, $password, $mode = '') {
        if (empty($mode) && (empty($login) || empty($password))) {
            return false;
        }

        if (empty($mode)) {
            $modes = $this->schema;
        } else {
            $modes = array($mode);
        }

        foreach ($modes as $mode) {
            $user = $this->try_login($login, $password, $mode);
            if (!empty($user)) {
                $this->set_user($user);
                return true;
            }
        }

        return false;
    }

    function try_login($login, $password, $mode) {
        $CI = &get_instance();
        $method = (empty($mode)) ? 'login' : 'login_' . $mode;

        if ($mode == 'facebook') {
            $CI->load->library('facebook');
            if (!$CI->facebook->logged_in()) {
                $continue = $CI->_get_redirect();
                $CI->session->set_userdata('continue', $continue);
                $CI->facebook->login();
                exit;
            } else {
                $user_sso = $CI->facebook->user();
                $pic_types = array('small', 'normal', 'large', 'square');
                foreach ($pic_types as $pic_type) {
                    @mkdir('data/user/image/' . $pic_type, 0777, true);
                    fork('wget "http://graph.facebook.com/' . $user_sso->id . '/picture?type=' . $pic_type . '" -O "./data/user/image/' . $pic_type . '/fb_' . $user_sso->id . '"');
                }

                $data = array(
                    'sso_facebook' => $user_sso->id,
                    'email' => $user_sso->email,
                    'image' => 'user/image/fb_' . $user_sso->id,
                );

                if (@$user_sso->username != NULL) {
                    $data['username'] = $user_sso->username;
                }
                if (@$user_sso->first_name != NULL) {
                    $data['first_name'] = $user_sso->first_name;
                }
                if (@$user_sso->last_name != NULL) {
                    $data['last_name'] = $user_sso->last_name;
                }
                if (@$user_sso->location != NULL && $user_sso->location->name != NULL) {
                    $data['address'] = $user_sso->location->name;
                }
                if (@$user_sso->gender != NULL) {
                    $data['gender'] = $user_sso->gender;
                }
                if (@$user_sso->timezone != NULL) {
                    $data['timezone'] = $user_sso->timezone;
                }
                if (@$user_sso->locale != NULL) {
                    $data['locale'] = $user_sso->locale;
                }
                if (@$user_sso->birthday != NULL) {
                    $b = $user_sso->birthday;
                    $e = explode('/', $b);
                    $data['dob'] = @$e[2] . '-' . @$e[0] . '-' . @$e[1];
                }

                $user = $CI->_model('user')->$method($user_sso->id);
                if (!empty($user)) {
                    if (empty($user['status'])) {
                        redirect('user/denied');
                        exit;
                    }
                    $CI->_model('user')->save($data, $user['id']);
                    foreach ($data as $key => $value) {
                        $user[$key] = $value;
                    }
                    return $user;
                } else {
                    if (@$user_sso->username == NULL) {
                        $data['username'] = uniqid();
                    }
                    // FIXME reekoheek to get user by email
                    // username will always be overrided as above
                    $data['password'] = uniqid();
                    $id = $CI->_model('user')->save($data);
                    $CI->_model('user')->add_user_role($id, 2);
                    
					$user = $CI->_model('user')->$method($user_sso->id);

                    if (!empty($this->hook_after_register)) {
                        foreach($this->hook_after_register as $cb) {
                            call_user_func($cb, $user);
                        }
                    }

                    return $user;
                }
            }
        } else if ($mode == 'twitter') {
            $CI->load->library('tweet');
            if (!$CI->tweet->logged_in()) {
                $CI->tweet->login();
                exit;
            } else {
                $user_sso = $CI->tweet->call('get', 'account/verify_credentials');

                $user = $CI->_model('user')->$method($user_sso->id);
                if (!empty($user)) {
                    return $user;
                }

                $name = explode(' ', $user_sso->name);
                if (empty($name)) {
                    $name = $user_sso->screen_name;
                }

                // calback
                $data = array(
                    'sso_twitter' => $user_sso->id,
                    'username' => uniqid(),
                    'password' => '',
                    'first_name' => $name[0],
                    'last_name' => $name[count($name) - 1],
                );
                $CI->_model('user')->save($data);
                return $CI->_model('user')->$method($user_sso->id);
            }
        }

        return $CI->_model('user')->$method($login, $password);
    }

    function logout() {
        $this->set_user(null);
    }

    function is_login() {
        $user = $this->get_user();
        return $user['is_login'];
    }

    function set_user($user) {
        $user = (array) $user;
        $CI = &get_instance();
        if (empty($user)) {
            $login = $CI->session->userdata($this->session_id);
            if (@$login['login_mode'] == 'facebook') {
                $CI->load->library('facebook');
                if ($CI->facebook->logged_in()) {
                    $CI->facebook->logout();
                }
            } else if (@$login['login_mode'] == 'twitter') {
                $CI->load->library('twitter');
                if ($CI->twitter->logged_in()) {
                    $CI->twitter->logout();
                }
            }

            $user = array();
            if ($CI->input->is_cli_request()) {
                $user['login'] = $user['username'] = 'cli';
            } else {
                $user['login'] = $user['username'] = 'guest';
            }
            $user['id'] = '0';
            $CI->_model('user')->_fetch_user_data($user);
            $user['is_login'] = false;

            $CI->session->sess_destroy();
            $CI->session->sess_create();

        } else {
            $user['is_login'] = true;
        }
        $CI->session->set_userdata($this->session_id, $user);
        
        return $user;
    }

    function get_user($refetch = false) {
        $CI = &get_instance();
        $user = $CI->session->userdata($this->session_id);

        if ($refetch) {
            $user = $CI->_model('user')->refetch_user($user);
            $user['is_login'] = true;
            $CI->session->set_userdata($this->session_id, $user);
        }
        if (empty($user)) {
            $user = $this->set_user(null);
        }
        return $user;
    }

}
