<?php
/**
 * Description of next_target *
 * @author generator
 */

class next_target extends app_crud_controller {
	function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('id','next_year_target', 'how_to');
        $config['formats'] = array('row_detail','','', '');
        return $config;
    }

	function _save($id= null){
			
		
		if ($_POST) {
		 	if($this->_validate()){
		 		$CI=&get_instance();
				$user=$CI->_get_user();
				$_POST['user_id']= $user['id'];
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
    
	
 	function detail($id) {
        $this->_data['field_data'] = $this->_model()->field_data();
        $this->_data['data'] =$data= $this->_model()->get($id);
 		$CI=&get_instance();
		$user=$CI->_get_user();
        if($data['user_id']!=$user['id']){
        	$this->_model('scoring')->saveIseng($user,$id,'next_target');
        	redirect(site_url("next_target/listing"));
        }
        
        
    }
	
	
    
    
    
    
}
