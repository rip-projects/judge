<?php

/**
 * user.php
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

class User extends app_crud_controller {

    function __construct() {
        parent::__construct();

        $this->_validation = array(
            'add' => array(
                array(
                    'field' => 'first_name',
                    'label' => l('First Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'last_name',
                    'label' => l('Last Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'email',
                    'label' => l('Email'),
                    'rules' => 'required|valid_email|callback__unique_email',
                ),
                array(
                    'field' => 'username',
                    'label' => l('Username'),
                    'rules' => 'required|callback__unique_username',
                ),
                array(
                    'field' => 'password',
                    'label' => l('Password'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'password2',
                    'label' => l('Retype Password'),
                    'rules' => 'required|callback__retypepassword_check',
                ),
            ),
            'register' => array(
                array(
                    'field' => 'first_name',
                    'label' => l('First Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'last_name',
                    'label' => l('Last Name'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'email',
                    'label' => l('Email'),
                    'rules' => 'required|valid_email|callback__unique_email',
                ),
                array(
                    'field' => 'username',
                    'label' => l('Username'),
                    'rules' => 'required|callback__unique_username',
                ),
                array(
                    'field' => 'password',
                    'label' => l('Password'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'password2',
                    'label' => l('Retype Password'),
                    'rules' => 'required|callback__retypepassword_check',
                ),
            ),
            'change_password' => array(
                array(
                    'field' => 'password',
                    'label' => l('Password'),
                    'rules' => 'required',
                ),
                array(
                    'field' => 'old_password',
                    'label' => l('Old Password'),
                    'rules' => 'required|callback__old_password_check',
                ),
                array(
                    'field' => 'password2',
                    'label' => l('Retype Password'),
                    'rules' => 'required|callback__retypepassword_check',
                ),
            ),
        );
    }

    function _retypepassword_check($value) {
        if ($value !== $_POST['password']) {
            $this->form_validation->set_message('_retypepassword_check', 'Password does not match.');
            return FALSE;
        }
        return true;
    }
    
    function _old_password_check($value) {
        $id = $this->uri->segment(3);
        $user = $this->_model()->get($id);
        $user_password = $user['password'];
        $old_password = md5($value);

        if ($user_password !== $old_password) {
            $this->form_validation->set_message('_old_password_check', 'Old Password does not match.');
            return FALSE;
        }
        return true;
    }

    function _unique_email($value) {
        $count = $this->_model()->query('SELECT COUNT(*) count FROM user WHERE email LIKE ?', array($value))->row()->count;
        if ($count == 0) {
            return true;
        } else {
            $this->form_validation->set_message('_unique_email', 'The %s field must be unique.');
            return FALSE;
        }
    }

    function _unique_username($value) {
        $count = $this->_model()->query('SELECT COUNT(*) count FROM user WHERE username LIKE ?', array($value))->row()->count;
        if ($count == 0) {
            return true;
        } else {
            $this->form_validation->set_message('_unique_username', 'The %s field must be unique.');
            return FALSE;
        }
    }

    function login($mode = '') {
        $this->_layout_view = 'layouts/main';

        if ($_POST || !empty($mode)) {
            if ($_POST) {
                $is_login = $this->auth->login($_POST['login'], $_POST['password'], $mode);
            } else {
                $is_login = $this->auth->login('', '', $mode);
            }

            if ($is_login) {
                $this->_model('user')->add_trail('login');
                redirect($this->_get_redirect());
                exit;
            } else {
                $this->_data['errors'] = l('Username/email or password not found');
            }
        }
    }

    function logout() {
        $this->_model('user')->add_trail('logout');
        $this->auth->logout();
        redirect($this->_get_redirect());
    }

    function register() {

        // REMARK hanya yang memiliki code
        // if (empty($_GET['act'])) {
        //     redirect('/');
        // }
        // REMARK hanya yang memiliki code

        if ($_POST) {
            if ($this->_validate()) {
                $data = array(
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'email' => $_POST['email'],
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                );
                $this->_model()->register($data);

                $this->load->library('xmailer');
                $data = array(
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'username' => $_POST['username'],
                );
                $this->xmailer->send('register', $data, $_POST['email']);

                $this->_model('user_candidate')->graduate($_GET['act']);

                $_POST['login'] = $_POST['username'];
                $this->login();
            }
        } else {
            $user = $this->_model('user_candidate')->get(array('invitation' => $_GET['act']));
            if (empty($user)) {
                redirect('/');
            }
            $_POST = $user;
        }
    }

    function profile($username = NULL) {
        $user = $this->_get_user();
        if (empty($username)) {
            if ($user['is_login']) {
                redirect('profile/' . $user['username']);
            } else {
                redirect('register');
            }
            exit;
        }


        $user = $this->_model()->get(array('username' => $username));

        if (empty($user)) {
            redirect('/');
            exit;
        }

        $sql = 'SELECT role_id id, name FROM ' . $this->db->dbprefix . 'user_role ug LEFT JOIN ' . $this->db->dbprefix . 'role g ON ug.role_id = g.id WHERE user_id = ?';
        $user['role'] = $this->db->query($sql, array($user['id']))->result_array();

// FIXME reekoheek: i think code below doesnt necessary for generic arch
//        if ($user['role']['id'] != 3) {
//            redirect('/');
//            exit;
//        }

        $this->_data['user'] = $user;

        if (empty($user)) {
            redirect('/');
            exit;
        }
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('username', 'first_name', 'last_name', 'email');
        return $config;
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/' . 'show';

        $user_model = $this->_model('user');

        if ($_POST) {
            if ($this->_validate()) {
                $roles = @$_POST['roles'];
                $org_id = @$_POST['org'];
                $_POST['id'] = $id;
                unset($_POST['roles']);
                unset($_POST['org']);
                unset($_POST['password2']);

                $id = $user_model->save($_POST, $id);
                if (!empty($roles)) {
                    $user_model->update_user_role($id, $roles);
                }
                if (!empty($org_id)) {
                    $user_model->update_user_org($id, $org_id);
                }

                if ($this->input->is_ajax_request()) {
                    echo true;
                    exit;
                } else {
                    redirect($this->_get_uri('listing'));
                    exit;
                }
            } else {
                $this->_data['errors'] = validation_errors();
            }
        } else {
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $user_model->get($id);
                $param = array($_POST['id']);

                $roles = $user_model->_db()->query('SELECT role_id FROM ' . $user_model->_db()->dbprefix . 'user_role WHERE user_id = ?', $param)->result_array();
                $_POST['roles'] = array();
                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        $_POST['roles'][] = $role['role_id'];
                    }
                }
                $org = $user_model->_db()->query('SELECT org_id FROM ' . $user_model->_db()->dbprefix . 'user_organization WHERE user_id = ?', $param)->row_array();
                if (!empty($org)) {
                    $_POST['org'] = $org['org_id'];
                }
            }
        }

        $this->_data['field_data'] = $user_model->field_data();

        $_POST['password'] = '';

        $roles = $this->_model('role')->find();
        $this->_data['role_items'] = array();
        foreach ($roles as $role) {
            $this->_data['role_items'][$role['id']] = $role['name'];
        }
        $orgs = $this->_model('organization')->find(null, array('name' => 'asc'));
        $this->_data['org_items'] = array();
        foreach ($orgs as $org) {
            $this->_data['org_items'][$org['id']] = $org['name'];
        }
    }

    // function activation() {
    //     if (!empty($_GET['act'])) {
    //         $user = $this->_model()->get(array('activation' => $_GET['act']));

    //         if (!empty($user)) {
    //             $data = array('activation' => '1');
    //             $this->_model()->save($data, $user['id']);
    //             $this->_get_user(true);

    //             $this->auth->logout();

    //             $_POST['login'] = $_POST['username'];
    //             $this->login();
    //         }
    //     }
    // }

    // function generate_activation_code() {
    //     $user = $this->_get_user();
    //     $this->_model('user')->generate_activation_code($user['id']);

    //     $this->_get_user(true);
    //     $this->load->library('xmailer');
    //     $this->xmailer->send('activation', $data);

    //     redirect('/');
    // }

    function _check_access() {
        $allows = array(
            'activation',
            'generate_activation_code',
            'profile',
            'request_invitation',
        );
        foreach ($allows as $allow) {
            if ($allow == $this->uri->rsegments[2]) {
                return true;
            }
        }
        return parent::_check_access();
    }

    function request_invitation() {
        $this->load->helper('captcha');
        $vals = array(
            'img_path' => './captcha/',
            'img_url' => base_url() . 'captcha/',
            'img_width' => 150,
            'img_height' => 50,
        );

        $this->_data['cap'] = $cap = create_captcha($vals);

        $data = array(
            'captcha_time' => $cap['time'] . '',
            'ip_address' => $this->input->ip_address(),
            'word' => $cap['word']
        );

        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);
    }

    function invite_friends() {
        
    }

    function edit_profile() {
        
    }
    
    function change_password($id) {

        $user = $this->_model()->get($id);
        if ($_POST) {
            
            if ($this->_validate()) {
                unset($_POST['password2']);

                $data = array( 
                    
                    'password' => md5($_POST['password']),
                );
//                unset($_POST['old_password']);
                $this->db->where('id', $id);
                $this->db->update('user', $data);

                redirect(site_url(''));
                exit;
            } else {
                $this->_data['errors'] = validation_errors();
            }
        }
    }

    function inbox () {

    }

}

