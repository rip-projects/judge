<?php

/**
 * user_candidate_model.php
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
 * Description of user_candidate_model
 *
 * @author jafar
 */
class user_candidate_model extends Base_Model {

    function accept_user($id) {
        $code = $this->generate_invitation_code($id);
        return $this->get($id);
    }

    function generate_invitation_code($id) {
        $user_candidate = $this->get($id);
        $code = hash_hmac('sha256', json_encode($user_candidate), uniqid());

        $data = array(
            'invitation' => $code,
        );
        $this->save($data, $id);

        return $code;
    }

    function graduate($invitation) {
        $this->_db()->where('invitation', $invitation);
        $this->_db()->update('user_candidate', array('invitation' => '', 'status' => 0));
    }

}