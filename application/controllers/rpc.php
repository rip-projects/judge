<?php

/**
 * rpc.php
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
 * Description of rpc
 *
 * @author jafar
 */
class rpc extends base_controller {

    function tags() {
        $this->db->like('name', $_GET['q']);
        $result = $this->db->get('tag')->result_array();
        echo json_encode($result);
        exit;
    }

    function get_post_name() {
        $name = strtolower(url_title($_POST['title']));

        $post = $this->db->query('SELECT * FROM post WHERE post_name LIKE ?', array($name))->row_array();
        if (!empty($post)) {
            $posts = $this->db->query('SELECT post_name FROM post WHERE post_name LIKE "' . $name . '%"')->result_array();
            $pn = '';
            for ($i = 1;; $i++) {
                $found = false;
                foreach ($posts as $post) {
                    if ($name . '-' . $i == $post['post_name']) {
                        $found = true;
                    }
                }

                if (!$found) {
                    echo json_encode($name . '-' . $i);
                    exit;
                }
            }
        } else {
            echo json_encode($name);
        }
        exit;
    }

    function _check_access() {
        return true;
    }
    
	function user_options() {
        $q = strtolower($_GET['q']);
        $sql = "SELECT id `key`, concat(first_name,' ',last_name) value FROM user WHERE lower(first_name) LIKE ? OR lower(last_name) LIKE ? ";

        $result = $this->db->query($sql, array('%' . $q . '%','%' . $q . '%'))->result_array();
        echo json_encode($result);
        exit;
    }

}