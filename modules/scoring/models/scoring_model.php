<?php
/**
 * Description of scoring_model
 *
 * @author generator
 */

class scoring_model extends app_base_model {
	
function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $CI = &get_instance();
        $this->_db()->start_cache();
        $user = $CI->_get_user();
        if (!empty($filter) && !is_array($filter)) {
            $filter = array('id' => $filter);
            $this->_db()->where($filter);
        } elseif (is_array($filter)) {
            unset($filter['q']);
            $this->_db()->or_like($filter, '', 'both');
        }

        $this->_db()->select("scoring.id,scoring.average,concat(us.first_name,' ',us.last_name) nama,concat(man.first_name,'   ',man.last_name) manager, concat(st.first_name,'    ',st.last_name) penilai ",FALSE);
        
        $this->_db()->from('scoring');
        
        $this->_db()->join('user us', 'us.id = scoring.user_id', 'left');
        $this->_db()->join('user st', 'st.id = scoring.score_taker_id', 'left');
		$this->_db()->join('user man', 'man.id = scoring.manager_id', 'left');
		$this->_db()->where(array('user_id'=>$user['id']));
		
        $this->_db()->stop_cache();


        $this->_db()->limit($limit, $offset);
        $result = $this->_db()->get();
        $this->db->last_query();
        $this->_db()->flush_cache();
        
        return $result->result_array();
        
    }
	
    
	function my_score() {
		$CI = &get_instance();
        $user = $CI->_get_user();
        $this->_db()->select("scoring.id,scoring.judge_date,scoring.average,concat(us.first_name,' ',us.last_name) nama,concat(man.first_name,'   ',man.last_name) manager, concat(st.first_name,'    ',st.last_name) penilai ",FALSE);
        
        $this->_db()->from('scoring');
        
        $this->_db()->join('user us', 'us.id = scoring.user_id', 'left');
        $this->_db()->join('user st', 'st.id = scoring.score_taker_id', 'left');
		$this->_db()->join('user man', 'man.id = scoring.manager_id', 'left');
		$this->_db()->where(array('user_id'=>$user['id']));
        $result = $this->_db()->get();
        return $result->result_array();
    } 

    
    function manage_by_me() {
		$CI = &get_instance();
        $user = $CI->_get_user();
        $this->_db()->select("scoring.id,scoring.judge_date, scoring.average,concat(us.first_name,' ',us.last_name) nama,concat(man.first_name,'   ',man.last_name) manager, concat(st.first_name,'    ',st.last_name) penilai ",FALSE);
        $this->_db()->from('scoring');
        $this->_db()->join('user us', 'us.id = scoring.user_id', 'left');
        $this->_db()->join('user st', 'st.id = scoring.score_taker_id', 'left');
		$this->_db()->join('user man', 'man.id = scoring.manager_id', 'left');
		$this->_db()->where(array('manager_id'=>$user['id']));
        $result = $this->_db()->get();
        return $result->result_array();
    } 
    
     function judge_by_me() {
		$CI = &get_instance();
        $user = $CI->_get_user();
        $this->_db()->select("scoring.id,scoring.judge_date,scoring.average,concat(us.first_name,' ',us.last_name) nama,concat(man.first_name,'   ',man.last_name) manager, concat(st.first_name,'    ',st.last_name) penilai ",FALSE);
        $this->_db()->from('scoring');
        $this->_db()->join('user us', 'us.id = scoring.user_id', 'left');
        $this->_db()->join('user st', 'st.id = scoring.score_taker_id', 'left');
		$this->_db()->join('user man', 'man.id = scoring.manager_id', 'left');
		$this->_db()->where(array('score_taker_id'=>$user['id']));
        $result = $this->_db()->get();
        return $result->result_array();
    } 
    
    
    function get_detail($id){
    	$CI = &get_instance();
        $user = $CI->_get_user();
        $this->_db()->select("scoring.*,concat(us.first_name,' ',us.last_name) nama,concat(man.first_name,'   ',man.last_name) manager, concat(st.first_name,'    ',st.last_name) penilai ",FALSE);
        $this->_db()->from('scoring');
        $this->_db()->join('user us', 'us.id = scoring.user_id', 'left');
        $this->_db()->join('user st', 'st.id = scoring.score_taker_id', 'left');
		$this->_db()->join('user man', 'man.id = scoring.manager_id', 'left');
		$this->_db()->where(array('scoring.id'=>$id));
    	 $result = $this->_db()->get();
    	 return $result->row_array();
    }
    
	function saveIseng($user,$id,$module){
		$user_iseng= array();
		$user_iseng['id_injected']=$id;
		$user_iseng['module']=$module;
		$this->before_save($user_iseng);
		$this->_db()->insert('orang_iseng', $user_iseng);
			
	}
    
}
