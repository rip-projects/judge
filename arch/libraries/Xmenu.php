<?php

/**
 * xmenu.php
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

class Xmenu {

    var $xmenu_items = array();
    var $xmenu_source = 'inline';
    var $home_url = '';
    var $CI;

    function __construct($params = array()) {
        $this->CI = &get_instance();

        $source = explode(':', trim($params['xmenu_source']));
        switch($source[0]) {
            case 'model':
                if (method_exists($this->CI->_model($source[1]), $source[2])) {
                    $params['xmenu_items'] = $this->CI->_model($source[1])->{$source[2]}();
                }
                break;
            default:
                break;
        }

        $this->initialize($params);
    }

    function initialize($params = array()) {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }

        $this->xmenu_items = $this->_check_access($this->xmenu_items);
    }

    function _check_access($menus) {
        $CI = &get_instance();
        $new_menus = array();
        foreach($menus as $menu) {
            $show = false;
            if (!empty($menu['uri'])) {
                if ($CI->_model('user')->privilege($menu['uri'])) {
                    $show = true;
                }
            }

            $menu['children'] = @$this->_check_access($menu['children']);
            if (!empty($menu['children'])) {
                $show = true;
            }
            if ($show) {
                $new_menus[] = $menu;
            }
        }
        return $new_menus;
    }

    function _get_menu($menus) {
        $CI = &get_instance();
        return $CI->load->view('libraries/xmenu_show', array(
            'menus' => $menus,
            'self' => $this,
                ), true);
    }

    function show() {
        $menu_string = '';
        if (!empty($this->xmenu_items)) {
            $menu_string = $this->_get_menu($this->xmenu_items);
        }
        return $menu_string;
    }

    function _get_breadcrumb_path($menus = 'top') {
        if ($menus == 'top') {
            $menus = $this->xmenu_items;
        }

        $CI = &get_instance();
        $uri = $CI->_get_uri($CI->uri->rsegments[2]);
        foreach ($menus as $menu) {
            if (!empty($menu['uri']) && $menu['uri'] == $uri) {
                return array($menu);
            } else if (!empty($menu['children'])) {
                $sub_menu = $this->_get_breadcrumb_path($menu['children']);
                if ($sub_menu !== null) {
                    if (empty($sub_menu['uri'])) {
                        return array_merge(array($menu), $sub_menu);
                    } else {
                        return array($menu, $sub_menu);
                    }
                }
            }
        }
        return null;
    }

    function breadcrumb($breadcrumb = null, $show_home = true) {
        $CI = &get_instance();
        if ($breadcrumb == null) {
            $breadcrumb = $this->_get_breadcrumb_path();
            if (empty($breadcrumb)) {
                $breadcrumb = array(
                    array('title' => humanize($CI->_name), 'uri' => $CI->uri->rsegments[1]),
                );
                if ($CI->uri->rsegments[2] != 'index') {
                    $breadcrumb[] = array('title' => humanize($CI->uri->rsegments[2]), 'uri' => $CI->uri->uri_string);
                }
            }
        }
        return $CI->load->view('libraries/xmenu_breadcrumb', array(
            'breadcrumb' => $breadcrumb,
            'show_home' => $show_home,
            'self' => $this,
                ), true);
    }

}

