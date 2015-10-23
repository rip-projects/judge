<?php

/**
 * ARCH_Pagination.php
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

class ARCH_Pagination extends CI_Pagination {

    var $per_pages = '';
    var $per_page_changer_prefix = 'Show: ';

    function __construct($params = array()) {
        parent::__construct($params);

        $CI = &get_instance();

        if (isset($_GET['per_page'])) {
            $CI->session->set_userdata('per_page', $_GET['per_page']);
            $this->per_page = $_GET['per_page'];
//            redirect($this->base_url);
        } else {
            $per_page = $CI->session->userdata('per_page');
            if (!empty($per_page)) {
                $this->per_page = $per_page;
            }
        }
    }

    function initialize($params = array()) {
        $CI = &get_instance();
        if (empty($params['base_url'])) {
            $listing_pos = array_search('listing', $CI->uri->segments);
            $this->base_url = site_url($CI->_get_uri('listing'));
            $this->uri_segment = $listing_pos + 1;
        }
        
        parent::initialize($params);
    }

    function per_page_changer() {
        $CI = &get_instance();
        $current_per_page = $CI->session->userdata('per_page');
        if (empty($current_per_page)) {
            $current_per_page = $this->per_pages[0];
            $CI->session->set_userdata('per_page', $current_per_page);
        }

        $params = array(
            'CI' => &$CI,
            'self' => &$this,
            'current_per_page' => $current_per_page,
        );

        return $CI->load->view('libraries/pagination_per_page_changer', $params, true);
    }

    function create_links() {
        $links = parent::create_links();
        if ($links) {
            $links = "
                <div class=\"pagination\">
                    <span class=\"left\">Page</span> 
                    <div class=\"left\">" . $links . "</div> 
                    <span class=\"left\" style=\"margin-left:5px\"> of ".(ceil($this->total_rows / $this-> per_page))."</span>
                </div>";
        }
        return $links;
    }

}