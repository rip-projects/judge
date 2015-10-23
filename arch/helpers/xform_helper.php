<?php

/**
 * xform_helper.php
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

if ( ! function_exists('xform_autocomplete')) {

    function xform_autocomplete($input_data = '', $value = '', $extra = '', $data = '') {
        $CI = &get_instance();

        if (is_array($input_data)) {
            $field = $input_data['name'] . '_options';
            $f_name = $input_data['name'];
        } else {
            $field = $input_data . '_options';
            $f_name = $input_data;
        }

        if ($data === '') {
            if (!empty($CI->load->_ci_cached_vars[$field])) {
                $data = $CI->load->_ci_cached_vars[$field];
            } else {
                $data = site_url('rpc/' . $field);
            }
        } elseif (is_string($data)) {
            $data = site_url($data);
        }

        return $CI->load->view('helpers/xform_autocomplete', array(
            'data' => $data,
            'field' => $f_name,
            'input_data' => $input_data,
            'value' => $value,
            'extra' => $extra,
                ), true);
    }
}

if ( !function_exists('xform_boolean')) {
    function xform_boolean($data) {
        $items = array( 'False', 'True' );
        return form_dropdown($data, $items);
    }
}

if ( !function_exists('xform_to_mdatetime')) {
    function xform_to_mdatetime($fdate, $ftime = '') {
        $fdate = explode('/', $fdate);
        $ftime = explode(':', $ftime);
        if (count($ftime) < 3) {
            $count = count($ftime);
            for($i=$count;$i<=3;$i++) {
                $ftime[] = '00';
            }
        }
        return trim($fdate[2]).'-'.trim($fdate[1]).'-'.trim($fdate[0]).' '.trim($ftime[0]).':'.trim($ftime[1]).':'.trim($ftime[2]);
    }
}

if ( !function_exists('xform_anchor')) {
    function xform_anchor($uri, $title, $attributes) {
        $CI = &get_instance();
        $uri = trim($uri, '/');
        if ($CI->_model('user')->privilege($uri)) {
            return anchor($uri, $title, $attributes);
        }
    }
}

if ( !function_exists('xform_lookup')) {

	function xform_lookup($field_name, $sgroup = '', $selected = array(), $extra = '') {
		$sgroup = (empty($sgroup)) ? $field_name : $sgroup;
		
		$CI = &get_instance();
		$rows = $CI->db->query('SELECT * FROM sysparam WHERE sgroup = ?', array($sgroup))->result_array();
		$options = array();
                $options[''] = '(Please select)';
		foreach($rows as $row) {
		    $options[$row['skey']] = $row['svalue'];
		}
		return form_dropdown($field_name, $options, $selected, $extra);
	}

}


if ( !function_exists('xform_date')) {

	function xform_date($name, $options = array(), $extra = '') {
		$CI = &get_instance();
		$data = array(
		    'name' => $name,
            'extra' => $extra,
            'options' => $options,
		);
        if (!empty($options['default_today']) && empty($_POST[$name])) {
    		$_POST[$name] = mysql_current_date();
        }
		return $CI->load->view('helpers/xform_date', $data, true);
	}
}

if ( !function_exists('xform_model_lookup')) {

    function xform_model_lookup($field_name, $selected = array(), $extra = '') {
        $CI = &get_instance();
        $model_name = preg_replace('/(.*)_id$/', '$1', $field_name);

        $options = array('' => '(Please select)');
        $rows = $CI->db->get($model_name)->result_array();
        foreach($rows as $row) {
            $options[$row['id']] = $row['name'];
        }
        return form_dropdown($field_name, $options, $selected, $extra);
    }

}


if ( !function_exists('xform_radio')) {

    function xform_radio($field_name,$options=array(), $data=array(), $extra = '') {
        $CI = &get_instance();
        echo "<table><tr>";	
        foreach($options as $row) {
        	
        	$selected=(!empty($data)&&$data[$field_name]==$row['id'])?"checked=true":"";
        	echo "<td colspan='20px'><input style='width:5px;' type='radio' name='$field_name' $selected  $extra value='".$row['id']."'/>".$row['name']."&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>";
        }
        echo "</tr></table>";
       // return form_dropdown($field_name, $options, $selected, $extra);
    }

}
