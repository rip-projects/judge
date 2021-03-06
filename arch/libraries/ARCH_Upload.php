<?php

/**
 * ARCH_Upload.php
 *
 * @package     arch-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

class ARCH_Upload extends CI_Upload {

    var $field = '';

    function initialize($params = array()) {
        $CI = &get_instance();

        if (!empty($params['field'])) {
            $this->field = $params['field'];
        }

        parent::initialize($params);
    }

}