<?php

/**
 * user_candidate.php
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
 * Description of user_candidate
 *
 * @author jafar
 */
class user_candidate extends app_crud_controller {

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'invite_me' => array(
                array(
                    'field' => 'full_name',
                    'label' => l('Full Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'gender',
                    'label' => l('Gender'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'email',
                    'label' => l('Email'),
                    'rules' => 'required|valid_email|callback__unique_email',
                ),
                array(
                    'field' => 'store_type',
                    'label' => l('Store Type'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'reason',
                    'label' => l('Reason'),
                    'rules' => 'required',
                ),
            ),
        );
    }

    public function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('email', 'gender', 'first_name', 'last_name', 'store_type', 'reason', 'invitation', 'status');
        $config['formats'] = array('', '', '', '', '', '', 'callback__invitation', 'callback__status');
        $config['names'] = array('', '', '', '', '', '', 'Invited', 'Graduated');
        $config['actions'] = array('Accept User and Send Invitation' => $this->_get_uri('accept_user'));
        return $config;
    }
    
    function _status($value) {
        if ($value == 1) {
            return '';
        }
        return 'Graduated';
    }
    
    function _invitation($value) {
        if (!empty($value)) {
            return 'Invited';
        }
        return '';
    }

    function _unique_email($value) {
        $count = $this->_model()->query('SELECT COUNT(*) count FROM user WHERE email LIKE ?', array($value))->row()->count;
        $count2 = $this->_model()->query('SELECT COUNT(*) count FROM user_candidate WHERE email LIKE ?', array($value))->row()->count;
        if ($count == 0 && $count2 == 0) {
            return true;
        } else {
            $this->form_validation->set_message('_unique_email', 'You are already registered or ask for invitation with the same %s.');
            return FALSE;
        }
    }

    function invite_me() {
        $result = array();
        if ($_POST) {
            if ($this->_validate()) {
                $fn = explode(' ', trim($_POST['full_name']), 2);

                unset($_POST['captcha']);

                $_POST['first_name'] = trim($fn[0]);
                if (!empty($fn[1])) {
                    $_POST['last_name'] = trim($fn[1]);
                }
                unset($_POST['full_name']);
                $this->_model()->save($_POST);

                $this->load->library('xmailer');
                $this->xmailer->send('invite_me', $_POST, $_POST['email']);

                notify_admin('admin/new_request_invitation', $_POST);

                $result = 1;
            } else {
                $result['error'] = array(
                    'code' => -2,
                    'message' => $this->_data['errors'],
                );
            }
        } else {
            $result['error'] = array(
                'code' => -1,
                'message' => 'No form sent',
            );
        }
        echo json_encode($result);
        exit;
    }

    function _check_access() {
        if ($this->uri->rsegments[2] == 'invite_me') {
            return true;
        }
        return parent::_check_access();
    }

    function accept_user($id = null) {
        if (!empty($id)) {
            $user_candidate = $this->_model()->accept_user($id);
            
            $this->load->library('xmailer');
            $data = array(
                'first_name' => $user_candidate['first_name'],
                'last_name' => $user_candidate['last_name'],
                'activation' => $user_candidate['invitation'],
            );
            $this->xmailer->send('user_accepted', $data, $user_candidate['email']);
            redirect('user_candidate/listing');
        }
    }

}