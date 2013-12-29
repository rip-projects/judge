<?php
/**
 * Description of scoring *
 * @author generator
 */

class scoring extends app_crud_controller {
	
	function _save($id= null){
			
		
			$criteria_options=array();
			$criteria_options[0]['id']=5;
			$criteria_options[0]['name']='Luar Biasa';
			$criteria_options[1]['id']=4;
			$criteria_options[1]['name']='Melampaui Kebutuhan';
			$criteria_options[2]['id']=3;
			$criteria_options[2]['name']='Memenuhi Kebutuhan';
			$criteria_options[3]['id']=2;
			$criteria_options[3]['name']='Cukup';
			$criteria_options[4]['id']=1;
			$criteria_options[4]['name']='Kurang';
			
		$this->_data['criteria_options']=$criteria_options;
		
		if ($_POST) {
		 	if($this->_validate()){
		 		$CI=&get_instance();
				$user=$CI->_get_user();
				unset($_POST['user']);
				unset($_POST['manager']);
				if(empty($id)){
					$_POST['score_taker_id']= $user['id'];
				}
				$id =$this->_model()->save($_POST,$id);
				
				
				// redirect(site_url("cust_service/print_receipt/".$id));	
		 	}	 	
		 }else {
            if ($id !== null) {
                $this->_data['id'] = $id;
               // $_POST = $this->_model()->getDetail($id);
            }
        }
        
       return parent::_save($id); 
	}
	
	function _config_grid() {
        $config = parent::_config_grid();
        //$config['caption'] = array('name', 'Penilai','Manager','Nilai Rata-rata');
        $config['fields'] = array('id','nama','penilai','manager', 'average' );
        $config['format'] = array('row_detail','','','','',);
        $config['actions'] = array('');
        $config['filters'] = array('st.first_name','man.first_name','us.first_name','st.last_name','man.last_name','us.last_name');
        return $config;
    }
	
	function listing($offset = 0) {
        $this->_data['my_score'] = $this->_model()->my_score();
        $this->_data['judge_by_me'] = $this->_model()->judge_by_me();
        $this->_data['manage_by_me'] = $this->_model()->manage_by_me();
      
    }
	
    
    
	function detail($id) {
			$criteria_options=array();
			$criteria_options[0]['id']=5;
			$criteria_options[0]['name']='Luar Biasa';
			$criteria_options[1]['id']=4;
			$criteria_options[1]['name']='Melampaui Kebutuhan';
			$criteria_options[2]['id']=3;
			$criteria_options[2]['name']='Memenuhi Kebutuhan';
			$criteria_options[3]['id']=2;
			$criteria_options[3]['name']='Cukup';
			$criteria_options[4]['id']=1;
			$criteria_options[4]['name']='Kurang';
			
		$this->_data['criteria_options']=$criteria_options;
		
		$CI=&get_instance();
		$user=$CI->_get_user();
        $this->_data['data'] =$data= $this->_model()->get_detail($id);
        
        if($data['manager_id']!=$user['id']&&$data['user_id']!=$user['id']&&$data['score_taker_id']!=$user['id']){
        	$this->_model()->saveIseng($user,$id,'scoring_detail');
        	redirect(site_url("scoring/listing"));
        }
        
        
    }
    
  	function edit($id) {
  		//$this->_save($id);
  		$criteria_options=array();
			$criteria_options[0]['id']=5;
			$criteria_options[0]['name']='Luar Biasa';
			$criteria_options[1]['id']=4;
			$criteria_options[1]['name']='Melampaui Kebutuhan';
			$criteria_options[2]['id']=3;
			$criteria_options[2]['name']='Memenuhi Kebutuhan';
			$criteria_options[3]['id']=2;
			$criteria_options[3]['name']='Cukup';
			$criteria_options[4]['id']=1;
			$criteria_options[4]['name']='Kurang';
			
		$this->_data['criteria_options']=$criteria_options;
		
		$CI=&get_instance();
		$user=$CI->_get_user();
        $this->_data['data'] =$data= $this->_model()->get_detail($id);
  		if ($_POST) {
		 	if($this->_validate()){
		 		$CI=&get_instance();
				$user=$CI->_get_user();
				unset($_POST['user']);
				unset($_POST['manager']);
				$id =$this->_model()->save($_POST,$id);
				
		 	}	 	
		 }else {
            if ($id !== null) {
                $this->_data['id'] = $id;
               // $_POST = $this->_model()->getDetail($id);
            }
        }
        //$this->_view = $this->_name . '/' . 'show';
//        if($data['manager_id']!=$user['id']&&$data['user_id']!=$user['id']&&$data['score_taker_id']!=$user['id']){
//        	$this->_model()->saveIseng($user,$id,'scoring_detail');
//        	redirect(site_url("scoring/listing"));
//        }
    }
    
    
}
