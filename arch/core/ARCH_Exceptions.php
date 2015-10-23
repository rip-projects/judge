<?php

/**
 * ARCH_Exceptions.php
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

class ARCH_Exceptions extends CI_Exceptions {

    function __construct() {
        parent::__construct();
        if (class_exists('CI_Controller')) {
            $CI = &get_instance();
            if (!empty($CI)) {
                $CI->lang->load_gettext();
            } else {
                $GLOBALS['LANG']->load_gettext();
            }
        } else {
            require_once ARCHPATH . 'helpers/x_helper' . EXT;
        }
    }

}

