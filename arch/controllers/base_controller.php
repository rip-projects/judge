<?php

/**
 * base_controller.php
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

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of Base_controller
 *
 * @author jafar
 */
class base_controller extends CI_Controller {

    var $_validation;
    var $CI;
    var $_name;
    var $_data;
    var $_view;
    var $_layout_view = 'layouts/main';

    function __construct() {
        parent::__construct();

        if ($this->router->is_module) {
            $this->load->add_package_path(MODPATH . $this->router->module_name);
        }

        $this->CI = $this;

        $this->config->load('app');

        $this->output->enable_profiler($this->config->item('enable_profiler'));

        if (!$this->input->is_cli_request()) {
            if ($this->config->config['cookie_path'] == '') {
                $x = explode($_SERVER['HTTP_HOST'], $this->config->item('base_url'));
                $cookie_path = rtrim($x[1], '/');
                $this->CI->config->set_item('cookie_path', ($cookie_path == '') ? '/' : $cookie_path);
            }
        }

        $this->load->helper(array('url', 'form', 'x', 'text', 'inflector', 'xform'));

        $this->_name = $this->uri->rsegments[1];

        $this->_page_title = $this->config->item('page_title') . ' - ' . ((empty($this->uri->rsegments[1])) ? '' : humanize($this->uri->rsegments[1])) . ((empty($this->uri->rsegments[2])) ? ' ' : ' ' . humanize($this->uri->rsegments[2]));
        if ($this->CI->config->item('lang_use_gettext')) {
            $this->lang->load_gettext();
        }
        $this->load->library('session');
        $this->load->library('xparam');

        if (!isset($this->auth)) {
            $this->load->library('xauth', null, 'auth');
        }

//        if ($this->config->item('debug')) {
//            $this->load->driver('cache', array('adapter' => 'dummy'));
//        } else {
        // reekoheek: cache should always be run
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
//        }
    }

    function _validate($csrf = false) {
        $this->_data['errors'] = (isset($this->_data['errors'])) ? $this->_data['errors'] : '';
        $result = true;

        if (!$this->security->is_valid_token() && $csrf) {
            $this->_data['errors'] = '<p>Cannot access resources, Contact Administrator</p>';
            $result = false;
        } else {

            $this->load->library('form_validation');

            if (!empty($this->_validation[$this->uri->rsegments[2]])) {
                $this->form_validation->set_rules($this->_validation[$this->uri->rsegments[2]]);

                $result = $this->form_validation->run();
                if (!$result) {
                    $this->_data['errors'] = validation_errors();
                }
            }

            $uploader = null;
            if (isset($this->ximage)) {
                $uploader = $this->ximage;
            } elseif (isset($this->upload)) {
                $uploader = $this->upload;
            }
            if (!empty($uploader)) {
                if ($_FILES[$uploader->field]['error'] !== 4) {
                    if (!$uploader->do_upload($uploader->field)) {
                        $result = false;
                        $this->_data['errors'] .= "\n<p>" . l($uploader->display_errors()) . '</p>';
                    }
                }
            }

            if (isset($_POST['captcha'])) {
                if (!validate_captcha()) {
                    $result = false;
                    $this->_data['errors'] .= "\n<p>" . l('You must submit the word that appears in the image') . '</p>';
                }
            } elseif (isset($_POST['recaptcha_response_field'])) {
                $this->load->library('recaptcha');
                $this->lang->load('recaptcha');
                $captcha = trim($_POST['recaptcha_response_field']);
                if (empty($captcha) || !$this->recaptcha->check_answer($this->input->ip_address(), $this->input->post('recaptcha_challenge_field'), $this->input->post('recaptcha_response_field'))) {
                    $result = false;
                    $this->_data['errors'] .= "\n<p>" . l('You must submit the word that appears in the image') . '</p>';
                }
            }
        }

        if (!empty($this->_data['errors'])) {
            $this->session->set_flashdata('validation::errors', $this->_data['errors']);
        }

        return $result;
    }

    function &_model($name = '') {
        if (empty($name)) {
            $remove_pkg = false;
            $name = $this->_name;
        } else {
            $remove_pkg = true;
            $this->load->add_package_path(MODPATH . $name);
        }
        $model = $name . '_model';
        $this->load->model($model);
// TODO: undecided yet, but perhaps we may have functionality to remove package after used
//        if ($remove_pkg) {
//            $this->load->remove_package_path(MODPATH.$name);
//        }
        return $this->$model;
    }

    function _post_controller_constructor() {
        $this->_model('user')->add_trail();

        if (!$this->_check_access()) {
            redirect('site/error?continue=' . current_url(), null, 303);
            exit;
        }
    }

    function _post_controller() {
        if (empty($this->_view)) {
            $this->_view = $this->_name . '/' . $this->uri->rsegments[2];
        }
        if (!$this->input->is_ajax_request() && !empty($this->_layout_view)) {
            $view = $this->_layout_view;
            $data = array();
        } else {
            $view = $this->_view;
            $data = $this->_data;
        }
        widget_view($view, $data);
    }

    function _get_user($refetch = false) {
        return $this->auth->get_user($refetch);
    }

    function _get_uri($action = '') {
        $pos = array_search($this->_name, $this->uri->segments);
        if ($pos === false) {
            throw new RuntimeException('Error creating uri for action: ' . $action . ' with uri: ' . $this->uri->uri_string);
        }
        $uri = array_chunk($this->uri->segments, $pos);
        return implode('/', $uri[0]) . '/' . $action;
    }

    function _get_redirect() {
        $continue = $this->session->userdata('continue');
        if (!empty($continue)) {
            $this->session->userdata('continue', NULL);
            return $continue;
        }
        if (!empty($_REQUEST['continue'])) {
            return $_REQUEST['continue'];
        }
        if (!empty($_GET['continue'])) {
            return $_GET['continue'];
        }
        if (!empty($_POST['continue'])) {
            return $_POST['continue'];
        }
        if (!empty($_COOKIE['continue'])) {
            return $_COOKIE['continue'];
        }
        return base_url();
    }

    function _check_access() {
        if (strtoupper($this->config->item('uri_protocol')) === 'CLI') {
            return true;
        }

        if (preg_match('/^(user\/(login|logout|register))|(site\/error)/', $this->uri->uri_string)) {
            return true;
        } elseif ($this->uri->rsegments[1] == 'about' && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
            return true;
        }

        return ($this->_model('user')->privilege($this->uri->rsegments[1] . '/' . $this->uri->rsegments[2]) || $this->_model('user')->privilege($this->uri->uri_string));
    }

    // REMARK: reekoheek this method is deprecated
    // function field_data() {
    //  return $this->db->field_data($this->_name);
    // }
}

