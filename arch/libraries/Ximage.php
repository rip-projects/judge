<?php

/**
 * Ximage.php
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

require_once BASEPATH . 'libraries/Upload.php';

class Ximage extends CI_Upload {

    var $presets = array('thumb' => array('width' => 180, 'height' => 120));
    var $valid_required = true;
    var $data_dir = '';
    var $field = '';
    var $default_image = '';
    var $is_watermarked = false;

    function __construct($params = array()) {
        parent::__construct($params);
        $this->initialize($params);
    }

    function initialize($params = array()) {
        parent:: initialize($params);

        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        $this->upload_path = 'data/' . $this->data_dir;
        
        if (!file_exists($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        if (is_string($this->allowed_types)) {
            $this->set_allowed_types($this->allowed_types);
        }
    }

    function validate() {

        $this->do_upload();
//        $CI->upload
    }

    function get_image($data, $type = 'thumb', $data_dir = '', $default_image = '') {
        if (empty($data_dir)) {
            $data_dir = $this->data_dir;
        }
        if (empty($default_image)) {
            $default_image = $this->default_image;
        }
        $fields = explode('/', $data_dir);
        $field = $fields[count($fields) - 1];
        if (is_array($data)) {
            if (!empty($data[$field])) {
                $data = $data[$field];
            } else {
                $data = '';
            }
        } elseif (is_numeric($data)) {
            $CI = &get_instance();
            $data = $CI->_model($fields[0])->get($data);
            $data = $data[$field];
        }
        $uri = $data;
        if (empty($uri)) {
            $uri = $default_image;
        } else {
            $img = explode('/', $uri);
            $uri = $data_dir . '/' . $type . '/' . $img[count($img) - 1];
        }

        return $uri;
    }

    function do_upload($field = 'userfile') {
        $CI = &get_instance();
        if (!parent::do_upload($field)) {
            return FALSE;
        }

        $uploaded = $this->data();
        $_POST[$field] = $this->data_dir . '/' . $uploaded['file_name'];

        // copy original
        $dirname = 'data/' . $this->data_dir . '/original/';
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        copy($uploaded['full_path'], 'data/' . $this->data_dir . '/original/' . $uploaded['file_name']);

        if ($this->is_watermarked) {
            $this->watermark($_POST);
        }
        foreach ($this->presets as $key => $preset) {
            $this->resize($_POST, $key);
        }

        unlink($this->data_dir . '/' . $uploaded['file_name']);

        return TRUE;
    }

    function resize($photo, $preset, $config = null) {
        if (is_string($preset)) {
            $tmp = $preset;
            $preset = $this->presets[$preset];
            $preset['name'] = $tmp;
        }
        $field = $this->field;

        if (is_array($photo)) {
            $f = explode('/', $photo[$field]);
            $filename = $f[count($f) - 1];
        } elseif (is_object($photo)) {
            $f = explode('/', $photo->$field);
            $filename = $f[count($f) - 1];
        } else {
            throw new Exception('Error here and unhandled yet!');
        }

        if (!empty($photo)) {
            $dirname = 'data/' . $this->data_dir . '/' . $preset['name'] . '/';
            if (!file_exists($dirname)) {
                mkdir($dirname, 0777, true);
            }
            copy('data/' . $this->data_dir . '/original/' . $filename, $dirname . $filename);

            $CI = &get_instance();
            $config['source_image'] = $dirname . $filename;
            $config['width'] = $preset['width'];
            $config['height'] = $preset['height'];
            $CI->load->library('image_lib', $config);

            $CI->image_lib->initialize($config);
            $CI->image_lib->resize();
        }
    }

    function watermark($photo, $config = null) {
        $field = $this->field;

        if (is_array($photo)) {
            $f = explode('/', $photo[$field]);
            $filename = $f[count($f) - 1];
        } elseif (is_object($photo)) {
            $f = explode('/', $photo->$field);
            $filename = $f[count($f) - 1];
        } else {
            throw new Exception('Error here and unhandled yet!');
        }

        if (!empty($photo)) {
            $dirname = 'data/' . $this->data_dir . '/' . $preset['name'] . '/';
            if (!file_exists($dirname)) {
                mkdir($dirname, 0777, true);
            }
            copy('data/' . $this->data_dir . '/original/' . $filename, $dirname . $filename);
            $CI = &get_instance();

            $CI->load->library('image_lib');
            if ($config === null) {
                $config['source_image'] = $dirname . $filename;
                $config['wm_type'] = 'overlay';
                $config['wm_vrt_alignment'] = 'bottom';
                $config['wm_hor_alignment'] = 'right';
                $config['wm_overlay_path'] = 'data/logo.png';
            }
            $CI->image_lib->initialize($config);
            $CI->image_lib->watermark();
        }
    }

}

