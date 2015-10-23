<?php

/**
 * post_model.php
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

class Post_model extends App_Base_Model {

    function listing($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {

        $filter_arr = array();

        if (!empty($filter['tag'])) {
            $filter_arr['AND tag_post.tag_id'] = $filter['tag'];
        }

        $from = 'FROM post LEFT JOIN tag_post ON post.id = tag_post.post_id WHERE 1=1 ';

        foreach ($filter_arr as $k => $v) {
            $from .= $k . ' = ? ';
        }

        $sql = 'SELECT COUNT(*) count ' . $from.' GROUP BY post.id';
        $count = @$this->db->query($sql, $filter_arr)->row()->count;
        if (empty($count)) $count = 0;

        $sql = 'SELECT post.* ' . $from.' GROUP BY post.id';

        $result = $this->db->query($sql, $filter_arr)->result_array();
        foreach ($result as &$value) {
            $tags = $this->db->query('SELECT name FROM tag LEFT JOIN tag_post ON tag.id = tag_post.tag_id WHERE tag_post.post_id = ?', array($value['id']))->result_array();
            $tag_str = array();
            foreach ($tags as $tag) {
                $tag_str[] = $tag['name'];
            }

            $value['tags'] = implode(',', $tag_str);
        }

        return $result;
    }

    function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        
        $filter_arr = array();

        if (!empty($filter['tag'])) {
            $filter_arr['AND tag_post.tag_id'] = $filter['tag'];
        }

        $from = 'FROM post LEFT JOIN tag_post ON post.id = tag_post.post_id WHERE 1=1 ';

        foreach ($filter_arr as $k => $v) {
            $from .= $k . ' = ? ';
        }

        $sql = 'SELECT COUNT(*) ' . $from;
        $count = $this->db->query($sql, $filter_arr)->result_array();

        $sql = 'SELECT post.*, tag_post.tag_id ' . $from;
        $result = $this->db->query($sql, $filter_arr)->result_array();
        foreach ($result as &$value) {
            $tags = $this->db->query('SELECT name FROM tag LEFT JOIN tag_post ON tag.id = tag_post.tag_id WHERE tag_post.post_id = ?', array($value['id']))->result_array();
            $tag_str = '';
            foreach ($tags as $tag) {
                $tag_str .= $tag['name'];
            }

            $value['tags'] = $tag_str;
        }

        return $result;
    }
    
    function find_by_tag($tag) {
        $sql = 'SELECT p.* FROM tag_post tp
            JOIN tag t ON tp.tag_id = t.id
            JOIN post p ON tp.post_id = p.id
            WHERE t.name = ?
            ORDER BY p.created_time DESC';
        return $this->db->query($sql, array($tag))->result_array();
    }

}

