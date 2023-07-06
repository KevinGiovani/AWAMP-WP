<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WSC__Plugin_Config extends WSC__StoreClosing{
	
	public function __construct() {
		
		add_action( 'admin_init', array( $this, 'options_hub__WSC'));
	
		if ( is_admin() ) { 
			// Loads styles & scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'adminscripts__WSC'));
			
			// Dashboard Info
			add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widgets__WSC'));
			
			//js scripts
			add_action( 'init', array( $this, 'scripts__WSC'));
			
		} else {
			// Loads styles
			add_action( 'init', array( $this, 'styles__WSC') );
			
			//js scripts
			add_action( 'init', array( $this, 'scripts__WSC'));
		}

	}
	
	public function options_hub__WSC() {

		register_setting( WSC__SLUG.'_daily', WSC__SLUG.'_daily' );
		register_setting( WSC__SLUG.'_upcoming', WSC__SLUG.'_upcoming' );
		register_setting( WSC__SLUG.'_manual', WSC__SLUG.'_manual' );
		register_setting( WSC__SLUG.'_popup', WSC__SLUG.'_popup' );
		register_setting( WSC__SLUG.'_exclude', WSC__SLUG.'_exclude' );
		register_setting( WSC__SLUG.'_notification', WSC__SLUG.'_notification' );
		register_setting( WSC__SLUG.'_settings', WSC__SLUG.'_settings' );

	}

	public function adminscripts__WSC() {
	
		$Get_Screen = get_current_screen();
		if ( strpos($Get_Screen->base, WSC__SLUG) === false) { return; }
		
		wp_register_script( 'WSC__admin_js', WSC__PLUGIN_URL .  'includes/admin/js/admin.js', array( 'jquery-ui-tooltip'), '1.0', false );
		wp_localize_script( 'WSC__admin_js', 'Ajax_Url', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_enqueue_script( array( 'jquery', 'jquery-ui-tooltip', 'jquery-ui-slider', 'jquery-ui-datepicker', 'WSC__admin_js') );

		wp_enqueue_style( 'jquery-ui-styles' ,'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		wp_enqueue_style( 'WSC__admin_style' , WSC__PLUGIN_URL . 'includes/admin/admin.css');
		
	}


	public function styles__WSC(){

		wp_enqueue_style( WSC__SLUG.'_style', WSC__PLUGIN_URL.'includes/'.WSC__SLUG.'-style.css');

	}
	
	public function scripts__WSC() {
		
		wp_register_script( WSC__SLUG.'_script', WSC__PLUGIN_URL .  'includes/'.WSC__SLUG.'.js' );
		wp_enqueue_script( array( 'jquery', WSC__SLUG.'_script') );
				
	}
	
	function dashboard_widgets__WSC() {
		global $wp_meta_boxes;
	 
		wp_add_dashboard_widget('custom_help_widget', 'WooCommerce Store Closing', array($this,'dashboard_info__WSC'));
	}
	 
	function dashboard_info__WSC() {
		$storeclosing = $this->WSC__Active_Plan();
	?>
		<div style="display:flow-root;line-height: 24px;" class="dashicons-before dashicons-backup store-closing-title"> <?php echo ($storeclosing['active_plan'] != '') ? "<span style='font-size: 20px;color: #9E6095;'>" . __( WSC__LONG_NAME, WSC__DOMAIN ) . "</span> | <span style='background-color: #F00;color: #FFF;padding: 5px;'>" . __( 'Store is closed now', WSC__DOMAIN ) . "</span>" : "<span style='font-size: 20px;color: #9E6095;'>" . __( WSC__LONG_NAME, WSC__DOMAIN ) . "</span>" ?>
		<hr />
		<?php if($storeclosing['active_plan'] != ''){ 
		$active_plan = str_replace('&','<br>&nbsp;&nbsp;&nbsp;',$storeclosing['active_plan']);
		?>
			<div class="activeplan"> <span style="font-size:10px;margin-right:5px;color:#ABFF00;background-color: #ABFF00;border-radius: 50%;box-shadow: rgba(0, 0, 0, 0.2) 0 0px 3px 1px, inset darkgray 0px -1px 0px, #89FF00 0 2px 12px;">&#9679;</span> <span class="activeplan_info"><?php echo __( 'Active', WSC__DOMAIN ).' | '.$active_plan; ?></span></div>
		<?php } else { echo __('Store is currently open', WSC__DOMAIN ); }?>
		<a href="admin.php?page=storeclosing" class="button button-primary" style="float: right;"><?php echo __( 'Settings', WSC__DOMAIN ) ?></a>
		</div>
	<?php
	}


}
?>