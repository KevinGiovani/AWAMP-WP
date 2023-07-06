<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WSC__Admin_Pages extends WSC__StoreClosing{

	private static $mobile_theme = false;
	private static $storeclosing_activeplan;
	
	private static $timezoneshow;
	private static $day_number;
	
	function __construct() {
		
		$this->WSC__Options_Data = parent::WSC__Options_Data();
		
		$this->WSC__Active_plan = parent::WSC__Active_plan();
		
		self::$timezoneshow =  date('H:i', current_time( 'timestamp', 0 ) );
		self::$day_number = date('N', current_time( 'timestamp', 0 ) ) - 1;

		self::WSC__Admin_Mobil_Check();
		self::WSC__Admin_Tabs();
	
	}
		
	function WSC__Admin_Tabs() {

		if (isset($_GET['dismiss']) && preg_match('#.+#',$_GET['dismiss']) and $_GET['dismiss'] == 'RemindMe'){	//v9.5.6
				update_option( 'wsc_dismiss','RemindMe');
		}
	
	?>
		<form id="WSC__Settings_Form" method="post" action="options.php" class="store-closing-form">
		
		<div id="store-closing-container">
		<!-- Head -->	
		<div class="store-closing-head">
			<div class="dashicons-before dashicons-backup store-closing-title"> <?php echo __( 'Store Closing', WSC__DOMAIN ) ?>
			<?php if($this->WSC__Active_plan['active_plan'] != ''){ ?>
				<div class="activeplan"> 
					<span class="active_dot">&#9679;</span> 
					
					<span class="activeplan_info"><?php echo __( 'Active', WSC__DOMAIN ).' | '.$this->WSC__Active_plan['active_plan']; ?> 
					
					</span>
				</div>
			<?php } ?>
			</div>	
			<div class="store-closing-currenttime">
				<a href="options-general.php">
				<span title="<?php echo __( 'Current Time (from Wordpress Setting)', WSC__DOMAIN ) ?>" class="dashicons-before dashicons-info"></span>
				<?php echo self::$timezoneshow ?>
				<?php echo (get_option('timezone_string') != '') ? " ( ".get_option('timezone_string')." )" : ''; ?>
				</a>
			</div>
			<h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=" class="nav-tab <?php echo $_GET[ 'tab' ] == '' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Daily', WSC__DOMAIN ) ?></a>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Upcoming" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Upcoming' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Upcoming', WSC__DOMAIN ) ?></a>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Manual" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Manual' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Manual', WSC__DOMAIN ) ?></a>
            
            <?php if( current_user_can('administrator') ) { ?>		
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Popup" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Popup' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Popup', WSC__DOMAIN ) ?></a>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Exclude" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Exclude' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Exclude', WSC__DOMAIN ) ?></a>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Notification" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Notification' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Notification', WSC__DOMAIN ) ?></a>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=Settings" class="nav-tab <?php echo $_GET[ 'tab' ] == 'Settings' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'Settings', WSC__DOMAIN ) ?></a>
            <?php } ?>
            
            <?php if (!is_plugin_active( 'remind-me/remindme.php' ) && !get_option('wsc_dismiss') == 'RemindMe'){ ?>
            <a href="?page=<?php echo WSC__SLUG; ?>&tab=RemindMe" class="remindme-tab nav-tab <?php echo $_GET[ 'tab' ] == 'RemindMe' ? 'nav-tab-active' : ''; ?>"><?php echo __( 'RemindMe', WSC__DOMAIN ) ?></a>
            <?php } ?>
        </h2>
		</div>
		
		<?php 
		
		if(isset($_GET[ 'tab' ]) and preg_match('#.+#',$_GET[ 'tab' ]) and $_GET[ 'tab' ] != ''){
		
			switch ($_GET[ 'tab' ]) {
				case 'Daily':
					self::WSC__Admin_Daily();
					break;
				case 'Upcoming':
					self::WSC__Admin_Upcoming();
					break;
				case 'Manual':
					self::WSC__Admin_Manual();
					break;
				case 'Popup':
					self::WSC__Admin_Popup();
					break;
				case 'Exclude':
					self::WSC__Admin_Exclude();
					break;
				case 'Notification':
					self::WSC__Admin_Notification();
					break;
				case 'Settings':
					self::WSC__Admin_Settings();
					break;
				case 'RemindMe':
					self::WSC__Admin_Dismiss();
					break;
				default:
					self::WSC__Admin_Daily();
			}
	
		}else{

			self::WSC__Admin_Daily();

		}
		
		// Tips
		if(isset($_GET[ 'Tutorial' ]) and preg_match('#.+#',$_GET[ 'Tutorial' ]) and $_GET[ 'Tutorial' ] != ''){
			WSC__Admin_Store_Closing_Tip($_GET[ 'Tutorial' ]);
		}
		
		?>
		<?php submit_button(); ?>
		</form>
		<div class="clear"></div>
		</div>
		<hr />
		<a href="http://www.woocommercestoreclosing.com" target="_blank"><p class='dashicons-before dashicons-admin-page'><?php echo __( 'More details and documentation page', WSC__DOMAIN ); ?></p></a><br />
	
	<?php
	}

	
	function WSC__Admin_Daily() {
		settings_fields ( WSC__SLUG.'_daily' );
		do_settings_sections ( WSC__SLUG.'_daily' );
	?>
	
<!-- Daily -->
	<div class="store-closing-boxes storeclosing_admin daily">	
		<div class="store-closing-box store-closing-col<?php echo $smart_pricing_col; echo ($StoreClosing->act != 'on') ? ' store-closing-disable':''; ?>" >
		
			<table class='storeclosing_admintable daily_table'>
			<tbody>
			<tr>
				<th width="90" align='center' valign='middle'></th>
				<th style="border-bottom:solid 2px #CCC" colspan="10" align='left' valign='middle' class='tabletitle'><h2><?php echo __('First', WSC__DOMAIN ); ?></h2></th>
			</tr>
			<tr>
				<th width="90" align='center' valign='middle'></th>
				<th width="22" align='center' valign='middle'></th>
				<th colspan='3' align='center' valign='middle'><?php echo __('Opening', WSC__DOMAIN ); ?></th>
				<th width="10" align='left' valign='middle'>&nbsp;</th>
				<th colspan='3' align='center' valign='middle'><?php echo __('Closing', WSC__DOMAIN ); ?></th>
			<?php if(self::$mobile_theme == false) { ?>
				<th width="10" align='center' valign='middle'>&nbsp;</th>
				<th align='left' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></th>
			<?php } ?>
			</tr>
		
			<?php 
			foreach($this->WSC__Options_Data['daily'] as $day_name=>$times){
				
				if ($day_name == 7) break;
				if (isset($this->WSC__Options_Data['daily'][$day_name][0]) && $this->WSC__Options_Data['daily'][$day_name][0] == true) {
					$day_active_first =  'checked="checked" ';
					$cell_active_first = '';
				} else {
					$day_active_first ='';
					$cell_active_first = 'disable'; 
				}
				?>
				<tr>
					<th <?php echo (self::$day_number == $day_name) ? 'style="border-left:solid 6px #9E6095;"':''; ?> class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='right' valign='middle'><?php echo $this->WSC__Options_Data['daysweek'][$day_name]; ?></th>
				  <th class="<?php echo $cell_active_first.' first_'.$day_name; ?>" valign='middle'>
					<label class="storeclosing-checkbox">
					<input id="first_<?php echo $day_name; ?>" type='checkbox' name='storeclosing_daily[<?php echo $day_name; ?>][0]' <?php echo $day_active_first; ?>>
					<sellectarea></sellectarea>
					</label>
				  </th>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="45" align='right'><input id="<?php echo 'first_opening_hour_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][1]' value='<?php echo ($times[1] == '')?'01':$times[1]; ?>' min='0' max='23' data-day="<?php echo $day_name; ?>" data-section="first"></td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="5" align='center'>:</td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="45" align='left'><input id="<?php echo 'first_opening_minute_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][2]' value='<?php echo ($times[2] == '')?'00':$times[2]; ?>' min='0' max='59' data-day="<?php echo $day_name; ?>" data-section="first"></td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="5" align='center'>&nbsp;</td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="45" align='right'><input id="<?php echo 'first_closing_hour_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][3]' value='<?php echo ($times[3] == '')?'01':$times[3]; ?>' min='0' max='23' data-day="<?php echo $day_name; ?>" data-section="first"></td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="5" align='center'>:</td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" width="45" align='left'><input id="<?php echo 'first_closing_minute_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][4]' value='<?php echo ($times[4] == '')?'00':$times[4]; ?>' min='0' max='59' data-day="<?php echo $day_name; ?>" data-section="first"></td>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='left'>&nbsp;</td>
					<?php if(self::$mobile_theme == false) { ?>
					<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='left'><input type='text' name='storeclosing_daily[<?php echo $day_name; ?>][5]' value='<?php echo $times[5]; ?>' style="text-align:left;"></td>
					<?php }else{ ?>
					</tr>
					<tr class="storeclosing_mobile_daily_message">
						<td colspan="2" align='right' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></td>
						<td colspan="7" class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='left'><input type='text' name='storeclosing_daily[<?php echo $day_name; ?>][5]' value='<?php echo $times[5]; ?>' style="text-align:left;"></td>
					<?php } ?> 
				</tr>
			<?php } ?> 
			</tr>
			</tbody></table>
			<table class='storeclosing_admintable daily_table'>
				<tbody>
				<tr>
					<th width="90" align='center' valign='middle'></th>
					<th style="border-bottom:solid 2px #CCC" colspan="10" align='left' valign='middle' class='tabletitle'><h2><?php echo __('Second', WSC__DOMAIN ); ?></h2></th>
				</tr>
				<tr>
					<th width="90" align='center' valign='middle'></th>
					<th width="22" align='center' valign='middle'></th>
					<th colspan='3' align='center' valign='middle' class='tabletitle'><?php echo __('Opening', WSC__DOMAIN ); ?></th>
					<th width="10" align='center' valign='middle'>&nbsp;</th>
					<th colspan='3' align='center' valign='middle' class='tabletitle'><?php echo __('Closing', WSC__DOMAIN ); ?></th>
					<?php if(self::$mobile_theme == false) { ?>
					<th width="10" align='center' valign='middle'>&nbsp;</th>
					<th align='left' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></th>
					<?php } ?>
				</tr>
				
				<?php 
				foreach($this->WSC__Options_Data['daily'] as $day_name=>$times){
					if ($day_name == 7) break;
					if (isset($this->WSC__Options_Data['daily'][$day_name][6]) && $this->WSC__Options_Data['daily'][$day_name][6] == true) {
						$day_active_second =  'checked="checked" ';
						$cell_active_second = '';
					} else {
						$day_active_second ='';
						$cell_active_second = 'disable'; 
					}
					?>
					<tr>
						<th <?php echo (self::$day_number == $day_name) ? 'style="border-left:solid 6px #9E6095;"':''; ?> class="<?php echo $cell_active_second.' second_'.$day_name; ?>" align='right' valign='middle'><?php echo $this->WSC__Options_Data['daysweek'][$day_name]; ?></th>
						<th class="<?php echo $cell_active_second.' second_'.$day_name; ?>" valign='middle'>
						<label class="storeclosing-checkbox">
						<input id="second_<?php echo $day_name; ?>" type='checkbox' name='storeclosing_daily[<?php echo $day_name; ?>][6]' <?php echo $day_active_second; ?>>
						<sellectarea></sellectarea>
						</label>
						</th>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="45" align='right'><input id="<?php echo 'second_opening_hour_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][7]' value='<?php echo ($times[7] == '')?'01':$times[7]; ?>' min='0' max='23' data-day="<?php echo $day_name; ?>" data-section="second"></td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="5" align='center'>:</td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="45" align='left'><input id="<?php echo 'second_opening_minute_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][8]' value='<?php echo ($times[8] == '')?'00':$times[8]; ?>' min='0' max='59' data-day="<?php echo $day_name; ?>" data-section="second"></td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="5" align='center'>&nbsp;</td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="45" align='right'><input id="<?php echo 'second_closing_hour_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][9]' value='<?php echo ($times[9] == '')?'01':$times[9]; ?>' min='0' max='23' data-day="<?php echo $day_name; ?>" data-section="second"></td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="5" align='center'>:</td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" width="45" align='left'><input id="<?php echo 'second_closing_minute_'.$day_name; ?>" type='number' name='storeclosing_daily[<?php echo $day_name; ?>][10]' value='<?php echo ($times[10] == '')?'00':$times[10]; ?>' min='0' max='59' data-day="<?php echo $day_name; ?>" data-section="second"></td>
						<td class="<?php echo $cell_active_second.' second_'.$day_name; ?>" align='left'>&nbsp;</td>
						<?php if(self::$mobile_theme == false) { ?>
						<td class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='left'><input type='text' name='storeclosing_daily[<?php echo $day_name; ?>][11]' value='<?php echo $times[11]; ?>' style="text-align:left;"></td>
						<?php }else{ ?>
						</tr>
						<tr class="storeclosing_mobile_daily_message">
							<td colspan="2" align='right' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></td>
							<td colspan="7" class="<?php echo $cell_active_first.' first_'.$day_name; ?>" align='left'><input type='text' name='storeclosing_daily[<?php echo $day_name; ?>][11]' value='<?php echo $times[11]; ?>' style="text-align:left;"></td>
						<?php } ?> 
					</tr>
				<?php } ?> 
				</tr>
				
				<tr>
					<td colspan="11" align="left"><br>
					<?php echo __('If active day message is empty', WSC__DOMAIN ); ?>, <a href="?page=storeclosing&tab=Notification"><?php echo __('General Notification Message', WSC__DOMAIN ); ?></a> <?php echo __(' will shows.', WSC__DOMAIN ); ?><h6>[tstamp] : <?php echo __('Active Hours', WSC__DOMAIN ); ?> | [countdown] : <?php echo __('Countdown', WSC__DOMAIN ); ?></h6>
					</td>
				</tr>
			</tbody></table>
			
		</div>
		<div class="clear">&nbsp;</div>		
	</div>
	
	<?php
	}
	
	function WSC__Admin_Upcoming() {
		settings_fields ( WSC__SLUG.'_upcoming' );
		do_settings_sections ( WSC__SLUG.'_upcoming' );
	?>
<!-- Upcoming -->
	<div class="store-closing-boxes">
	
		<?php
		$upcoming_plans = (is_array($this->WSC__Options_Data['upcoming'])) ? count($this->WSC__Options_Data['upcoming']) : 0;

		if($upcoming_plans > 0) {
			$this->WSC__Options_Data['upcoming'] = array_values($this->WSC__Options_Data['upcoming']);
			usort($this->WSC__Options_Data['upcoming'],function($a,$b){
	
				$c = strcmp($a[1],$b[1]);
				$c .= strcmp($a[2],$b[2]);
				$c .= strcmp($a[3],$b[3]);
	
				return $c;
	
			});
		}

		for ($upcoming_plan = 0; $upcoming_plan <= $upcoming_plans; $upcoming_plan++) {
		?>
	
		<div class="store-closing-box store-closing-settings">
		
		  <table id="<?php echo $upcoming_plan == $upcoming_plans ? "upcoming_plan_temp" : 'upcoming_plan_'.$upcoming_plan; ?>" cellpadding="0" cellspacing="0" class="storeclosing_admintable upcoming_table <?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][0]!=true)?'disable':''; ?>" data-id="<?php echo $upcoming_plan ?>">
			<tbody>
			<tr>
				<th colspan="9" align='left' valign='middle'>
				<label class="storeclosing-checkbox">
				<input id="<?php echo $upcoming_plan == $upcoming_plans ? "upcoming_temp" : 'upcoming_active_'.$upcoming_plan; ?>" name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][0]' <?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][0]) && $this->WSC__Options_Data['upcoming'][$upcoming_plan][0] == true )?'checked="checked" ':''; ?> type='checkbox' data-id= "<?php echo $upcoming_plan; ?>">
				<sellectarea></sellectarea>
				</label>
				&nbsp;&nbsp;&nbsp;
				<span id="titleinfo_<?php echo $upcoming_plan == $upcoming_plans ? "temp" : $upcoming_plan; ?>" class="upcoming_titleinfo" data-id= "<?php echo $upcoming_plan; ?>">
					<?php 
						echo ($upcoming_plan != $upcoming_plans) ? 
							date('d M Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]))
							.'&nbsp;'.
							$this->WSC__Options_Data['upcoming'][$upcoming_plan][2].':'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][3]
							.' - '.
							date('d M Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]))
							.'&nbsp;'.
							$this->WSC__Options_Data['upcoming'][$upcoming_plan][6].':'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][7]
							.' <span class="titleinfo_arrows">&nbsp;&nbsp;&nbsp;&darr;&uarr;</span>'
							:__('New Upcoming Plan', WSC__DOMAIN ); 
					?>
				</span>
				<div <?php echo $upcoming_plan == $upcoming_plans ? "style='display:none;'" : ''; ?> id="<?php echo $upcoming_plan; ?>" class="upcoming_delete dashicons dashicons-trash"></div>
			
			  </th>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th style="border-bottom:solid 2px #CCC" colspan="9" align='left' valign='middle'>
					<h2><?php echo __('Closing', WSC__DOMAIN ); ?></h2>
				</th>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th width="100" align='center' valign='middle' class='tabletitle'><?php echo __('Date', WSC__DOMAIN ); ?></th>
				<th width="5" align='center' valign='middle'>&nbsp;</th>
				<th width="45" align='center' valign='middle' class='tabletitle'><?php echo __('Hour', WSC__DOMAIN ); ?></th>
				<th width="5" align='left' valign='middle'>&nbsp;</th>
				<th width="45" align='center' valign='middle' class='tabletitle'><?php echo __('Minute', WSC__DOMAIN ); ?></th>
				<?php if(self::$mobile_theme == false) { ?>
				<th width="5" align='left' valign='middle'>&nbsp;</th>
				<th align='left' valign='middle' class='tabletitle'><?php echo __('Notification Message', WSC__DOMAIN ); ?></th>
				<th width="5" align='left' valign='middle'>&nbsp;</th>
				<th width="200" align='center' valign='middle' class='tabletitle'><div title="<?php echo __('When you want to close your store for a future date, you can give an earlier info for your customers. Select the day you want this message appear.', WSC__DOMAIN ); ?>" class="dashicons dashicons-info storeclosing_info"></div><?php echo __('Show Message', WSC__DOMAIN ); ?></th>
				<?php } ?>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][1]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][1]:date('Y-m-d', current_time( 'timestamp', 0 ) ); ?>"  type="date" id= "closing_date_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"/></td>
				<td align='center' valign='middle'>&nbsp;</td>
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][2]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][2]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][2]:'00'; ?>" type='number' min='0' max='23' id= "closing_hour_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"></td>
				<td align='center'>:</td>
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][3]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][3]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][3]:'00'; ?>" type='number' min='0' max='59' id= "closing_minute_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"></td>
				<?php if(self::$mobile_theme == false) { ?>
				<td align='center'>&nbsp;</td>
				<td align='left' valign='middle'><input type='text' name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][4]' value='<?php echo ($upcoming_plan != $upcoming_plans)?$this->WSC__Options_Data['upcoming'][$upcoming_plan][4]:'Our store will close order on [pcstamp]<br>[countdown]'; ?>' id= "closing_message" class="upcoming_inputs"></td>
				<td align='center'>&nbsp;</td>
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][9]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][9]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][9]:date('Y-m-d', current_time( 'timestamp', 0 ) ); ?>"  type="date" id= "show_date_<?php echo $upcoming_plan; ?>" class="upcoming_inputs show_date" data-id= "<?php echo $upcoming_plan; ?>" data-hour= "00" data-minute= "00"/>
				<div class="show_datearea">
					<input id="show_daily_message_<?php echo $upcoming_plan; ?>" name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][10]' type='hidden' value="<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][10] == 'hidden') ? "hidden" : 'visibility'; ?>" >
					<div title="<?php echo __('Daily Message to be displayed when this message is active ?', WSC__DOMAIN ); ?>" class="dashicons dashicons-<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][10] == 'hidden') ? "hidden" : 'visibility'; ?>" data-id= "<?php echo $upcoming_plan; ?>"></div>
				</div>
				</td>
				<?php }else{ ?>
				<tr>
					<td colspan="5" align='left' valign='middle'><input type='text' name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][4]' value='<?php echo ($upcoming_plan != $upcoming_plans)?$this->WSC__Options_Data['upcoming'][$upcoming_plan][4]:'Our store will close order on [pcstamp]<br>[countdown]'; ?>' id= "closing_message" class="upcoming_inputs"></td>
				</tr>
				<tr>
					<td align='left' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][9]' value="<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][9]!='')?$this->WSC__Options_Data['upcoming'][$upcoming_plan][9]:date('Y-m-d', current_time( 'timestamp', 0 ) ); ?>"  type="date" id= "show_date_<?php echo $upcoming_plan; ?>" class="upcoming_inputs show_date" data-id= "<?php echo $upcoming_plan; ?>" data-hour= "00" data-minute= "00"/>
					<div class="show_datearea">
						<input id="show_daily_message_<?php echo $upcoming_plan; ?>" name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][10]' type='hidden' value="<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][10] == 'hidden') ? "hidden" : 'visibility'; ?>" >
						<div title="<?php echo __('Daily Message to be displayed when this message is active ?', WSC__DOMAIN ); ?>" class="dashicons dashicons-<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][10] == 'hidden') ? "hidden" : 'visibility'; ?>" data-id= "<?php echo $upcoming_plan; ?>"></div>
					</div>
					</td>
					<td colspan="4">&nbsp;</td>
				<?php } ?>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th style="border-bottom:solid 2px #CCC" colspan="9" align='left' valign='middle'>
					<h2><?php echo __('Opening', WSC__DOMAIN ); ?></h2>
				</th>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th width="150" align='center' valign='middle' class='tabletitle'><?php echo __('Date', WSC__DOMAIN ); ?></th>
				<th width="5" align='center' valign='middle'>&nbsp;</th>
				<th width="50" align='center' valign='middle' class='tabletitle'><?php echo __('Hour', WSC__DOMAIN ); ?></th>
				<th width="5" align='left' valign='middle'>&nbsp;</th>
				<th width="50" align='center' valign='middle' class='tabletitle'><?php echo __('Minute', WSC__DOMAIN ); ?></th>
				<?php if(self::$mobile_theme == false) { ?>
				<th width="5" align='left' valign='middle'>&nbsp;</th>
				<th colspan="3" align='left' valign='middle' class='tabletitle'><?php echo __('Notification Message', WSC__DOMAIN ); ?></th>
				<?php } ?>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][5]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][5]:date('Y-m-d', current_time( 'timestamp', 0 ) ); ?>"  type="date" id= "opening_date_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"/></td>
				<td align='center' valign='middle'>&nbsp;</td>
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][6]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][6]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][6]:'00'; ?>" type='number' min='0' max='23' id= "opening_hour_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"></td>
				<td align='center'>:</td>
				<td align='right' valign='middle'><input name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][7]' value="<?php echo (isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][7]))?$this->WSC__Options_Data['upcoming'][$upcoming_plan][7]:'00'; ?>" type='number' min='0' max='59' id= "opening_minute_<?php echo $upcoming_plan; ?>" class="upcoming_inputs" data-id= "<?php echo $upcoming_plan; ?>"></td>
				<?php if(self::$mobile_theme == false) { ?>
				<td align='center'>&nbsp;</td>
				<td colspan="3" align='left' valign='middle'><input type='text' name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][8]' value='<?php echo ($upcoming_plan != $upcoming_plans)?$this->WSC__Options_Data['upcoming'][$upcoming_plan][8]:'Our store will be opened to order on [postamp]<br>[countdown]'; ?>' id= "opening_message" class="upcoming_inputs"></td>
				<?php }else{ ?>
				<tr>
					<td colspan="5" align='left' valign='middle'><input type='text' name='<?php echo $upcoming_plan == $upcoming_plans ? "temp_" : ''; ?>storeclosing_upcoming[<?php echo $upcoming_plan; ?>][8]' value='<?php echo ($upcoming_plan != $upcoming_plans)?$this->WSC__Options_Data['upcoming'][$upcoming_plan][8]:'Our store will be opened to order on [postamp]<br>[countdown]'; ?>' id= "opening_message" class="upcoming_inputs"></td>
				<?php } ?>
			</tr>
			<?php if(self::$mobile_theme == false) { ?>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th colspan="9" style="border-bottom:solid 2px #CCC" align='left' valign='middle'>
					<h2><?php echo __('Business Plan', WSC__DOMAIN ); ?></h2>
				</th>
			</tr>
			<tr class="tr_upcoming_wide_<?php echo $upcoming_plan ?>">
				<th colspan="9" align='center' valign='middle'>
					<div id="business_plan_<?php echo $upcoming_plan; ?>" class="business_plan">
				
						<div id="storeclosing_daystart" class="upcoming_preview" data-wpnow_date="<?php echo date( 'Y-m-d', current_time( 'timestamp', 0 ) ); ?>" data-wpnow_hour="<?php echo date( 'H', current_time( 'timestamp', 0 ) ); ?>" data-wpnow_minute="<?php echo date( 'i', current_time( 'timestamp', 0 ) ); ?>"><div class="title"><span class="dashicons dashicons-clock"></span> <?php echo date( 'Y-m-d H:i', current_time( 'timestamp', 0 ) ); ?></div></div>
						<div id="storeclosing_storeopened" class="upcoming_preview" ><div class="title"><span class="dashicons dashicons-unlock"></span> <?php echo __('Open until', WSC__DOMAIN ); ?> : <span class="date_stamp"><?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]!='')? date('Y-m-d', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]) ) : date('Y-m-d', current_time( 'timestamp', 0 ) ); ?> <?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][2]!='')? $this->WSC__Options_Data['upcoming'][$upcoming_plan][2] : '00'; ?>:<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][3]!='')? $this->WSC__Options_Data['upcoming'][$upcoming_plan][3] : '00'; ?></span></div></div>
						<div id="storeclosing_storeclosed" class="upcoming_preview" ><div class="title"><span class="dashicons dashicons-lock"></span> <?php echo __('Close until', WSC__DOMAIN ); ?> : <span class="date_stamp"><?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]!='')? date('Y-m-d', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]) ) : date('Y-m-d', current_time( 'timestamp', 0 ) ); ?> <?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][6]!='')? $this->WSC__Options_Data['upcoming'][$upcoming_plan][6] : '00'; ?>:<?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][7]!='')? $this->WSC__Options_Data['upcoming'][$upcoming_plan][7] : '00'; ?></span></div></div>
					</div>
				</th>
			</tr>
			<?php } ?>
			</tbody></table>
			</div>
		<?php } ?>        
			<table class="storeclosing_admintable <?php echo ($this->WSC__Options_Data['upcoming'][$upcoming_plan][0]!=true)?'disable':''; ?>" width="90%">
			<tbody>
			<tr>
				<td >
					<h6>[pcstamp] : <?php echo __('Closing Time', WSC__DOMAIN ); ?> [postamp] : <?php echo __('Opening Time', WSC__DOMAIN ); ?> | [countdown] : <?php echo __('Countdown', WSC__DOMAIN ); ?></h6>
				</td>
			</tr>
		  </tbody></table> 
		
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
	
	function WSC__Admin_Manual() {
		settings_fields ( WSC__SLUG.'_manual' );
		do_settings_sections ( WSC__SLUG.'_manual' );
	?>
<!-- Manual -->
	<div class="store-closing-boxes">
		<div class="store-closing-box store-closing-settings">
			<table class='storeclosing_admintable storeclosing_manuel'>
				<tbody>
				<tr>
					<td align="center">
					<?php echo __('This option close your store and shows general notification message.', WSC__DOMAIN ).'<br>('.__('Without timestamp and countdown', WSC__DOMAIN ).')'; ?>
					<br><a href="?page=storeclosing&tab=Notification"><?php echo __('Click for general message', WSC__DOMAIN ); ?></a>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td align='center' valign='middle'>
					<label class="storeclosing-manuel-checkbox <?php echo ($this->WSC__Options_Data['manual']==true)?'disable" ':''; ?>">
					<input id="storeclosing_manuel" <?php echo ($this->WSC__Options_Data['manual']==true)?'checked="checked" ':''; ?> type="checkbox" name="storeclosing_manual">
					<sellectarea></sellectarea><br /><span id="storeclosing_manuel_message" ><?php echo ($this->WSC__Options_Data['manual']==true) ? __('STORE is CLOSE', WSC__DOMAIN ) : __('STORE is OPEN', WSC__DOMAIN ); ?> </span><span id="storeclosing_manuel_loading" style="display:none;"><img width="50px" src="<?php echo esc_url( plugins_url( 'js/loading.gif', __FILE__ ) ); ?>" /></span></label>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
	
	function WSC__Admin_Popup() {
		settings_fields ( WSC__SLUG.'_popup' );
		do_settings_sections ( WSC__SLUG.'_popup' );
	?>
<!-- Popup -->
	<div class="store-closing-boxes">
		<div class="store-closing-box store-closing-settings">
			<table class='storeclosing_admintable popup_table <?php echo (!isset($this->WSC__Options_Data['popup'][0])) ? 'disable':''; ?>'>
				<tbody>
				<tr valign="middle">
				  <td align='left' nowrap="nowrap" class="tabletitle" style="border-bottom:solid 2px #CCC"><label class='storeclosing-checkbox'><input id="popup_act" type='checkbox' name='storeclosing_popup[0]' <?php echo (isset($this->WSC__Options_Data['popup'][0]) && $this->WSC__Options_Data['popup'][0] == true)?'checked="checked" ':''; ?>><sellectarea></sellectarea></label><span id="storeclosing_popup_loading" style="display:none;">&nbsp;&nbsp;&nbsp;<img width="15px" src="<?php echo esc_url( plugins_url( 'js/loading.gif', __FILE__ ) ); ?>" /></span>
				  </td>
				  <th align='left' class="tabletitle" style="border-bottom:solid 2px #CCC">&nbsp;</th>
					<?php if(self::$mobile_theme == false) { ?>
				  <th align="center" class="tabletitle" style="border-bottom:solid 2px #CCC"><h2><?php echo __('Popup Preview', WSC__DOMAIN ); ?></h2></th>
					<?php } ?>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Show in Area', WSC__DOMAIN ); ?></td>
					<td width="150" valign='middle'>
						<select id="popup_position" name='storeclosing_popup[1]'>
							<option <?php echo ($this->WSC__Options_Data['popup'][1] == 'top' ) ? "selected='selected'":''; ?> value="top"><?php echo __( 'Top', WSC__DOMAIN ); ?></option>
							<option <?php echo ($this->WSC__Options_Data['popup'][1] == 'bottom' ) ? "selected='selected'":''; ?> value="bottom"><?php echo __( 'Bottom', WSC__DOMAIN ); ?></option>  
						</select>
					</td>
					<?php if(self::$mobile_theme == false) { ?>
					<td rowspan="11" align='center' valign='middle'>
				
						<div id="storeclosing_popup_setting">
							<div id="popup_message" style=" <?php echo (!isset($this->WSC__Options_Data['popup'][0])) ? 'display:none;':''; ?> <?php echo ($this->WSC__Options_Data['popup'][1] == 'top' ) ? 'top:0;':'bottom:0;'; ?> <?php echo ($this->WSC__Options_Data['popup'][5]!='') ? 'background-color:'.$this->WSC__Options_Data['popup'][5].';':''; ?> <?php echo ($this->WSC__Options_Data['popup'][6] != '' && $this->WSC__Options_Data['popup'][6] != 100) ? "opacity: 0.".$this->WSC__Options_Data['popup'][6]."; filter: alpha(opacity=".$this->WSC__Options_Data['popup'][6]."); " : "opacity: 1; filter: alpha(opacity=100); " ?>">
							<?php 
								$WSC__Active_Plan = parent::WSC__Active_Plan();
								
								if($WSC__Active_Plan['store_check'] == 'CLOSE') {
									echo $WSC__Active_Plan['storeclosing_preview'];
								} else {
									echo "<h2>".__('Store is currently open', WSC__DOMAIN )."</h2>";
								}
							?>
							</div>
						</div>
				 
				  </td>
					<?php } ?>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Css Class', WSC__DOMAIN ); ?></td>
					<td width="150" valign='middle'><input type='text' name='storeclosing_popup[2]' value='<?php echo ($this->WSC__Options_Data['popup'][2]) ? $this->WSC__Options_Data['popup'][2] : ''; ?>' ></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Close Button', WSC__DOMAIN ); ?></td>
					<td width="150" valign='middle'>
						<select name='storeclosing_popup[3]'>
							<option <?php echo ($this->WSC__Options_Data['popup'][3] == 'yes' ) ? "selected='selected'":''; ?> value="yes"><?php echo __( 'Yes', WSC__DOMAIN ); ?></option>
							<option <?php echo ($this->WSC__Options_Data['popup'][3] == 'no' ) ? "selected='selected'":''; ?> value="no"><?php echo __( 'No', WSC__DOMAIN ); ?></option> 
						</select>
					</td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Closing Time (sec)', WSC__DOMAIN ); ?></td>
					<td width="150" align="left" valign='middle'><input id="popup_closingtime" class="store-closing-label" type="number" name="storeclosing_popup[4]" min="0" max="59" value='<?php echo ($this->WSC__Options_Data['popup'][4]) ? $this->WSC__Options_Data['popup'][4] : 0; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Background Color', WSC__DOMAIN ); ?></td>
					<td width="150"  align="left" valign='middle'><input id="popup_background" class="store-closing-label" type="text" name="storeclosing_popup[5]" value='<?php echo ($this->WSC__Options_Data['popup'][5]) ? $this->WSC__Options_Data['popup'][5] : ''; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Transparent (%)', WSC__DOMAIN ); ?></td>
					<td width="150"  align="left" valign='middle'><input id="popup_transparent" class="store-closing-label" type="number" name="storeclosing_popup[6]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['popup'][6]) ? $this->WSC__Options_Data['popup'][6] : 100; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap" bgcolor="#9E6095" style="color:#ffffff;"><?php echo __( 'Cookie Expires', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'>
				  	<select id='popup_cookie' name='storeclosing_popup[12]'>
						<option <?php echo ($this->WSC__Options_Data['popup'][12] == 'active' || $this->WSC__Options_Data['popup'][12] != 'pasive' ) ? "selected='selected'":''; ?> value="active"><?php echo __( 'Active', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['popup'][12] == 'pasive' ) ? "selected='selected'":''; ?> value="pasive"><?php echo __( 'Pasive', WSC__DOMAIN ); ?></option> 
					</select>
				  </td>
				  </tr>
				<tr align='center' valign='middle' class='popup_cookie <?php echo ($this->WSC__Options_Data['popup'][12] == 'pasive' )?'disable':''; ?>'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Days', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'><input class="store-closing-label" type="number" name="storeclosing_popup[7]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['popup'][7]) ? $this->WSC__Options_Data['popup'][7] : 0; ?>' /></td>
				  </tr>
				<tr align='center' valign='middle' class='popup_cookie <?php echo ($this->WSC__Options_Data['popup'][12] == 'pasive' )?'disable':''; ?>'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Hours', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'><input class="store-closing-label" type="number" name="storeclosing_popup[8]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['popup'][8]) ? $this->WSC__Options_Data['popup'][8] : 0; ?>' /></td>
				  </tr>
				<tr align='center' valign='middle' class='popup_cookie <?php echo ($this->WSC__Options_Data['popup'][12] == 'pasive' )?'disable':''; ?>'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Minutes', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'><input class="store-closing-label" type="number" name="storeclosing_popup[9]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['popup'][9]) ? $this->WSC__Options_Data['popup'][9] : 0; ?>' /></td>
				  </tr>
				<tr align='center' valign='middle' class='popup_cookie <?php echo ($this->WSC__Options_Data['popup'][12] == 'pasive' )?'disable':''; ?>'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Seconds', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'><input class="store-closing-label" type="number" name="storeclosing_popup[10]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['popup'][10]) ? $this->WSC__Options_Data['popup'][10] : 0; ?>' /></td>
				  </tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='top' nowrap="nowrap"><?php echo __( 'Exclude Page', WSC__DOMAIN ); ?> <div class="dashicons dashicons-warning" title="<?php echo __( 'The popup message will not show at the selected pages. If you want select page, click on the page name.', WSC__DOMAIN ); ?>"></div></td>
				  <td colspan="2" align='left' valign='middle'>
				  	<input id="storeclosing_exludepage" class="store-closing-label" type="hidden" name="storeclosing_popup[11]" value="<?php echo ($this->WSC__Options_Data['popup'][11]) ? $this->WSC__Options_Data['popup'][11] : ''; ?>" />
				  	<?php
						$exclude_pages = explode( ',', $this->WSC__Options_Data['popup'][11] );
						$pages = get_pages(); 
						foreach ( $pages as $page ) {
							if(in_array($page->ID, $exclude_pages)){
								echo "<div id='".$page->ID."' class='storeclosing_exludepage_button storeclosing_exludepage_selected'>".$page->post_title."</div>";
							}else{
								echo "<div id='".$page->ID."' class='storeclosing_exludepage_button storeclosing_exludepage'>".$page->post_title."</div>";
							}						
						}
				  	?>
				  </td>
				  </tr>
			  </tbody>
			</table>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
	
	function WSC__Admin_Exclude() {
		settings_fields ( WSC__SLUG.'_exclude' );
		do_settings_sections ( WSC__SLUG.'_exclude' );
	?>
<!-- Exclude -->
	<div class="store-closing-boxes">
		<div class="store-closing-box store-closing-settings">
	
			<table class='storeclosing_admintable exclude_table <?php echo (!isset($this->WSC__Options_Data['exclude'][0])) ? 'disable':''; ?>'>
				<tbody>
				<tr valign="middle">
				  <td align='left' nowrap="nowrap" class="tabletitle" style="border-bottom:solid 2px #CCC"><label class='storeclosing-checkbox'><input id="exclude_act" type='checkbox' name='storeclosing_exclude[0]' <?php echo (isset($this->WSC__Options_Data['exclude'][0]) && $this->WSC__Options_Data['exclude'][0] == true)?'checked="checked" ':''; ?>><sellectarea></sellectarea></label><span id="storeclosing_exclude_loading" style="display:none;">&nbsp;&nbsp;&nbsp;<img width="15px" src="<?php echo esc_url( plugins_url( 'js/loading.gif', __FILE__ ) ); ?>" /></span>
				  </td>
				  <td colspan="2" align='left' valign='middle' style="border-bottom:solid 2px #CCC">
				  	<input style="width:45px;" type='number' name='storeclosing_exclude[6]' value='<?php echo ($this->WSC__Options_Data['exclude'][6] == '')?'00':$this->WSC__Options_Data['exclude'][6]; ?>' min='0' max='23' data-day="<?php echo $day_name; ?>" min='0' max='23'>
				  	:
				  	<input style="width:45px;" type='number' name='storeclosing_exclude[7]' value='<?php echo ($this->WSC__Options_Data['exclude'][7] == '')?'00':$this->WSC__Options_Data['exclude'][7]; ?>' min='0' max='59'>
					-
				  	<input style="width:45px;" type='number' name='storeclosing_exclude[8]' value='<?php echo ($this->WSC__Options_Data['exclude'][8] == '')?'00':$this->WSC__Options_Data['exclude'][8]; ?>' min='0' max='23'>
				  	:
				  	<input style="width:45px;" type='number' name='storeclosing_exclude[9]' value='<?php echo ($this->WSC__Options_Data['exclude'][9] == '')?'00':$this->WSC__Options_Data['exclude'][9]; ?>' min='0' max='59'>
				  	<div class="dashicons dashicons-warning" title="<?php echo __( 'You can set start and end times for exclude process. If you set 00 process works without time limitation.', WSC__DOMAIN ); ?>"></div>
				  </td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Exclude Category', WSC__DOMAIN ); ?></td>
					<td width="150" valign='middle'>
						<select name="storeclosing_exclude[1]">
								<option value=""><?php echo __( 'Select Category', WSC__DOMAIN ); ?></option>
								<?php
								$args = array( 'taxonomy' => 'product_cat' );
								$product_cats = get_terms('product_cat', $args);
								print_r($product_cats);
									if (count($product_cats) > 0) {
										foreach ($product_cats as $product_cat) { ?>
											<option <?php echo ($this->WSC__Options_Data['exclude'][1]==$product_cat->term_id)?'selected="selected" ':''; ?> value='<?php echo $product_cat->term_id; ?>'><?php echo $product_cat->name; ?></option>
										<?php }
									}
								?>                            
							</select>
					</td>
					<?php if(self::$mobile_theme == false) { ?>
					<td rowspan="4" align='center' valign='middle'>
						<div style='width:90%;'>
						<div id="storeclosing_exclude_setting">
							<div id="exclude_message" class="<?php echo $this->WSC__Options_Data['exclude'][2]; ?>" style=" <?php echo (!isset($this->WSC__Options_Data['exclude'][0]) || $this->WSC__Options_Data['exclude'][5] == '') ? 'display:none;':''; ?> <?php echo ($this->WSC__Options_Data['exclude'][3]!='') ? 'background-color:'.$this->WSC__Options_Data['exclude'][3].';':''; ?> <?php echo ($this->WSC__Options_Data['exclude'][4] != '' && $this->WSC__Options_Data['exclude'][4] != 100) ? "opacity: 0.".$this->WSC__Options_Data['exclude'][4]."; filter: alpha(opacity=".$this->WSC__Options_Data['exclude'][4]."); " : "opacity: 1; filter: alpha(opacity=100); " ?>">
							<?php $exclude_message = str_replace( '[plist]', '<br> * '.__( 'Product Name', WSC__DOMAIN ).'<br>* ...' , $this->WSC__Options_Data['exclude'][5] ); ?>
							<?php echo $exclude_message; ?>
							</div>
						</div>
						</div>
				  </td>
					<?php } ?>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Css Class', WSC__DOMAIN ); ?></td>
				  <td width="150" valign='middle'><input type='text' name='storeclosing_exclude[2]' value='<?php echo ($this->WSC__Options_Data['exclude'][2]) ? $this->WSC__Options_Data['exclude'][2] : ''; ?>' ></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Background Color', WSC__DOMAIN ); ?></td>
				  <td width="150"  align="left" valign='middle'><input id="exclude_background" class="store-closing-label" type="text" name="storeclosing_exclude[3]" value='<?php echo ($this->WSC__Options_Data['exclude'][3]) ? $this->WSC__Options_Data['exclude'][3] : ''; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Transparent (%)', WSC__DOMAIN ); ?></td>
				  <td width="150"  align="left" valign='middle'><input id="exclude_transparent" class="store-closing-label" type="number" name="storeclosing_exclude[4]" min="0" max="100" value='<?php echo ($this->WSC__Options_Data['exclude'][4]) ? $this->WSC__Options_Data['exclude'][4] : 100; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td width="150" align='left' valign='middle' nowrap="nowrap"><?php echo __( 'Message', WSC__DOMAIN ); ?></td>
				  <td colspan="2" align="left" valign='middle'><input id="exclude_message" class="store-closing-label" type="text" name="storeclosing_exclude[5]" value='<?php echo $this->WSC__Options_Data['exclude'][5]; ?>'></td>
				</tr>
				<tr align='center' valign='middle'>
				  <td colspan="3" align="left" valign='middle'><h6>[plist] : <?php echo __('Unauthorized Product(s) List', WSC__DOMAIN ); ?> ( <?php echo __('Blank', WSC__DOMAIN ); ?> : <?php echo __('Invisible', WSC__DOMAIN ); ?> )</h6></td>
				</tr>
			  </tbody>
			</table>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
	
	function WSC__Admin_Notification() {
		settings_fields ( WSC__SLUG.'_notification' );
		do_settings_sections ( WSC__SLUG.'_notification' );
	?>
	
<!-- Notification -->
	<div class="store-closing-boxes storeclosing_admin daily">	
		<div class="store-closing-box store-closing-col<?php echo $smart_pricing_col; echo ($StoreClosing->act != 'on') ? ' store-closing-disable':''; ?>" >

			<table class='storeclosing_admintable daily_table'>
			<tr align='center' valign='middle'>
				<th valign='middle' class='tabletitle'><h2><?php echo __('General Notification Message', WSC__DOMAIN ); ?></h2></th>
			</tr>
			<tr>
				<td valign='middle'><input id="storeclosing_general_message" type='text' name="storeclosing_notification[0]" value='<?php echo $this->WSC__Options_Data['notification'][0]; ?>'><br /><h6>[tstamp] : <?php echo __('Active Hours', WSC__DOMAIN ); ?> | [countdown] : <?php echo __('Countdown', WSC__DOMAIN ); ?></h6></td>
			</tr>
			</tbody></table>
		
			<table class='storeclosing_admintable daily_table'>
			<tbody>
			<tr>
				<th width="90" align='center' valign='middle'></th>
				<th style="border-bottom:solid 2px #CCC" colspan="10" align='left' valign='middle' class='tabletitle'><h2><?php echo __('Dayparts Notification Message', WSC__DOMAIN ); ?></h2></th>
			</tr>
			<tr>
				<th width="90" align='center' valign='middle'></th>
				<th width="22" align='center' valign='middle'></th>
				<th colspan='3' align='center' valign='middle'><?php echo __('Starting', WSC__DOMAIN ); ?></th>
				<th width="10" align='left' valign='middle'>&nbsp;</th>
				<th colspan='3' align='center' valign='middle'><?php echo __('Ending', WSC__DOMAIN ); ?></th>
			<?php if(self::$mobile_theme == false) { ?>
				<th width="10" align='center' valign='middle'>&nbsp;</th>
				<th align='left' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></th>
			<?php } ?>
			</tr>
		
			<tr class="notification_morning <?php echo (isset($this->WSC__Options_Data['notification'][1]) && $this->WSC__Options_Data['notification'][1] == true)?'':'disable'; ?>">
				<th class="" align='right' valign='middle'><?php echo __('Morning', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox">
					<input id="notification_morning" type='checkbox' name='storeclosing_notification[1]' <?php echo (isset($this->WSC__Options_Data['notification'][1]) && $this->WSC__Options_Data['notification'][1] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[2]' value='<?php echo $this->WSC__Options_Data['notification'][2]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[3]' value='<?php echo $this->WSC__Options_Data['notification'][3]; ?>' min='0' max='59'></td>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[4]' value='<?php echo $this->WSC__Options_Data['notification'][4]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[5]' value='<?php echo $this->WSC__Options_Data['notification'][5]; ?>' min='0' max='59'></td>
				<td class="" align='left'>&nbsp;</td>
				<?php if(self::$mobile_theme == false) { ?>
					<td class="" align='left'><input type='text' name='storeclosing_notification[6]' value='<?php echo $this->WSC__Options_Data['notification'][6]; ?>' style="text-align:left;"></td>
				<?php }else{ ?>
				</tr>
					<tr class="storeclosing_mobile_daily_message">
						<td colspan="2" align='right' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></td>
						<td colspan="7" class="" align='left'><input type='text' name='storeclosing_notification[6]' value='<?php echo $this->WSC__Options_Data['notification'][6]; ?>' style="text-align:left;"></td>
				<?php } ?> 
			</tr>
			
			<tr class="notification_noon <?php echo (isset($this->WSC__Options_Data['notification'][7]) && $this->WSC__Options_Data['notification'][7] == true)?'':'disable'; ?>">
				<th class="" align='right' valign='middle'><?php echo __('Noon', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox">
					<input id="notification_noon" type='checkbox' name='storeclosing_notification[7]' <?php echo (isset($this->WSC__Options_Data['notification'][7]) && $this->WSC__Options_Data['notification'][7] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[8]' value='<?php echo $this->WSC__Options_Data['notification'][8]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[9]' value='<?php echo $this->WSC__Options_Data['notification'][9]; ?>' min='0' max='59'></td>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[10]' value='<?php echo $this->WSC__Options_Data['notification'][10]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[11]' value='<?php echo $this->WSC__Options_Data['notification'][11]; ?>' min='0' max='59'></td>
				<td class="" align='left'>&nbsp;</td>
				<?php if(self::$mobile_theme == false) { ?>
					<td class="" align='left'><input type='text' name='storeclosing_notification[12]' value='<?php echo $this->WSC__Options_Data['notification'][12]; ?>' style="text-align:left;"></td>
				<?php }else{ ?>
				</tr>
					<tr class="storeclosing_mobile_daily_message">
						<td colspan="2" align='right' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></td>
						<td colspan="7" class="" align='left'><input type='text' name='storeclosing_notification[12]' value='<?php echo $this->WSC__Options_Data['notification'][12]; ?>' style="text-align:left;"></td>
				<?php } ?> 
			</tr>
			
			<tr class="notification_afternoon <?php echo (isset($this->WSC__Options_Data['notification'][13]) && $this->WSC__Options_Data['notification'][13] == true)?'':'disable'; ?>">
				<th class="" align='right' valign='middle'><?php echo __('Evening', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox">
					<input id="notification_afternoon" type='checkbox' name='storeclosing_notification[13]' <?php echo (isset($this->WSC__Options_Data['notification'][13]) && $this->WSC__Options_Data['notification'][13] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[14]' value='<?php echo $this->WSC__Options_Data['notification'][14]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[15]' value='<?php echo $this->WSC__Options_Data['notification'][15]; ?>' min='0' max='59'></td>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td class="" width="45" align='right'><input id="" type='number' name='storeclosing_notification[16]' value='<?php echo $this->WSC__Options_Data['notification'][16]; ?>' min='0' max='23'></td>
				<td class="" width="5" align='center'>:</td>
				<td class="" width="45" align='left'><input id="" type='number' name='storeclosing_notification[17]' value='<?php echo $this->WSC__Options_Data['notification'][17]; ?>' min='0' max='59'></td>
				<td class="" align='left'>&nbsp;</td>
				<?php if(self::$mobile_theme == false) { ?>
					<td class="" align='left'><input type='text' name='storeclosing_notification[18]' value='<?php echo $this->WSC__Options_Data['notification'][18]; ?>' style="text-align:left;"></td>
				<?php }else{ ?>
				</tr>
					<tr class="storeclosing_mobile_daily_message">
						<td colspan="2" align='right' valign='middle'><?php echo __('Message', WSC__DOMAIN ); ?></td>
						<td colspan="7" class="" align='left'><input type='text' name='storeclosing_notification[18]' value='<?php echo $this->WSC__Options_Data['notification'][18]; ?>' style="text-align:left;"></td>
				<?php } ?> 
			</tr>
			
			<tr>
				<td colspan='10' valign='middle'><h6>[gm] : <?php echo __('General Notification Message', WSC__DOMAIN ); ?></h6></td>
			</tr>
			
			<tr class="notification_product <?php echo (isset($this->WSC__Options_Data['notification'][19]) && $this->WSC__Options_Data['notification'][19] == true)?'':'disable'; ?>">
				<th colspan='4' class="" align='right' valign='middle'><?php echo __('Product Page Notification', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox" data-infoimg="<?php echo esc_url( plugins_url( 'img/storeclosing_message_product.jpg', __FILE__ ) ); ?>">
					<input id="notification_product" type='checkbox' name='storeclosing_notification[19]' <?php echo (isset($this->WSC__Options_Data['notification'][19]) && $this->WSC__Options_Data['notification'][19] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td colspan='5' align='right'>
					<select name='storeclosing_notification[20]'>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_single_product') ? "selected='selected'":''; ?> value='woocommerce_before_single_product'><?php echo __('1 - woocommerce_before_single_product', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_single_product_summary') ? "selected='selected'":''; ?> value='woocommerce_before_single_product_summary'><?php echo __('2 - woocommerce_before_single_product_summary', WSC__DOMAIN ); ?></option>					
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_single_product_summary') ? "selected='selected'":''; ?> value='woocommerce_single_product_summary'><?php echo __('3 - woocommerce_single_product_summary', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_add_to_cart_form') ? "selected='selected'":''; ?> value='woocommerce_before_add_to_cart_form'><?php echo __('4 - woocommerce_before_add_to_cart_form', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_variations_form') ? "selected='selected'":''; ?> value='woocommerce_before_variations_form'><?php echo __('5 - woocommerce_before_variations_form', WSC__DOMAIN ); ?></option>
						<option <?php echo ((isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_add_to_cart_button') || !isset($this->WSC__Options_Data['notification'][20])) ? "selected='selected'":''; ?> value='woocommerce_before_add_to_cart_button'><?php echo __('6 - woocommerce_before_add_to_cart_button', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_before_single_variation') ? "selected='selected'":''; ?> value='woocommerce_before_single_variation'><?php echo __('7 - woocommerce_before_single_variation', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_single_variation') ? "selected='selected'":''; ?> value='woocommerce_single_variation'><?php echo __('8 - woocommerce_single_variation', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_single_variation') ? "selected='selected'":''; ?> value='woocommerce_after_single_variation'><?php echo __('9 - woocommerce_after_single_variation', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_add_to_cart_button') ? "selected='selected'":''; ?> value='woocommerce_after_add_to_cart_button'><?php echo __('10 - woocommerce_after_add_to_cart_button', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_variations_form') ? "selected='selected'":''; ?> value='woocommerce_after_variations_form'><?php echo __('11 - woocommerce_after_variations_form', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_add_to_cart_form') ? "selected='selected'":''; ?> value='woocommerce_after_add_to_cart_form'><?php echo __('12 - woocommerce_after_add_to_cart_form', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_product_meta_start') ? "selected='selected'":''; ?> value='woocommerce_product_meta_start'><?php echo __('13 - woocommerce_product_meta_start', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_product_meta_end') ? "selected='selected'":''; ?> value='woocommerce_product_meta_end'><?php echo __('14 - woocommerce_product_meta_end', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_single_product_summary') ? "selected='selected'":''; ?> value='woocommerce_after_single_product_summary'><?php echo __('15 - woocommerce_after_single_product_summary', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] == 'woocommerce_after_single_product') ? "selected='selected'":''; ?> value='woocommerce_after_single_product'><?php echo __('16 - woocommerce_after_single_product', WSC__DOMAIN ); ?></option>
					</select>
				</td>
			</tr>
			<tr class="notification_shop <?php echo (isset($this->WSC__Options_Data['notification'][21]) && $this->WSC__Options_Data['notification'][21] == true)?'':'disable'; ?>">
				<th colspan='4' class="" align='right' valign='middle'><?php echo __('Shop Page Notification', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox" data-infoimg="<?php echo esc_url( plugins_url( 'img/storeclosing_message_shop.jpg', __FILE__ ) ); ?>">
					<input id="notification_shop" type='checkbox' name='storeclosing_notification[21]' <?php echo (isset($this->WSC__Options_Data['notification'][21]) && $this->WSC__Options_Data['notification'][21] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td colspan='5' align='right'>
					<select name='storeclosing_notification[22]'>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][22]) && $this->WSC__Options_Data['notification'][22] == 'woocommerce_before_shop_loop') ? "selected='selected'":''; ?> value='woocommerce_before_shop_loop'><?php echo __('1 - woocommerce_before_shop_loop', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][22]) && $this->WSC__Options_Data['notification'][22] == 'woocommerce_after_shop_loop') ? "selected='selected'":''; ?> value='woocommerce_after_shop_loop'><?php echo __('2 - woocommerce_after_shop_loop', WSC__DOMAIN ); ?></option>
					</select>
				</td>
			</tr>
			<tr class="notification_cart <?php echo (isset($this->WSC__Options_Data['notification'][23]) && $this->WSC__Options_Data['notification'][23] == true)?'':'disable'; ?>">
				<th colspan='4' class="" align='right' valign='middle'><?php echo __('Cart Page Notification', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox" data-infoimg="<?php echo esc_url( plugins_url( 'img/storeclosing_message_cart.jpg', __FILE__ ) ); ?>">
					<input id="notification_cart" type='checkbox' name='storeclosing_notification[23]' <?php echo (isset($this->WSC__Options_Data['notification'][23]) && $this->WSC__Options_Data['notification'][23] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td colspan='5' align='right'>
					<select name='storeclosing_notification[24]'>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] == 'woocommerce_before_cart') ? "selected='selected'":''; ?> value='woocommerce_before_cart'><?php echo __('1 - woocommerce_before_cart', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] == 'woocommerce_after_cart_table') ? "selected='selected'":''; ?> value='woocommerce_after_cart_table'><?php echo __('2 - woocommerce_after_cart_table', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] == 'woocommerce_before_cart_totals') ? "selected='selected'":''; ?> value='woocommerce_before_cart_totals'><?php echo __('3 - woocommerce_before_cart_totals', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] == 'woocommerce_after_cart_totals') ? "selected='selected'":''; ?> value='woocommerce_after_cart_totals'><?php echo __('4 - woocommerce_after_cart_totals', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] == 'woocommerce_after_cart') ? "selected='selected'":''; ?> value='woocommerce_after_cart'><?php echo __('5 - woocommerce_after_cart', WSC__DOMAIN ); ?></option>
					</select>
				</td>
			</tr>
			<tr class="notification_payment <?php echo (isset($this->WSC__Options_Data['notification'][25]) && $this->WSC__Options_Data['notification'][25] == true)?'':'disable'; ?>">
				<th colspan='4' class="" align='right' valign='middle'><?php echo __('Payment Page Notification', WSC__DOMAIN ); ?></th>
				<th class="" valign='middle'>
					<label class="storeclosing-checkbox" data-infoimg="<?php echo esc_url( plugins_url( 'img/storeclosing_message_payment.jpg', __FILE__ ) ); ?>">
					<input id="notification_payment" type='checkbox' name='storeclosing_notification[25]' <?php echo (isset($this->WSC__Options_Data['notification'][25]) && $this->WSC__Options_Data['notification'][25] == true)?'checked="checked" ':''; ?>>
					<sellectarea></sellectarea>
					</label>
				</th>
				<td class="" width="5" align='center'>&nbsp;</td>
				<td colspan='5' align='right'>
					<select name='storeclosing_notification[26]'>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] == 'woocommerce_checkout_before_customer_details') ? "selected='selected'":''; ?> value='woocommerce_checkout_before_customer_details'><?php echo __('1 - woocommerce_checkout_before_customer_details', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] == 'woocommerce_checkout_after_customer_details') ? "selected='selected'":''; ?> value='woocommerce_checkout_after_customer_details'><?php echo __('2 - woocommerce_checkout_after_customer_details', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] == 'woocommerce_checkout_before_order_review') ? "selected='selected'":''; ?> value='woocommerce_checkout_before_order_review'><?php echo __('3 - woocommerce_checkout_before_order_review', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] == 'woocommerce_review_order_before_payment') ? "selected='selected'":''; ?> value='woocommerce_review_order_before_payment'><?php echo __('4 - woocommerce_review_order_before_payment', WSC__DOMAIN ); ?></option>
						<option <?php echo (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] == 'woocommerce_checkout_after_order_review') ? "selected='selected'":''; ?> value='woocommerce_checkout_after_order_review'><?php echo __('5 - woocommerce_checkout_after_order_review', WSC__DOMAIN ); ?></option>
					</select>
				</td>
			</tr>

			</tbody></table>
		
		</div>
		<div class="clear">&nbsp;</div>		
	</div>
	
	<?php
	}
	
	function WSC__Admin_Settings() {
		settings_fields ( WSC__SLUG.'_settings' );
		settings_fields ( WSC__SLUG.'_settings' );
	?>
	
<!-- Settings -->
	<div class="store-closing-boxes">
		<div class="store-closing-box store-closing-settings">
			<table class='storeclosing_admintable setting_table'>
				<tbody>
				<tr>
					<th width="200px" align='left' valign='middle' class="tabletitle"><?php echo __('Notification Settings', WSC__DOMAIN ); ?></th>
					<th width="200px" align='center' valign='middle'>
					<select id="notification_setting" name='storeclosing_settings[0]'>
						<option <?php echo ($this->WSC__Options_Data['settings'][0] == 'default') ? "selected='selected'":''; ?> value='default'><?php echo __('Default CSS', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][0] == 'custom') ? "selected='selected'":''; ?> value='custom'><?php echo __('Custom', WSC__DOMAIN ); ?></option>
					</select> 
					</th>
					<?php if(self::$mobile_theme == false) { ?>
						<td rowspan="<?php echo ($this->WSC__Options_Data['settings'][0] == 'default') ? '8':'14'; ?>" align='center' valign='middle' style='border:solid 1px #9E6095; border-style: dotted; position: relative; padding:2%;'>
							<?php 
								$WSC__Active_Plan = parent::WSC__Active_Plan();
								if($WSC__Active_Plan['store_check'] == 'CLOSE') {
									echo $WSC__Active_Plan['storeclosing_preview'];
								} else {
									echo "<h2>".__('Store is currently open', WSC__DOMAIN )."</h2>";
								}
							?>
							<span id="storeclosing_setting_loading" style="display:none;"><img width="50px" src="<?php echo esc_url( plugins_url( 'js/loading.gif', __FILE__ ) ); ?>" /></span>
						</td>
					<?php } ?>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo (isset($this->WSC__Options_Data['settings'][0]) && $this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Background color', WSC__DOMAIN ); ?> <span style="color:#CCC;">(#4285BA)</span></td>
					<td valign='middle'><input class="custom_settings" id="theme_color" type='text' name='storeclosing_settings[1]' value='<?php echo $this->WSC__Options_Data['settings'][1]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo (isset($this->WSC__Options_Data['settings'][0]) && $this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<th align='left' valign='middle'>&nbsp</th>
					<td align='left' valign='middle'>
						<div id="storeclosing_colorred"></div>
						<div id="storeclosing_colorgreen"></div>
						<div id="storeclosing_colorblue"></div>
					</td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Text color', WSC__DOMAIN ); ?> <span style="color:#CCC;">(#FFFFFF)</span></td>
					<td valign='middle'><input class="custom_settings" id="color" type='text' name='storeclosing_settings[2]' value='<?php echo $this->WSC__Options_Data['settings'][2]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Width', WSC__DOMAIN ); ?> <span style="color:#CCC;">(90% / 300px)</span></td>
					<td valign='middle'><input class="custom_settings" id="width" type='text' name='storeclosing_settings[3]' value='<?php echo $this->WSC__Options_Data['settings'][3]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Padding', WSC__DOMAIN ); ?> <span style="color:#CCC;">(20px)</span></td>
					<td valign='middle'><input class="custom_settings" id="padding" type='text' name='storeclosing_settings[4]' value='<?php echo $this->WSC__Options_Data['settings'][4]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Font Size', WSC__DOMAIN ); ?> <span style="color:#CCC;">(large / 24px)</span></td>
					<td valign='middle'><input class="custom_settings" id="font-size" type='text' name='storeclosing_settings[5]' value='<?php echo $this->WSC__Options_Data['settings'][5]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Text Align', WSC__DOMAIN ); ?> <span style="color:#CCC;">(center / left)</span></td>
					<td valign='middle'><input class="custom_settings" id="text-align" type='text' name='storeclosing_settings[6]' value='<?php echo $this->WSC__Options_Data['settings'][6]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Border Style', WSC__DOMAIN ); ?></td>
					<td valign='middle'>
					<select class="custom_settings" id="border-style" style="text-align:center !important;" name='storeclosing_settings[7]'>
						<option <?php echo ($this->WSC__Options_Data['settings'][7] == 'none') ? "selected='selected'":''; ?> value='none'><?php echo __('None', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][7] == 'solid') ? "selected='selected'":''; ?> value='solid'><?php echo __('Solid', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][7] == 'dashed') ? "selected='selected'":''; ?> value='dashed'><?php echo __('Dashed', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][7] == 'dotted') ? "selected='selected'":''; ?> value='dotted'><?php echo __('Dotted', WSC__DOMAIN ); ?></option>
					</select>                        
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Border Width', WSC__DOMAIN ); ?> <span style="color:#CCC;">(2px / thick)</span></td>
					<td valign='middle'><input class="custom_settings" id="border-width" type='text' name='storeclosing_settings[8]' value='<?php echo $this->WSC__Options_Data['settings'][8]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Border color', WSC__DOMAIN ); ?> <span style="color:#CCC;">(#264D6A)</span></td>
					<td valign='middle'><input class="custom_settings" id="border-color" type='text' name='storeclosing_settings[9]' value='<?php echo $this->WSC__Options_Data['settings'][9]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Border Radius', WSC__DOMAIN ); ?> <span style="color:#CCC;">(5px / 2%)</span></td>
					<td valign='middle'><input class="custom_settings" id="border-radius" type='text' name='storeclosing_settings[10]' value='<?php echo $this->WSC__Options_Data['settings'][10]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Margin', WSC__DOMAIN ); ?> <span style="color:#CCC;">(30px / 3%)</span></td>
					<td valign='middle'><input class="custom_settings" id="margin" type='text' name='storeclosing_settings[11]' value='<?php echo $this->WSC__Options_Data['settings'][11]; ?>'></td>
				</tr>
				<tr align='center' valign='middle' class="custom_settings" <?php echo ($this->WSC__Options_Data['settings'][0]!='custom')?"style='display:none'":''; ?>>
					<td align='left' valign='middle'><?php echo __('Icon', WSC__DOMAIN ); ?></td>
					<td valign='middle'>
					<select class="custom_settings" id="icon" style="text-align:center !important;" name='storeclosing_settings[12]'>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'none') ? "selected='selected'":''; ?> value='none'><?php echo __('None', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'info') ? "selected='selected'":''; ?> value='info'><?php echo __('Info', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'warning') ? "selected='selected'":''; ?> value='warning'><?php echo __('Warning', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'clock') ? "selected='selected'":''; ?> value='clock'><?php echo __('Clock', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'key') ? "selected='selected'":''; ?> value='key'><?php echo __('Key', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'speech') ? "selected='selected'":''; ?> value='speech'><?php echo __('Speech', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'lock') ? "selected='selected'":''; ?> value='lock'><?php echo __('Lock', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'unlock') ? "selected='selected'":''; ?> value='unlock'><?php echo __('Unlock', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'megaphone') ? "selected='selected'":''; ?> value='megaphone'><?php echo __('Megaphone', WSC__DOMAIN ); ?></option>
						<option <?php echo ($this->WSC__Options_Data['settings'][12] == 'cart') ? "selected='selected'":''; ?> value='cart'><?php echo __('Cart', WSC__DOMAIN ); ?></option>
					</select>
					</td>
				</tr>
				<tr align='center' valign='middle'><th colspan="2"><hr /></th></tr>
				<tr align='center' valign='middle'>
					<td align='left' valign='middle'><?php echo __('Role Permision', WSC__DOMAIN ); ?></td>
					<td valign='middle'>
					<?php global $wp_roles; ?>
					<select name="storeclosing_settings[13]" style="width:100%;">
						<option value=""><?php echo __( 'Select Role', WSC__DOMAIN ); ?></option>                            
				<?php	foreach ( $wp_roles->roles as $key=>$role ) { 
						if ($key != 'administrator'){
				?>
						<option <?php echo (isset($this->WSC__Options_Data['settings'][13]) && $this->WSC__Options_Data['settings'][13] == $key) ? "selected='selected'":''; ?> value="<?php echo $key; ?>"><?php echo $role['name']; ?></option>
				<?php	}} ?>
					</select>
					</td>
				</tr>
				<tr align='center' valign='middle'><th colspan="2"><hr /></th></tr>
				<tr align='center' valign='middle'>
					<td align='left' valign='middle'><?php echo __('Backup', WSC__DOMAIN ); ?></td>
					<td align='left' valign='middle'>
						<a id='backup' class="button-primary" style="width:100%;"><?php echo __('Backup All Settings Now', WSC__DOMAIN ); ?></a>
						<span id="storeclosing_backup_loading" style="vertical-align: middle; display:none;" ><img width="23px" src="<?php echo esc_url( plugins_url( 'js/loading.gif', __FILE__ ) ); ?>" /></span>
					</td>
					<td align='left' valign='middle'>
						<a id='restore' class="button-secondary"><?php echo __('Restore Last Backup', WSC__DOMAIN ); ?> &nbsp:&nbsp&nbsp <span id='storeclosing_backup_date' style='background: #9E6095; color:#FFFFFF; padding: 6px;'><?php echo get_option( WSC__SLUG . '_backup'); ?></span></a>
						<a id='cleardb' class="button-secondary"><?php echo __('Clear all data', WSC__DOMAIN ); ?></a>
					</td>
				</tr>
			</tbody></table>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
	
	function WSC__Admin_Dismiss() {
	?>
<!-- Dismiss -->
	<div class="store-closing-boxes">
		<div class="store-closing-box store-closing-settings dismiss">
		
		<a id="storeclosing_dismiss" href="?page=store-closing/storeclosing-setting.php&tab=&dismiss=RemindMe"><?php echo __( "Close this", WSC__DOMAIN ) ?></a>
        <a href="https://codecanyon.net/item/woocommerce-remind-me/20892387?ref=Ozibal" target="_blank"><img class="alignleft" style="padding:10px;" src="<?php echo esc_url( plugins_url( 'img/remindme_icon-80x80.png', __FILE__ ) ); ?>"></a>
        <a href="https://codecanyon.net/item/woocommerce-remind-me/20892387?ref=Ozibal" target="_blank"><h1><?php echo __( "WooCommerce Remind Me", WSC__DOMAIN ) ?></h1></a>
        <p><strong><?php echo __( "This plugin", WSC__DOMAIN ) ?></strong> : <?php echo __( "Places a great-looking animated bell icon under the WSC (WooCommerce Store Closing) notifications. Your customers can type in email addresses and add a reminder. Automatically sends e-mail when the store is open.", WSC__DOMAIN ) ?></p>
        <a href="https://codecanyon.net/item/woocommerce-remind-me/20892387?ref=Ozibal" target="_blank" class="more"><?php echo __( "More Details", WSC__DOMAIN ) ?></a>
        <p><hr /></p>
        <div class="dismiss_galery">
        <a href="https://codecanyon.net/item/woocommerce-remind-me/20892387?ref=Ozibal" target="_blank"><img src="<?php echo esc_url( plugins_url( 'img/remindme_screenshot_10.png', __FILE__ ) ); ?>"></a>
        <a href="https://codecanyon.net/item/woocommerce-remind-me/20892387?ref=Ozibal" target="_blank"><img src="<?php echo esc_url( plugins_url( 'img/remindme_screenshot_13.png', __FILE__ ) ); ?>"></a>
        </div>
	
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	<?php
	}
		
	function WSC__Admin_Mobil_Check() {
		
		$useragent=$_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))

		self::$mobile_theme = true;
		
	}
	
}
?>