<?php

/**
 * organization_model.php
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

class Organization_model extends App_Base_Model {
    function save_mine($data, $id = null) {
        $CI = &get_instance();
        $now = date('Y-m-d H:i:s');
        $user = $CI->_get_user();
        $new_id = $this->save($data, $id);
        if (empty($id)) {
            $uo = array(
                'user_id' => $user['id'],
                'org_id' => $new_id,
            );
            
            $this->before_save($uo);
            $this->_db()->insert('user_organization', $uo);
            
            $id = $new_id;
        } else {
            throw new Exception('unimplemented yet');
        }
        return $id;
    }
}