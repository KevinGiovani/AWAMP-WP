<?php
/*
Plugin Name: Store Closing
Plugin URI: http://www.woocommercestoreclosing.com
Description: Woocommerce Store Closing.
Version: 9.6.4
Author: Ozibal
Author URI: https://codecanyon.net/user/ozibal
Text Domain: store-closing
Domain Path: /languages
WC requires at least: 3.0.0
WC tested up to: 3.6.3
*/

/*
Copyright 2016 4gendesign.
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'WSC__StoreClosing' ) ) :

	class WSC__StoreClosing {

		private static $WSC__Plugin_Config;
		private static $WSC__Admin_Menu;
		private static $WSC__Admin_Pages;
		private static $WSC__Show_StoreClosing;
		private static $WSC__Options_Data;
		private static $WSC__Short_Code;
		
		public $WSC__Active_Plan;
				
		public function __construct() {
			
			$this->WSC__Defines();
			$this->WSC__Includes();
			$this->WSC__Init_Hook();
			$this->WSC__Options_Data();
			
		}
		
		
		public function WSC__Options_Data() {
			
			$WSC__Array = new WSC__Array();
			
			return  self::$WSC__Options_Data = $WSC__Array->WSC__Generate_Options_Data();
						
		}
		
		public function WSC__Active_Plan($WSC__Active_Plan = false) {
		
			/***** $WSC__Active_Plan *******
				
				store_check 				=> 'CLOSE' & 'OPEN' 
				active_plan 				=> 'Plan Info', 
				storeclosing_notification 	=> 'Base Notification', 
				storeclosing_preview		=> 'Notification Preview', 
				active_daily 				=> array('Active Day of Week'),
				active_time					=> array('Date', 'Time')
			
			*/
			
			if($WSC__Active_Plan == false ) {
				
				$WSC__Active_Plan = get_option( 'storeclosing_activeplan');
			
			} else {
			
				update_option( 'storeclosing_activeplan', $WSC__Active_Plan);
			
			}

			return $WSC__Active_Plan;
			
		}
		
		public function WSC__Show($comefrom = '') {
			
			return self::$WSC__Show_StoreClosing->storeclosing_show($comefrom);
			
		}
		
		private function WSC__Init_Hook() {
			add_action( 'plugins_loaded', array( $this, 'WSC__Init' ), 0 );
			
		}
		
		public function WSC__Init() {
			
			$this->WSC__TextDomain();
			
			self::$WSC__Plugin_Config 		= new WSC__Plugin_Config();
			self::$WSC__Show_StoreClosing 	= new WSC__Show_StoreClosing();
			self::$WSC__Short_Code			= new WSC__Short_Code();
			
			if( current_user_can('administrator') ) { 
				
				add_action ( 'admin_menu', array( $this, 'WSC__Admin_Menu'));
				
				//WSC__Admin_Backup Version: 9.5.9
				add_action( 'wp_ajax_wsc__admin_backup', array( $this, 'WSC__Admin_Backup' ) );
		
			} else {

				if( isset(self::$WSC__Options_Data['settings'][13]) && self::$WSC__Options_Data['settings'][13] != '' && self::$WSC__Options_Data['settings'][13] == self::WSC__Get_Role() ) {

					$WSC___role = get_role( self::$WSC__Options_Data['settings'][13] );
					$WSC___role->add_cap( trim(self::$WSC__Options_Data['settings'][13]) );
					
					add_filter( 'option_page_capability_'. WSC__SLUG.'_daily', array($this, 'WSC__Capability') );
					add_filter( 'option_page_capability_'. WSC__SLUG.'_upcoming',  array($this, 'WSC__Capability') );
					add_filter( 'option_page_capability_'. WSC__SLUG.'_manual',  array($this, 'WSC__Capability') );
					
					add_action( 'admin_menu', 
						function() { 
							self::WSC__Role_Menu(); 
						} 
					);
	
				}
		
			}

		}
		
		function WSC__Admin_Menu(){
			
			add_submenu_page( 'woocommerce', __( 'Store Closing', WSC__DOMAIN ), __( 'Store Closing', WSC__DOMAIN ), 'manage_options', WSC__SLUG, array( $this, 'WSC__Admin_Pages' ) );
		
		}
		
		public function WSC__Get_Role() {
			global $wp_roles;

			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			$role = array_shift( $roles );

			return isset( $wp_roles->role_names[ $role ] ) ? $role : FALSE;
		}
	
		function WSC__Role_Menu(){
		
			add_menu_page(
				__( 'Store Closing', WSC__DOMAIN ),
				__( 'Store Closing', WSC__DOMAIN ),
				'read',
				WSC__SLUG,
				array( $this, 'WSC__Admin_Pages' ),
				'dashicons-backup',
				'60.0'
			);

		}

		function WSC__Capability() {
			return 'read';
		}
	
		function WSC__Admin_Pages() {
		
			if(!isset(self::$WSC__Admin_Pages)){
				self::$WSC__Admin_Pages = new WSC__Admin_Pages();
			}

		}

		private function WSC__Defines() {

			// Plugin version
			if ( ! defined( 'WSC__VERSION' ) ) {
				define( 'WSC__VERSION', '9.6.4' );
			}
			
			// Plugin domain
			if ( ! defined( 'WSC__DOMAIN' ) ) {
				define( 'WSC__DOMAIN', 'store-closing' );
			}
			
			// Slug Name
			if ( ! defined( 'WSC__SLUG' ) ) {
				define( 'WSC__SLUG', 'storeclosing' );
			}
			
			// Long Name
			if ( ! defined( 'WSC__LONG_NAME' ) ) {
				define( 'WSC__LONG_NAME', 'Store Closing' );
			}

			// Plugin Folder Path
			if ( ! defined( 'WSC__PLUGIN_DIR' ) ) {
				define( 'WSC__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WSC__PLUGIN_URL' ) ) {
				define( 'WSC__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Basename
			if ( ! defined( 'WSC__PLUGIN_BASENAME' ) ) {
				define( 'WSC__PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'WSC__PLUGIN_FILE' ) ) {
				define( 'WSC__PLUGIN_FILE', __FILE__ );
			}
			
			// Document Link
			if ( ! defined( 'WSC__DOCUMENT_LINK' ) ) {
				define( 'WSC__DOCUMENT_LINK', 'http://www.woocommercestoreclosing.com/how-do-i-do/' );
			}
			
			// Support Link
			if ( ! defined( 'WSC__SUPPORT_LINK' ) ) {
				define( 'WSC__SUPPORT_LINK', 'https://codecanyon.net/item/woocommerce-store-closing/19398781/support' );
			}

		}
		
		public function WSC__TextDomain() {

			$WSC__lang_dir = dirname( plugin_basename( WSC__PLUGIN_FILE ) ) . '/languages/';
			$WSC__lang_dir = apply_filters( 'wsp_languages_directory', $WSC__lang_dir );

			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, WSC__DOMAIN );

			unload_textdomain( WSC__DOMAIN );
			load_textdomain( WSC__DOMAIN, WP_LANG_DIR . '/store-closing/store-closing-' . $locale . '.mo' );
			load_plugin_textdomain( WSC__DOMAIN, false, $WSC__lang_dir );

		}

		private function WSC__Includes() {
			global $wsp_options;
						
			require_once WSC__PLUGIN_DIR . 'includes/storeclosing-plugin.php';
			require_once WSC__PLUGIN_DIR . 'includes/class-storeclosing-plugin-config.php';
			require_once WSC__PLUGIN_DIR . 'includes/storeclosing-data.php';
			require_once WSC__PLUGIN_DIR . 'includes/storeclosing-shortcode.php';
			require_once WSC__PLUGIN_DIR . 'includes/storeclosing-show.php';
			
			if ( is_admin() ) {
				
				require_once WSC__PLUGIN_DIR . 'includes/admin/admin-post-action.php';
				require_once WSC__PLUGIN_DIR . 'includes/admin/admin.php';
				
				
				// Tips
				require_once WSC__PLUGIN_DIR . 'includes/admin/tips.php';
				
			}

		}
		
		//WSC__Admin_Backup
		public function WSC__Admin_Backup() {
			
			if( isset($_POST['WSC__']) and preg_match('#.+#',$_POST['WSC__']) ){
				
				switch ($_POST['WSC__']) {
					case 'backup':
					
						update_option( WSC__SLUG . '_backup', date(get_option('date_format'), current_time( 'timestamp', 0 ) ).' '.date('H:i', current_time( 'timestamp', 0 ) ) );
		
						update_option( WSC__SLUG . '_backup_daily', get_option( WSC__SLUG.'_daily' ) );
						update_option( WSC__SLUG . '_backup_upcoming', get_option( WSC__SLUG.'_upcoming' ) );
						update_option( WSC__SLUG . '_backup_manual', get_option( WSC__SLUG.'_manual' ) );
						update_option( WSC__SLUG . '_backup_popup', get_option( WSC__SLUG.'_popup' ) );
						update_option( WSC__SLUG . '_backup_exclude', get_option( WSC__SLUG.'_exclude' ) );
						update_option( WSC__SLUG . '_backup_notification', get_option( WSC__SLUG.'_notification' ) );
						update_option( WSC__SLUG . '_backup_settings', get_option( WSC__SLUG.'_settings' ) );
						
						
						echo date(get_option('date_format'), current_time( 'timestamp', 0 ) ).' '.date('H:i', current_time( 'timestamp', 0 ) );
						
						break;
					case 'restore':
		
						update_option( WSC__SLUG . '_daily', get_option( WSC__SLUG.'_backup_daily' ) );
						update_option( WSC__SLUG . '_upcoming', get_option( WSC__SLUG.'_backup_upcoming' ) );
						update_option( WSC__SLUG . '_manual', get_option( WSC__SLUG.'_backup_manual' ) );
						update_option( WSC__SLUG . '_popup', get_option( WSC__SLUG.'_backup_popup' ) );
						update_option( WSC__SLUG . '_exclude', get_option( WSC__SLUG.'_backup_exclude' ) );
						update_option( WSC__SLUG . '_notification', get_option( WSC__SLUG.'_backup_notification' ) );
						update_option( WSC__SLUG . '_settings', get_option( WSC__SLUG.'_backup_settings' ) );
												
						break;
						
					case 'cleardb':
		
						$cleardb = new WSC__Array();
						$cleardb->WSC__Upgrade_Option_Data('cleardb');
												
						break;
					default:
						break;
				}
			}
			

			die();
		}
		

	}

endif; // End if class_exists check

$__WSC = new WSC__StoreClosing();
?>