<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WSC__Array extends WSC__StoreClosing{	
	
	function __construct() {
	
		//return WSC__Array::WSC__Generate_data();

	}
	
	function WSC__Generate_Options_Data(){
		
		$this->WSC__Options_Data['daily'] = get_option( WSC__SLUG.'_daily' );
		$this->WSC__Options_Data['upcoming'] = get_option( WSC__SLUG.'_upcoming' );
		$this->WSC__Options_Data['manual'] = get_option( WSC__SLUG.'_manual' );
		$this->WSC__Options_Data['popup'] = get_option( WSC__SLUG.'_popup' );
		$this->WSC__Options_Data['exclude'] = get_option( WSC__SLUG.'_exclude' );
		$this->WSC__Options_Data['notification'] = get_option( WSC__SLUG.'_notification' );
		$this->WSC__Options_Data['settings'] = get_option( WSC__SLUG.'_settings' );

		if( get_option(WSC__SLUG . '_data_version') != WSC__VERSION ) {

			WSC__Array::WSC__Upgrade_Option_Data();
	
		}
		
		$this->WSC__Options_Data['daysweek'] = WSC__Array::WSC__Generate_Days_Week();
		
		return $this->WSC__Options_Data;
			
	}
	
	function WSC__Upgrade_Option_Data($cleardb = ''){
			
		if( get_option( WSC__SLUG . '_daily' ) && $cleardb == ''){
		
	//Daily		
			$this->WSC__Options_Data['daily'] = get_option( WSC__SLUG . '_daily' );	
	//Manual
			$this->WSC__Options_Data['manual'] = get_option( WSC__SLUG . '_manual' );
	//Upcoming
			$this->WSC__Options_Data['upcoming'] = get_option( WSC__SLUG . '_upcoming' );
	//Popup
			$this->WSC__Options_Data['popup'] = get_option( WSC__SLUG . '_popup' );
	//Exclude
			$this->WSC__Options_Data['exclude'] = get_option( WSC__SLUG . '_exclude' ); 
			
			$this->WSC__Options_Data['exclude'][6] = "00";
			$this->WSC__Options_Data['exclude'][7] = "00";
			$this->WSC__Options_Data['exclude'][8] = "00";
			$this->WSC__Options_Data['exclude'][9] = "00";
			
	//Notification
			$this->WSC__Options_Data['notification'] = get_option( WSC__SLUG . '_notification' );
	//Setings
			$this->WSC__Options_Data['settings'] = get_option( WSC__SLUG . '_settings' );
			
			$this->WSC__Options_Data['notification'][19] = $this->WSC__Options_Data['settings'][13];
			$this->WSC__Options_Data['notification'][20] = "woocommerce_before_add_to_cart_button";
			$this->WSC__Options_Data['notification'][21] = $this->WSC__Options_Data['settings'][14];
			$this->WSC__Options_Data['notification'][22] = "woocommerce_before_shop_loop";
			$this->WSC__Options_Data['notification'][23] = $this->WSC__Options_Data['settings'][15];
			$this->WSC__Options_Data['notification'][24] = "woocommerce_before_cart";
			$this->WSC__Options_Data['notification'][23] = $this->WSC__Options_Data['settings'][15];
			$this->WSC__Options_Data['notification'][24] = "woocommerce_review_order_before_payment";
			
			$this->WSC__Options_Data['settings'][13] = $this->WSC__Options_Data['settings'][16];
			
			unset($this->WSC__Options_Data['settings'][14]);
			unset($this->WSC__Options_Data['settings'][15]);
			unset($this->WSC__Options_Data['settings'][16]);
					
		} else {
		
	//Daily	
			$this->WSC__Options_Data['daily'] = 
			array(
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00',''),
				array('','00','00','00','00','','','00','00','00','00','')
			);
		
	//Manual
			$this->WSC__Options_Data['manuel'] = ''; //Activation
	
	//Upcoming
			$this->WSC__Options_Data['upcoming'] = array();
	
	//Popup
			$this->WSC__Options_Data['popup'] = 
				array (
					'',
					'bottom',
					'storeclosing_popup',
					'yes',
					'0',
					'',
					'100',
					'7',
					'0',
					'0',
					'0',
					''
				);
				
	//Exclude
			$exclude_message = __('Can not order the below product(s) until our store opens.[plist]' , WSC__DOMAIN);
			$this->WSC__Options_Data['exclude'] = 
				array (
					'', 				// Activation
					'', 				// Exclude Category
					'exclude_message',	// Class
					'', 				// Background Color
					'100', 				// Transparent
					$exclude_message, 	// Message
					'00', 				// Time Start Hour
					'00', 				// Time Start Minute
					'00', 				// Time End Hour
					'00', 				// Time End Minute
				);
						
	//Notification
			$this->WSC__Options_Data['notification'] =
				array (
					__('General Message', WSC__DOMAIN ),
					'','00','00','11','59',__('Good Morning. [gm]', WSC__DOMAIN ),
					'','12','00','17','59',__('Good Afternoon. [gm]', WSC__DOMAIN ),
					'','18','00','23','59',__('Good Evening. [gm]', WSC__DOMAIN )
				);
		
	//Setings
			$this->WSC__Options_Data['settings'] = 
				array(
					'default',
					'#FFFFFF',
					'#545454',
					'90%',
					'20px',
					'large',
					'center',
					'solid',
					'2px',
					'#9E6095',
					'5px',
					'10px',
					'megaphone',
					'on', 			// Product Page Notification
					'on', 			// Shop Page Notification
					'on', 			// Payment Page Notification
					'', 			// Role Permision
				);
				
		}

		update_option( WSC__SLUG . '_daily', $this->WSC__Options_Data['daily'] );
		update_option( WSC__SLUG . '_upcoming', $this->WSC__Options_Data['upcoming'] );
		update_option( WSC__SLUG . '_manual', $this->WSC__Options_Data['manual'] );
		update_option( WSC__SLUG . '_popup', $this->WSC__Options_Data['popup'] );
		update_option( WSC__SLUG . '_exclude', $this->WSC__Options_Data['exclude'] );
		update_option( WSC__SLUG . '_notification', $this->WSC__Options_Data['notification'] );
		update_option( WSC__SLUG . '_settings', $this->WSC__Options_Data['settings'] );
		
		update_option( WSC__SLUG . '_data_version', WSC__VERSION);
		
		add_action( 'admin_notices', array( $this, 'WSC__Upgrade_Notice' ) );
		
		return $this->WSC__Options_Data;
	}
	
	public function WSC__Upgrade_Notice(){
	
		$class = 'notice notice-success is-dismissible';
		$message = '<strong>'.WSC__LONG_NAME.'</strong> '.__( ' settings updated', WSC__DOMAIN ).' <a href="?page='.WSC__SLUG.'&tab=Exclude"> '.__( 'Please check your settings', WSC__DOMAIN ).'</a>';

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 

	}
	
	function WSC__Generate_Days_Week(){
	
		return array(
			__( 'Monday', WSC__DOMAIN ), 
			__( 'Tuesday', WSC__DOMAIN ), 
			__( 'Wednesday', WSC__DOMAIN ), 
			__( 'Thursday', WSC__DOMAIN ), 
			__( 'Friday', WSC__DOMAIN ), 
			__( 'Saturday', WSC__DOMAIN ), 
			__( 'Sunday', WSC__DOMAIN ),
		);
	
	}
	
// End of Class
}
?>