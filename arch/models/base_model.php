<?php

/**
 * base_model.php
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

class Base_Model extends CI_Model {

    var $_name;
    var $_generated;
    var $_dbname = 'default';
    var $_id = 'id';

    function __construct() {
        parent::__construct();

        $CI = &get_instance();
        if (empty($CI->_dbs)) {
            include APPPATH . 'config/' . ENVIRONMENT . '/database' . EXT;
            $CI->_dbs = &$db;
            foreach ($db as $key => $value) {
                if ($key !== 'default') {
                    $dbname = 'db_' . $key;
                    $CI->$dbname = $CI->load->database($key, TRUE);
                    if (strtolower($value['dbdriver']) === 'oci8') {
                        $CI->$dbname->query('ALTER SESSION SET NLS_DATE_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                        $CI->$dbname->query('ALTER SESSION SET NLS_TIMESTAMP_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                        $CI->$dbname->query('ALTER SESSION SET NLS_COMP=ANSI');
                        $CI->$dbname->query('ALTER SESSION SET NLS_SORT=BINARY_CI');
                    }
                } else {
                    $CI->load->database();
                    if (strtolower($value['dbdriver']) === 'oci8') {
                        $CI->db->query('ALTER SESSION SET NLS_DATE_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                        $CI->db->query('ALTER SESSION SET NLS_TIMESTAMP_FORMAT="YYYY-MM-DD HH24:MI:SS"');
                        $CI->db->query('ALTER SESSION SET NLS_COMP=ANSI');
//                        $CI->db->query('ALTER SESSION SET NLS_SORT=BINARY_CI');
                    }
                }
            }
        }

        $name = preg_replace('/(.*)_model$/', '$1', get_class($this));
        $data = explode('_', $name);
        $this->_name = $CI->_dbs[$this->_dbname]['dbprefix'] . strtolower($name);

        $this->_generated = array('id', 'status', 'created_time', 'created_by', 'updated_time', 'updated_by');
    }

    function before_save(&$data, $id = null) {
        $CI = &get_instance();
        $user = $CI->_get_user();
        $now = date(l('format.mysql_datetime'));

        if (empty($id)) {
            if (!isset($data['status'])) {
                $data['status'] = 1;
            }
            $data['created_time'] = $data['updated_time'] = $now;
            $data['created_by'] = $data['updated_by'] = $user['username'];
        } else {
            $data['updated_time'] = $now;
            $data['updated_by'] = $user['username'];
        }
    }

    function after_save(&$data, $id = null) {
        
    }

    function save($data, $id = null) {
        $this->before_save($data, $id);

        if (empty($id)) {
            $this->_db()->insert($this->_name, $data);
        } else {
            $this->_db()->where($this->_id, $id);
            $this->_db()->update($this->_name, $data);
        }

        $err_no = $this->_db()->_error_number();
        $err_msg = $this->_db()->_error_message();
        if (empty($err_no)) {
            $id = (empty($id)) ? $this->_db()->insert_id() : $id;
            $this->after_save($data, $id);
            return $id;
        } else {
            log_message('warn', $err_no . ' : ' . $err_msg);
            throw new RuntimeException($err_msg, $err_no);
        }
    }

    function get($filter = null) {
        $count = 0;
        if (!empty($filter) && !is_array($filter)) {
            $filter = array('id' => $filter);
        } elseif (is_array($filter)) {
            unset($filter['q']);
        }
        $result = $this->_db()->get_where($this->_name, $filter)->row_array();

        $this->after_get($result);
        return $result;
    }

    function after_get(&$data) {
        
    }

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $this->_db()->start_cache();
        if (!empty($filter) && !is_array($filter)) {
            $filter = array('id' => $filter);
            $this->_db()->where($filter);
        } elseif (is_array($filter)) {
            unset($filter['q']);
            $this->_db()->or_like($filter, '', 'both');
        }
        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $this->_db()->order_by($key, $value);
            }
        }
        $this->_db()->stop_cache();
        $count = $this->_db()->count_all_results($this->_name);
        $result = $this->_db()->get($this->_name, $limit, $offset);
        $this->_db()->flush_cache();

        return $result->result_array();
    }

    function listing($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        return $this->find($filter, $sort, $limit, $offset, $count);
    }

    function import($fields) {
        $this->_db()->select($fields);
        return $this->_db()->get($this->_name)->result_array();
    }

    function delete($id) {
        if (is_array($id)) {
            $this->_db()->where_in('id', $id);
            $this->_db()->delete($this->_name);
        } else {
            $this->_db()->delete($this->_name, array('id' => $id));
        }
    }

    function delete_detail($parent_field, $parent_id) {
        $this->_db()->delete($this->_name, array($parent_field => $parent_id));
    }

    function field_data() {
        return $this->_db()->field_data($this->_name);
    }

    function _db($name = null) {
        if (!isset($name)) {
            $name = $this->_dbname;
        }
        if ($name === 'default') {
            return $this->db;
        } else {
            return $this->{'db_' . $name};
        }
    }

    function list_fields() {
        return $this->_db()->list_fields($this->_name);
    }

    function query($sql, $binds = FALSE, $return_object = TRUE) {
        return $this->_db()->query($sql, $binds, $return_object);
    }

    function truncate() {
        return $this->_db()->truncate($this->_name);
    }

    function &_model($name = '') {
        $CI = &get_instance();
        if (empty($name)) {
            $remove_pkg = false;
            $name = $CI->_name;
        } else {
            $remove_pkg = true;
            $CI->load->add_package_path(MODPATH.$name);
        }
        $model = $name . '_model';
        $CI->load->model($model);
//        if ($remove_pkg) {
//            $CI->load->remove_package_path(MODPATH.$name);
//        }
        return $CI->$model;
    }

    function get_tag($id) {
        $this->db->select('tag.*');
        $this->db->join('tag_' . $this->_name, 'tag_' . $this->_name . '.tag_id = tag.id');
        $this->db->where($this->_name . '_id', $id);
        $result = $this->db->get('tag')->result_array();

        $tags = array();
        foreach ($result as $r) {
            $tags[] = $r['name'];
        }

        return $tags;
    }

    function user_tag($user_id = '') {
        $CI = &get_instance();
        if (empty($user_id)) {
            $user = $CI->_get_user();
            $user_id = $user['id'];
        }
        $this->db->select('tag.*');
        $this->db->join('tag_' . $this->_name, 'tag_' . $this->_name . '.tag_id = tag.id');
        return $this->db->get('tag')->result_array();
    }

    function update_tag($tags, $id) {
        $CI = &get_instance();
        if (!is_array($tags)) {
            $tags = array($tags);
        }

        $this->db->where(array($this->_name . '_id' => $id));
        $this->db->delete('tag_' . $this->_name);

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $tag_id = $CI->_model('tag')->add($tag);

                if (!empty($tag_id)) {

                    $obj_tag = array(
                        'tag_id' => $tag_id,
                        $this->_name . '_id' => $id,
                    );

                    Base_Model::before_save($obj_tag);
                    $this->db->insert('tag_' . $this->_name, $obj_tag);
                }
            }
        }
    }

    function add_tag($id, $tags) {
        if (empty($tags)) {
            return;
        }

        if (!is_array($tags)) {
            $tags = array($tags);
        }
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                continue;
            }

            $row = $this->_db()->query('SELECT * FROM tag WHERE name = ?', array($tag))->row_array();
            if (empty($row)) {
                $data = array('name' => $tag);
                self::before_save($data);
                $this->_db()->insert('tag', $data);
                $tag_id = $this->_db()->insert_id();
            } else {
                $tag_id = $row['id'];
            }

            $data = array(
                'tag_id' => $tag_id,
                'ref_id' => $id,
            );
            self::before_save($data);
            $this->_db()->insert('tag_' . $this->_name, $data);
        }
    }


}

