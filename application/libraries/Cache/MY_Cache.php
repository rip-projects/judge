<?php

/**
 * MY_Cache.php
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

class MY_Cache extends CI_Cache {

    protected $lib_name = 'CI_Cache';

    public function context_get($id) {
        $CI = &get_instance();
        $id = strtolower($CI->config->item('app_id') . ':' . $id);
        return $this->get($id);
    }

    public function context_save($id, $data, $ttl = 60) {
        $CI = &get_instance();
        $id = strtolower($CI->config->item('app_id') . ':' . $id);
        return $this->save($id, $data, $ttl);
    }

    public function context_delete($id) {
        $CI = &get_instance();
        $id = strtolower($CI->config->item('app_id') . ':' . $id);
        return $this->delete($id);
    }

}
