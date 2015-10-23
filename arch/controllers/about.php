<?php

/**
 * about.php
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

class About extends app_base_controller {

    function framework() {
        $this->load->helper('inflector');
        $this->config->load('framework');
        $this->_data['framework'] = $this->config->item('framework');
        $this->_view = 'default/framework';
    }

    function _check_access() {
        return true;
    }

}
