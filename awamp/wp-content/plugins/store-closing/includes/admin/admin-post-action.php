<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function WSC__get_data_from_database($WSC_id, $WSC_Db){
	
	if($WSC_id) {
		$db = $WSC_Db->storeclosingdb_getrow("*","WHERE id = '".$WSC_id."'");
	
		foreach ($db as $tag => $value) {		
			$WSC_db_array[$tag] = $value;
		}

		return $WSC_db_array;
	}
	
}

function WSC__admin_post_action($data, $WSC_Db){
	if ($data ){
	
		$WSC_act = (isset($data['WSC_act'])) ? $data['WSC_act'] : '';
		$WSC_id = (isset($data['WSC_id'])) ? $data['WSC_id'] : '';
		$WSC_role = (isset($data['WSC_role'])) ? $data['WSC_role'] : '';
		$WSC_user = (isset($data['WSC_user'])) ? $data['WSC_user'] : '';
		$WSC_product = (isset($data['WSC_product'])) ? $data['WSC_product'] : '';
		$WSC_process_type = (isset($data['WSC_process_type'])) ? $data['WSC_process_type'] : '';
		$WSC_process_method = (isset($data['WSC_process_method'])) ? $data['WSC_process_method'] : '';
		$WSC_amount = (isset($data['WSC_amount'])) ? $data['WSC_amount'] : '';
		$WSC_cond = (isset($data['WSC_cond'])) ? $data['WSC_cond'] : '';
		$WSC_cond_type = (isset($data['WSC_cond_type'])) ? $data['WSC_cond_type'] : '';
		$WSC_errormessage = (isset($data['WSC_errormessage'])) ? $data['WSC_errormessage'] : '';
		$WSC_process = (isset($data['WSC_process'])) ? $data['WSC_process'] : '';
		
		switch ($data['WSC_process']) {
			
			case 'WSC_addrole':
				
				$WSC_Db->storeclosingdb_add('on', 'role', '', 'discount', 'rate',0);
				break;
				
			case 'WSC_adduser':
				
				$WSC_Db->storeclosingdb_add('on', 'user', '', 'discount', 'rate',0);
				break;
				
			case 'WSC_addproduct':
				
				$WSC_Db->storeclosingdb_add('on', 'product', '', 'discount', 'rate',0);
				break;
				
			case 'WSC_delete':
				
				$WSC_Db->storeclosingdb_delete( "id = $WSC_id" );
				
				$WSC_process = 'WSC_update';
				
				break;
			
			case 'WSC_act':
				
				$WSC_Db->storeclosingdb_update(array('act'=>$WSC_act), array('id'=>$WSC_id));
				
				$WSC_process = 'WSC_update';
				
				break;
				
			case 'WSC_up':
				
				$data_db = WSC__get_data_from_database($WSC_id, $WSC_Db);
				
				$priority = $data_db['priority'];
				$priority = ($priority > 1 ) ? $priority - 1 : $priority;
			
				$WSC_Db->storeclosingdb_replace(array('priority'=>$priority), array('id'=>$WSC_id));
				
				$WSC_errormessage = __('New rank', WSC__DOMAIN ).' : '.$priority;
				
				break;
				
			case 'WSC_down':
				
				$data_db = WSC__get_data_from_database($WSC_id, $WSC_Db);
				
				$priority = $data_db['priority'];
				$priority = $priority + 1 ;
				
				$WSC_Db->storeclosingdb_replace(array('priority'=>$priority), array('id'=>$WSC_id));
	
				$WSC_errormessage = __('New rank', WSC__DOMAIN ).' : '.$priority;
				
				break;
				
			case 'WSC_role':
				
				$WSC_Db->storeclosingdb_update(array('selected'=>$WSC_role), array('id'=>$WSC_id));
				
				$WSC_process = 'WSC_update';
				
				break;
				
			case 'WSC_update':
			
				$db_selected = '';
				$db_process_type = $WSC_process_type;
				$db_process_method = $WSC_process_method;
				$db_amount = $WSC_amount;
				$db_cond_type = $WSC_cond_type;
				$db_cond = $WSC_cond;
				
				if ($WSC_role != ''){
			
					$db_selected = $WSC_role;
					
					$WSC_cond = explode(' - ', $WSC_cond);
					$WSC_cond = reset($WSC_cond);
					$db_cond = $WSC_cond;
					
					$exists = $WSC_Db->storeclosingdb_exists("*","WHERE selected = '".$db_selected."' and id != '".$WSC_id."'");
					if($exists == TRUE ) {
				
						$WSC_errormessage = '! '.__('There is record for this role', WSC__DOMAIN );
						
						
						break;
			
					}
		
				}else if ($WSC_user != ''){
			
					$WSC_user = explode(' - ', $WSC_user);
					$WSC_user = reset($WSC_user);
					$db_selected = $WSC_user;
					
					$WSC_cond = explode(' - ', $WSC_cond);
					$WSC_cond = reset($WSC_cond);
					$db_cond = $WSC_cond;
			
					$exists = $WSC_Db->storeclosingdb_exists("*","WHERE selected = '".$db_selected."' and id != '".$WSC_id."'");
					if($exists == TRUE ) {
				
						$WSC_errormessage = '! '.__('There is record for this user', WSC__DOMAIN );
						
						break;
			
					}
			
				}else if ($WSC_product != '') {
					
					$WSC_product = explode(' - ', $WSC_product);
					$WSC_product = reset($WSC_product);
					$db_selected = $WSC_product;
				
				}
				
				$WSC_errormessage = __('Record was changed.', WSC__DOMAIN );
				
				$WSC_Db->storeclosingdb_update(
						array( 
							'selected' => $db_selected,
							'process_type' => $db_process_type, 
							'process_method' => $db_process_method, 
							'amount' => $db_amount,
							'cond_type' => $db_cond_type,
							'cond' => $db_cond,
						), 
						array( 
							'id' => $WSC_id 
						)
				);
				
				break;				
				
			default:
				break;
				
		}


// Return Result 
	$WSC_return_array = array(
		'WSC_id' => isset($WSC_id) ? $WSC_id : '',
		'WSC_role' => isset($WSC_role) ? $WSC_role : '',
		'WSC_user' => isset($WSC_user) ? $WSC_user : '',
		'WSC_product' => isset($WSC_product) ? $WSC_product : '',
		'WSC_process_type' => isset($WSC_process_type) ? $WSC_process_type : '',
		'WSC_process_method' => isset($WSC_process_method) ? $WSC_process_method : '',
		'WSC_amount' => isset($WSC_amount) ? $WSC_amount : '',
		'WSC_cond' => isset($WSC_cond) ? $WSC_cond : '',
		'WSC_cond_type' => isset($WSC_cond_type) ? $WSC_cond_type : '',
		'WSC_errormessage' => isset($WSC_errormessage) ? $WSC_errormessage : '',
		'WSC_process' => isset($WSC_process) ? $WSC_process : ''
	);
	
	return $WSC_return_array;
// *****
	
	}
	
}
?>