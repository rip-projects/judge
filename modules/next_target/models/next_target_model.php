<?php
/**
 * Description of next_target_model
 *
 * @author generator
 */

class next_target_model extends app_base_model {
	
function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $this->_db()->start_cache();
        $CI=&get_instance();
		$user=$CI->_get_user();	
 
            $filter = array('user_id' =>$user['id'] );
            $this->_db()->where($filter);
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
    
}
