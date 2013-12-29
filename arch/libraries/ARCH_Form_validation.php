<?php

/**
 * ARCH_Form_validation.php
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

class ARCH_Form_validation extends CI_Form_validation {
	public function set_rules($field, $label = '', $rules = '') {
		// No reason to set rules if we have no POST data
		if (count($_POST) == 0) {
			return $this;
		}

		if (is_array($field) && ($field!== array_values($field))) {
			foreach ($field as $key => $value) {
				$label = (!empty($value[1])) ? $value[1] : humanize($key);
				parent::set_rules($key, $label, $value[0]);
			}
			return $this;
		}
		parent::set_rules($field, $label, $rules);
	}
}