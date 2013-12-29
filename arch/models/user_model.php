<?php

/**
 * user_model.php
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

class User_model extends App_Base_Model {

    var $CACHE_KEY_PRIVILEGE = 'USER_PRIVILEGE';
    var $CACHE_TIMEOUT_PRIVILEGE = 86400;

    function _fetch_user_data(&$user) {
        $CI = &get_instance();
        $user['ip_address'] = $CI->input->ip_address();
        $user['user_agent'] = $CI->input->user_agent();
        $user['location'] = $CI->input->location();
    }

    function _after_query(&$user) {

        if (!empty($user['id'])) {
            $this->_fetch_user_data($user);

            // add roles
            $sql = 'SELECT * FROM ' . $this->_db()->dbprefix . 'user_role ug
                LEFT JOIN ' . $this->_db()->_protect_identifiers($this->_db()->dbprefix . 'role') . ' g ON ug.role_id = g.id
                    WHERE user_id = ? AND g.status = 1';
            $user['roles'] = $this->_db()->query($sql, array($user['id']))->result_array();

            // add organization
            $sql = 'SELECT * FROM ' . $this->_db()->dbprefix . 'user_organization uo LEFT JOIN ' . $this->_db()->dbprefix . 'organization o ON uo.org_id = o.id WHERE user_id = ?';
            $user['organization'] = $this->_db()->query($sql, array($user['id']))->result_array();
        }

        return $user;
    }

    function login($login, $password) {
        $CI = &get_instance();
        $sql = 'SELECT * FROM ' . $this->_name . ' WHERE (username = ? OR email = ?) AND password = ? AND status = 1';
        $user = $this->_db()->query($sql, array($login, $login, md5($password)))->row_array();

        $this->_after_query($user);
        if (!empty($user)) {
            $user['login_mode'] = 'default';
        }
        return $user;
    }

    function login_facebook($login) {
        $sql = 'SELECT * FROM user WHERE sso_facebook = ?';
        $user = $this->db->query($sql, array($login))->row_array();

        $this->_after_query($user);
        if (!empty($user)) {
            $user['login_mode'] = 'facebook';
        }
        return $user;
    }

    function login_twitter($login) {
        $sql = 'SELECT * FROM user WHERE sso_twitter = ?';
        $user = $this->db->query($sql, array($login))->row_array();

        $this->_after_query($user);
        if (!empty($user)) {
            $user['login_mode'] = 'twitter';
        }
        return $user;
    }

    function login_ldap($login, $password) {
        $CI = &get_instance();
        // check password to ldap
        $sql = 'SELECT * FROM ' . $this->_name . '
            WHERE (username = ? OR email = ?) AND status = 1';
        $user = $this->_db()->query($sql, array($login, $login))->row_array();

        if (empty($user)) {
            return false;
        }

        try {
            $CI->load->library('xldap');
            $CI->xldap->auth($user['username'], $password);
        } catch (Exception $e) {
            return false;
        }

        $this->_after_query($user);
        if (!empty($user)) {
            $user['login_mode'] = 'ldap';
        }
        return $user;
    }

    function login_chat($login, $password) {
        $CI = &get_instance();

        $names = explode('/', $login);
        if (strpos($names[0], '@gmail.com') !== FALSE) {
            $user = $this->_db()->query('SELECT * FROM user WHERE gtalk_id = ?', array($names[0]))->row_array();
        } else {
            $user = $this->_db()->query('SELECT * FROM user WHERE ym_id = ?', array($names[0]))->row_array();
        }

        if (empty($user)) {
            return false;
        }
        return $user;
    }

    function refetch_user($old_user) {
        $user = $this->_db()->query('SELECT * FROM user WHERE id = ?', array($old_user['id']))->row_array();

        $this->_after_query($user);
        if (!empty($user)) {
            $user['login_mode'] = $old_user['login_mode'];
        }
        return $user;
    }

    function before_save(&$data, $id = null) {
        parent::before_save($data, $id);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = md5($data['password']);
        }
    }

    function add_user_role($user_id, $role_id) {
        $user_role = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
        );
//        $gu = $this->_db()->get_where('user_role', $user_role)->row_array();
//        if (empty($gu)) {
        $this->before_save($user_role);
        $this->_db()->insert('user_role', $user_role);
//        }
    }

    function update_user_role($user_id, $role_ids) {
        $this->_db()->delete('user_role', array('user_id' => $user_id));

        if (!empty($role_ids)) {
            if (!is_array($role_ids)) {
                $role_ids = array($role_ids);
            }

            foreach ($role_ids as $role_id) {
                if (!empty($role_id)) {
                    $role_id = (empty($role_id)) ? 0 : $role_id;
                    $user_role = array(
                        'user_id' => $user_id,
                        'role_id' => $role_id,
                    );
                    Base_Model::before_save($user_role);
                    $this->_db()->insert('user_role', $user_role);
                }
            }
        }
    }

    function privilege($uri, $user_id = '') {
        $CI = &get_instance();

        $user = $CI->_get_user();
        
        if (empty($user_id)) {
            $user_id = $user['id'];
        }

        $privileges = $CI->cache->context_get($this->CACHE_KEY_PRIVILEGE);
        if ($privileges === FALSE) {
            $privileges = array();
            $result = $this->_db()->where('status', 1)->get('privilege_user')->result_array();
            foreach ($result as $row) {
                $privileges[$row['user_id']][$row['uri']] = 1;
            }
            $CI->cache->context_save($this->CACHE_KEY_PRIVILEGE, $privileges, $this->CACHE_TIMEOUT_PRIVILEGE);
        }

        if (isset($privileges) && isset($privileges[$user_id]) && (isset($privileges[$user_id]['*']) || isset($privileges[$user_id][$uri]))) {
            return true;
        }

        return $CI->_model('role')->privilege($uri);
    }

    function register($data) {
        $id = $this->save($data);
        // FIXME hardcoded role_id should be fix to fetch from model
        $this->add_user_role($id, 2);
    }

    function add_trail($activity = '') {
        $CI = &get_instance();

        // reekoheek: user trail must be always enable
//        if (!$CI->config->item('user_trail_enable')) {
//            return;
//        }

        $user = $CI->_get_user();

        if (empty($user['id'])) {
            return;
        }

        $trail = array(
            'user_id' => $user['id'],
            'controller' => $CI->uri->rsegments[1] . '/' . $CI->uri->rsegments[2],
            'uri' => $CI->uri->uri_string,
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => (!empty($_REQUEST)) ? json_encode($_REQUEST) : '',
            'ip_address' => $user['ip_address'],
            'user_agent' => $user['user_agent'],
            'location' => $user['location'],
            'is_login' => $user['is_login'],
            'activity' => $activity,
            'created_time' => date('Y-m-d H:i:s'),
            'created_by' => $user['username'],
        );

        // reekoheek: user trail should log to file not to database
        $f = @fopen(APPPATH . 'logs/user_trail-' . (date('Y-m-d')) . '.log.php', 'a+');
        if ($f) {
            fputcsv($f, $trail);
            fclose($f);
        }

//        Base_Model::before_save($trail);        
        //$this->_db()->insert('user_trail', $trail);
    }

    function generate_activation_code($id) {
        $user = $this->get($id);
        $code = uniqid($user['username'] . '_');
        $data = array(
            'activation' => $code,
        );
        $this->save($data, $id);
        return $code;
    }

}
