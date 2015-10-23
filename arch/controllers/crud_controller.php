<?php

/**
 * crud_controller.php
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
 * Description of crud_controller
 *
 * @author jafar
 */
class crud_controller extends app_base_controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('xform'));
    }

    function index() {
        redirect($this->_get_uri('listing'));
        exit;
    }

    function detail($id) {
        $this->_data['field_data'] = $this->_model()->field_data();
        $this->_data['data'] = $this->_model()->get($id);
    }

    function add() {
        $this->_save();
    }

    function edit($id) {
        if (!isset($id)) {
            redirect('/');
            exit;
        }

        $id = explode(',', $id);
        $this->_save($id[0]);
    }

    function delete($id) {
        if (!isset($id)) {
            redirect('/');
            exit;
        }
        if (!empty($_GET['confirmed'])) {
            $id = explode(',', $id);
            $this->_model($this->_name)->delete($id);
            redirect($this->_get_uri('listing'));
            exit;
        }

        $this->_data['id'] = $id;
        $this->_data['ids'] = explode(',', $id);

        if (count($this->_data['ids']) == 1) {
            $this->_data['row_name'] = 'row #' . $id;
        }
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/' . 'show';
        $model = $this->_model();

        if ($_POST) {
            if ($this->_validate()) {
                $_POST['id'] = $id;
                try {
                    $model->save($_POST, $id);
                    $referrer = $this->session->userdata('referrer');
                    if (empty($referrer)) {
                        $referrer = $this->_get_uri('listing');
                    }
                    redirect($referrer);
                } catch (Exception $e) {
                    $this->_data['errors'] = '<p>' . $e->getMessage() . '</p>';
                }
            }
        } else {
            $this->load->library('user_agent');
            $this->session->set_userdata('referrer', $this->agent->referrer());
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $model->get($id);
            }
        }

        $this->_data['field_data'] = $model->field_data();
    }

    function listing($offset = 0) {
        $this->load->library('pagination');

        $config_grid = $this->_config_grid();
        $config_grid['sort'] = $this->_get_sort();
        $config_grid['filter'] = $this->_get_filter();
        $per_page = $this->pagination->per_page;

        $method = $config_grid['method'];

        $count = 0;
        $this->_data['items'] = $this->_model()->$method($config_grid['filter'], $config_grid['sort'], $per_page, $offset, $count);
        $this->_data['filter'] = $config_grid['filter'];
        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'total_rows' => $count,
            'per_page' => $per_page,
        ));
    }

    function _get_sort() {
        $sort = array();
        if (!empty($_GET['sort'])) {
            $ss = explode(',', $_GET['sort']);
            foreach ($ss as $s) {
                $s = explode(':', trim($s));
                $sort[$s[0]] = (!empty($s[1])) ? $s[1] : 'asc';
            }
        }
        return $sort;
    }

    function _get_filter() {
        $filter = '';
        if (!empty($_POST['_']) && $_POST['_'] == 'filter') {
            unset($_POST['_']);
            foreach ($_POST as $key => $value) {
                if (empty($value)) {
                    unset($_POST[$key]);
                }
            }
            $data = array(
                'name' => $this->_name,
                'filter' => $_POST,
            );
            $this->session->set_userdata('listing', $data);
            redirect($this->_get_uri($this->uri->rsegments[2]));
            exit;
        } elseif (isset($_GET['q'])) {
            foreach ($_GET as $key => $value) {
                if (empty($value)) {
                    unset($_GET[$key]);
                }
            }
            $data = array(
                'name' => $this->_name,
                'filter' => $_GET,
            );
            $this->session->set_userdata('listing', $data);
            redirect($this->_get_uri($this->uri->rsegments[2]));
            exit;
        } else {
            $data = $this->session->userdata('listing');
            if ($data['name'] == $this->_name) {
                $filter = $data['filter'];
                $config_grid = $this->_config_grid();
                $fields = (!empty($config_grid['filters'])) ? $config_grid['filters'] : $config_grid['fields'];
                if (!empty($filter['q'])) {
                    foreach ($fields as $field) {
                        if (!array_key_exists($field, $filter)) {
                            $filter[$field] = $filter['q'];
                        }
                    }
                } else {
                    unset($filter['q']);
                }
            } else {
                $this->session->set_userdata('listing', null);
            }
        }
        return $filter;
    }

    function _config_grid() {
        $fields = array();
        $sorts = array();
        foreach ($this->_model($this->_name)->list_fields() as $field) {
            if (!$this->_is_generated($field)) {
                $fields[] = $field;
            }
            $sorts[] = true;
        }
        return array(
            'fields' => $fields,
            'formats' => array('row_detail'),
            'sorts' => $sorts,
            'actions' => array(
                'edit' => $this->_get_uri('edit'),
                'delete' => $this->_get_uri('delete'),
            ),
            'method' => 'listing',
        );
    }

    function _is_generated($field) {
        foreach ($this->_model($this->_name)->_generated as $generated) {
            if ($generated === $field) {
                return true;
            }
        }
        return false;
    }

    function import($type = 'csv') {
        $config_grid = $this->_get_exim_config();
        $this->_data['config'] = $config_grid;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $this->_get_user();
            $config['upload_path'] = './data/import/';
            $config['allowed_types'] = $type;
            $config['max_size'] = '100';
            $config['max_width'] = '1024';
            $config['max_height'] = '768';
            $config['file_name'] = sprintf('%s_%s.%s', $user['id'], date('YmdHis'), $type);

            $this->load->library('upload', $config);
            $filename = $config['upload_path'] . '/' . $config['file_name'];

            if (!$this->upload->do_upload()) {
                $this->_data['errors'] = $this->upload->display_errors();
            } else {
                $data = $this->upload->data();
                $this->_data['info'] = l('Upload success');

                $handle = fopen($filename, 'r');
                $index = 0;
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if ($index < 1) {
                        $index++;
                    } else {
                        $row = array();
                        $find = array(',', ';');
                        foreach ($config_grid['import'] as $key => $field) {
                            $row[$field] = str_replace($find, '', $data[$key]);
                        }
                        try {
                            $tmp = $this->_model()->_db()->db_debug;
                            $this->_model()->_db()->db_debug = false;
                            $this->_model()->save($row);
                            $this->_model()->_db()->db_debug = $tmp;
                        } catch (Exception $e) {
                            log_message('error', print_r($e, 1));
                        }
                    }
                }
                fclose($handle);

                redirect($this->_get_uri('listing'));
            }
        }
    }

    function export($type = 'csv') {
        $config_grid = $this->_get_exim_config();
        $this->load->helper('date');
        $outstr = NULL;

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;Filename=' . strtolower($this->_name) . '.' . $type);

        echo humanize($this->_name) . ",\n";
        $datestring = "%d %M %Y - %h:%i %a";
        $time = time();
        echo mdate($datestring, $time) . ",\n";

        echo implode(',', $config_grid['import_names']) . "\n";

        $data = $this->_model()->import($config_grid['import']);

        foreach ($data as $row) {
            echo implode(',', $row) . "\n";
        }
        exit;
    }

    function import_example($type = 'csv') {
        $config_grid = $this->_get_exim_config();

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment;Filename=example_' . strtolower($this->_name) . '.' . $type);

        echo implode(',', $config_grid['import_names']) . "\n";
        foreach ($config_grid['import_examples'] as $ex) {
            echo implode(',', $ex) . "\n";
        }
        exit;
    }

    function _get_exim_config() {
        $config_grid = $this->_config_grid();

        if (empty($config_grid['import'])) {
            $config_grid['import'] = $config_grid['fields'];
        }

        if (empty($config_grid['import_names'])) {
            foreach ($config_grid['import'] as $field) {
                $config_grid['import_names'][] = humanize($field);
            }
        }

        if (empty($config_grid['import_examples'])) {
            for ($i = 0; $i < 10; $i++) {
                $ex = array();
                foreach ($config_grid['import'] as $field) {
                    $ex[] = $field . ' ' . $i;
                }
                $config_grid['import_examples'][] = $ex;
            }
        }

        return $config_grid;
    }

}

