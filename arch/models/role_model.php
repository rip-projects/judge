<?php

/**
 * role_model.php
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

class role_model extends App_Base_Model {

    var $CACHE_KEY_PRIVILEGE = 'ROLE_PRIVILEGE';
    var $CACHE_TIMEOUT_PRIVILEGE = 86400;

    function add_role_user($role_id, $user_id) {
        $role_user = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
        );
        $gu = $this->db->get_where('user_role', $role_user)->row_array();

        if (empty($gu)) {
            $this->before_save($role_user);
            $this->db->insert('user_role', $role_user);
        }
    }
    
    function get_privileges($role_id) {
        return $this->_db()->where('role_id', $role_id)->get('privilege_role')->result_array();
    }

    function privilege($uri, $role_id = '') {
        $CI = &get_instance();

        $privileges = $CI->cache->context_get($this->CACHE_KEY_PRIVILEGE);
        if ($privileges === FALSE) {
            $privileges = array();
            $result = $this->_db()->where('status', 1)->get('privilege_role')->result_array();
            foreach ($result as $row) {
                $privileges[$row['role_id']][$row['uri']] = 1;
            }
            $CI->cache->context_save($this->CACHE_KEY_PRIVILEGE, $privileges, $this->CACHE_TIMEOUT_PRIVILEGE);
        }

        $user = $CI->_get_user();

        if (empty($role_id)) {
            $roles = (isset($user['roles'])) ? $user['roles'] : array();
            foreach ($roles as $role) {
                $role_id = $role['role_id'];
                if (isset($privileges) && isset($privileges[$role_id]) && (isset($privileges[$role_id]['*']) || isset($privileges[$role_id][$uri]))) {
                    return true;
                }
            }
        } else {
            if (isset($privileges) && isset($privileges[$role_id]) && (isset($privileges[$role_id]['*']) || isset($privileges[$role_id][$uri]))) {
                return true;
            }
        }

        return false;
    }

    function add_privilege($id, $uri) {
        $data = array(
            'role_id' => $id,
            'uri' => $uri,
        );
        parent::before_save($data);
        $this->_db()->insert('privilege_role', $data);
        $this->cache->context_delete($this->CACHE_KEY_PRIVILEGE);
    }

    function delete_privilege($id, $priv_id) {
        $this->_db()->where('id', $priv_id);
        $this->_db()->delete('privilege_role');
        $this->cache->context_delete($this->CACHE_KEY_PRIVILEGE);
    }

}