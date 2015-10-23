<?php

/**
 * role.php
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

class role extends app_crud_controller {

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'name',
                    'label' => l('Name'),
                    'rules' => 'required',
                ),
            ),
            'edit' => array(
                array(
                    'field' => 'name',
                    'label' => l('Name'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name');
        return $config;
    }

    function _save($id = null) {
        parent::_save($id);

        if (!empty($id)) {
            $this->_data['privileges'] = $this->_model()->get_privileges($id);
            $config = array(
                'fields' => array('uri'),
                'actions' => array(
                    'delete' => 'role/delete_privilege/' . $id,
                ),
            );
            $this->load->library('xgrid', $config, 'grid_privilege');
        }
    }

    function add_privilege($id) {
        $this->_model()->add_privilege($id, @$_POST['uri']);
        $this->cache->context_delete($this->_model()->CACHE_KEY_PRIVILEGE);
        redirect('role/edit/' . $id);
        exit;
    }

    function delete_privilege($id, $priv_id) {
        $this->_model()->delete_privilege($id, $priv_id);
        $this->cache->context_delete($this->_model()->CACHE_KEY_PRIVILEGE);
        redirect(site_url('role/edit/' . $id));
        exit;
    }

}
