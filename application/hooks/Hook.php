<?php

/**
 * Hook.php
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

class Hook {

    function post_controller_constructor() {
        $CI = &get_instance();
        if (method_exists($CI, '_post_controller_constructor')) {
            $CI->_post_controller_constructor();
        }
    }

    function post_controller() {
        $CI = &get_instance();
        if (method_exists($CI, '_post_controller')) {
            $CI->_post_controller();
        }
    }

    function pre_system() {
        // define('ARCHPATH', 'arch/');
        define('MODPATH', 'modules/');
        define('THEMEPATH', 'themes/');
        spl_autoload_register(array($this, '_autoload_class'));
    }

    function _autoload_class($class) {
        $class = strtolower($class);
        $matches = null;
        $match = preg_match('/(controller|model)$/i', $class, $matches);
        if ($match) {
            foreach (array(BASEPATH, APPPATH, ARCHPATH) as $path) {
                $file_path = $path . $matches[0] . 's/' . $class . EXT;
                if (file_exists($file_path)) {
                    require_once $file_path;
                }
            }
        }
    }

}

