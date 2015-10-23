<?php

/**
 * organization.php
 *
 * @package     arch-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
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

/**
 * Description of organization
 *
 * @author jafar
 */
class Organization extends app_crud_controller {
    
    function __construct() {
        parent::__construct();
        
        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'name',
                    'label' => l('Company Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'email',
                    'label' => l('Email'),
                    'rules' => 'valid_email',
                ),
            ),
        );
    }
    
    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields']    = array('name', 'address', 'phone', 'email', 'fax', 'website');
        $config['formats']    = array('row_detail', '', '', '', '', 'url');
        return $config;
    }

}

