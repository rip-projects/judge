<?php

/**
 * cli_controller.php
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
 * Description of app_base_controller
 *
 * @author jafar
 */
class Cli_Controller extends CI_Controller {

    function _post_controller_constructor() {
        if (!$this->_check_access()) {
            $this->load->helper('url');
            header("Status: 500 Cannot access from outside CLI");
            exit;
        }
    }
    
    function _check_access() {
        return $this->input->is_cli_request();
    }
    
    function &_model($name) {
        $model = $name . '_model';
        $this->load->model($model);
        return $this->$model;
    }

}

