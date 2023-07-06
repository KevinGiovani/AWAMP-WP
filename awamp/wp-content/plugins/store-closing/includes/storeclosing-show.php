<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WSC__Show_StoreClosing extends WSC__StoreClosing{
	
	private $storeclosing_options;
	private $day_number;
	private $storeclosing_day;
	private $now_date;
	private $local_time;
	private $closed_upcoming;
	
	public 	$storeclosing_notification;
	public 	$storeclosing_preview;
	public 	$store_check;
	public 	$active_daily;
	public 	$active_plan;
	public 	$active_time;
	
	public function __construct() {
	
		$this->WSC__Options_Data = parent::WSC__Options_Data();
				
		$this->active_daily = '';
		$this->active_plan = '';
		$this->closed_upcoming = '';
				
		$this->now_date =  strtotime(date('Y-m-d H:i', current_time( 'timestamp', 0 )));
		$this->local_time =  date('Hi', current_time( 'timestamp', 0 ) );
		$this->day_number = date('N', current_time( 'timestamp', 0 ) ) - 1;
		
		$this->storeclosing_day = $this->WSC__Options_Data['daily'][$this->day_number];
		$this->storeclosing_control();
		
		$this->storeclosing_preview = self::storeclosing_show('preview'); //v9.5.6
		
		WSC__Show_StoreClosing::WSC__Get_Active_Plan();
				
	}

	public function storeclosing_control(){

		if($this->WSC__Options_Data['manual'] == 'on'){ //Manual
			
			$this->active_plan = __( 'Manual', WSC__DOMAIN );
			$this->active_time['manual'] = array(
				'manual',
				'manual',
				__( 'Store is Close', WSC__DOMAIN )
			);
			
			$this->storeclosing_notification = $this->WSC__Options_Data['notification'][0];			
			$this->store_check = 'CLOSE'; // Store is close
			
			add_action( 'wp_loaded', array( $this, 'storeclosing_disable' ), 10 ); //v9.5.3
			$this->storeclosing_show_switch(); 	
			
		} else { //Upcoming & Daily
			
			// Search Active Upcoming Plan v9.5.2
			$upcoming_plans = (is_array($this->WSC__Options_Data['upcoming'])) ? count($this->WSC__Options_Data['upcoming']) : 0;

			if($upcoming_plans > 0) {
				$this->WSC__Options_Data['upcoming'] = array_values($this->WSC__Options_Data['upcoming']);
				usort($this->WSC__Options_Data['upcoming'],function($a,$b){
					
					$result = strcmp($a[1],$b[1]);
					$result .= strcmp($a[2],$b[2]);
					$result .= strcmp($a[3],$b[3]);
					
					return $result;
				});
			}
			if (is_array($this->WSC__Options_Data['upcoming'])) {
			
			foreach($this->WSC__Options_Data['upcoming'] as $plan_key => $upcoming_plans){

				$opening_date = strtotime($upcoming_plans[5].' '.$upcoming_plans[6].':'.$upcoming_plans[7]);
				
				if($opening_date < $this->now_date){ //Delete Old Upcoming Plan(s)
						
					unset($this->WSC__Options_Data['upcoming'][$plan_key]);
					update_option( WSC__SLUG.'_upcoming', $this->WSC__Options_Data['upcoming']);
					
					add_action( 'admin_notices', array( $this, 'storeclosing_notice' ), 10 );
					
				}
				if(isset($this->WSC__Options_Data['upcoming'][$plan_key][0]) && $this->WSC__Options_Data['upcoming'][$plan_key][0] == 'on' ) {
										
					$closedate = strtotime($this->WSC__Options_Data['upcoming'][$plan_key][1]);
					$closedays = ceil(($closedate - $this->now_date) / 86400);
					$closing_time = $this->WSC__Options_Data['upcoming'][$plan_key][2].$this->WSC__Options_Data['upcoming'][$plan_key][3];
					$opendate = strtotime($this->WSC__Options_Data['upcoming'][$plan_key][5]);
					$opendays = ceil(($opendate - $this->now_date) / 86400);
					$opening_time = $this->WSC__Options_Data['upcoming'][$plan_key][6].$this->WSC__Options_Data['upcoming'][$plan_key][7];
					$show_date = ($this->WSC__Options_Data['upcoming'][$plan_key][9] != '') ? strtotime($this->WSC__Options_Data['upcoming'][$plan_key][9]) : $closedate;
					$show_days = ceil(($show_date - $this->now_date) / 86400);
					$show_message = ($this->WSC__Options_Data['upcoming'][$plan_key][9] !='') ? $this->storeclosing_options[4][13] : 'visibility';
					
					if( $show_days <= 0 ){
						$this->closed_upcoming = $this->WSC__Options_Data['upcoming'][$plan_key][10];

						if ($this->WSC__Options_Data['upcoming'][$plan_key][4] != '' ){
							
							$notification_time = date('d.m.Y', strtotime($this->WSC__Options_Data['upcoming'][$plan_key][1]) )." - ".$this->WSC__Options_Data['upcoming'][$plan_key][2].':'.$this->WSC__Options_Data['upcoming'][$plan_key][3];
														
							$this->storeclosing_notification = $this->WSC__Options_Data['upcoming'][$plan_key][4];
							
							$this->storeclosing_show_switch();
								
						}
						
					}
					
					if( $closedays > 0 || ($closedays == 0 && $closing_time > $this->local_time) ){ 
					// Between Now & Closing

						if ($this->WSC__Options_Data['upcoming'][$plan_key][4] != '' ){
							
							$this->active_plan = __( 'Upcoming ( Closing )', WSC__DOMAIN ).' : '
							.date( get_option('date_format'), strtotime( $upcoming_plans[1] ) )
							.' '
							.date( get_option('time_format'), strtotime( $upcoming_plans[2].':'.$upcoming_plans[3] ) );
							
							$this->active_time['upcoming'] = array(
								date( get_option('date_format'), strtotime( $upcoming_plans[1] ) ),
								date( get_option('time_format'), strtotime( $upcoming_plans[2].':'.$upcoming_plans[3] ) ),
								date('M j, Y', strtotime($upcoming_plans[1]) )." ".$upcoming_plans[2].":".$upcoming_plans[3].":00"
							);							
							
						}else{
						
							$this->active_plan = __( 'Upcoming ( Closing )', WSC__DOMAIN ).' : '
							.date( get_option('date_format'), strtotime( $upcoming_plans[1] ) )
							.' '
							.date( get_option('time_format'), strtotime( $upcoming_plans[2].':'.$upcoming_plans[3] ) )
							.' [ ! '.__( 'Warning', WSC__DOMAIN ).' : '.__( 'No Notification Message', WSC__DOMAIN ).' ]';
							
							$this->active_time['upcoming'] = array(
								date( get_option('date_format'), strtotime( $upcoming_plans[1] ) ),
								date( get_option('time_format'), strtotime( $upcoming_plans[2].':'.$upcoming_plans[3] ) ),
								date('M j, Y', strtotime($upcoming_plans[1]) )." ".$upcoming_plans[2].":".$upcoming_plans[3].":00"
							);

						}
						
					}else if($opendays > 0 or ($opendays == 0 and $opening_time > $this->local_time)){ 
					// Between Closing & Opening
						
						if($this->WSC__Options_Data['upcoming'][$plan_key][8] != ''){
						
							$this->active_plan = __( 'Upcoming ( Opening )', WSC__DOMAIN ).' : '
							.date( get_option('date_format'), strtotime( $upcoming_plans[5] ) )
							.' '
							.date( get_option('time_format'), strtotime( $upcoming_plans[6].':'.$upcoming_plans[7] ) );
							
							$this->active_time['upcoming'] = array(
								date( get_option('date_format'), strtotime( $upcoming_plans[5] ) ),
								date( get_option('time_format'), strtotime( $upcoming_plans[6].':'.$upcoming_plans[7] ) ),
								date('M j, Y', strtotime($upcoming_plans[5]) )." ".$upcoming_plans[6].":".$upcoming_plans[7].":00"
							);

							
						}else{
						
							$this->active_plan = __( 'Upcoming ( Opening )', WSC__DOMAIN ).' : '
							.date( get_option('date_format'), strtotime( $upcoming_plans[5] ) )
							.' '
							.date( get_option('time_format'), strtotime( $upcoming_plans[6].':'.$upcoming_plans[7] ) )
							.' [ ! '.__( 'Warning', WSC__DOMAIN ).' : '.__( 'No Notification Message', WSC__DOMAIN ).' ]';
							
							$this->active_time['upcoming'] = array(
								date( get_option('date_format'), strtotime( $upcoming_plans[5] ) ),
								date( get_option('time_format'), strtotime( $upcoming_plans[6].':'.$upcoming_plans[7] ) ),
								date('M j, Y', strtotime($upcoming_plans[5]) )." ".$upcoming_plans[6].":".$upcoming_plans[7].":00"
							);							
							
						}
																				
						$notification_time = date('d.m.Y', strtotime($this->WSC__Options_Data['upcoming'][$plan_key][5]) )." - ".$this->WSC__Options_Data['upcoming'][$plan_key][6].':'.$this->WSC__Options_Data['upcoming'][$plan_key][7];
						
						$countdown_date = date('M j, Y', strtotime($this->WSC__Options_Data['upcoming'][$plan_key][5]) )." ".$this->WSC__Options_Data['upcoming'][$plan_key][6].":".$this->WSC__Options_Data['upcoming'][$plan_key][7].":00";
						
						$this->storeclosing_notification = $this->WSC__Options_Data['upcoming'][$plan_key][8];
						
						$this->storeclosing_show_switch();
												
						add_action( 'wp_loaded', array( $this, 'storeclosing_disable' ), 10 ); //v9.5.3

						$this->closed_upcoming = 'yes'; // Daily is off
						$this->store_check = 'CLOSE'; // Store is close
						
					}
					
					break;
				}
			}
			}
			// End : Search Active Upcoming Plan v9.5.2
			
			// Search Active Daily Plan v9.5.2
			$count = $this->day_number + 1;
			for ($i = 1; $i <= 7; $i++) {
				
				if (isset($this->WSC__Options_Data['daily'][$count][0]) && $this->WSC__Options_Data['daily'][$count][0] == 'on'){
					$this->active_daily = array(
						$this->WSC__Options_Data['daily'][$count][1],
						$this->WSC__Options_Data['daily'][$count][2],
						$this->WSC__Options_Data['daily'][$count][3],
						$this->WSC__Options_Data['daily'][$count][4],
						$this->WSC__Options_Data['daily'][$count][5],
						date("M j, Y", strtotime("$i day") )
					);
					break;								
				}
				
				if (isset($this->WSC__Options_Data['daily'][$count][6]) && $this->WSC__Options_Data['daily'][$count][6] == 'on'){
					$this->active_daily = array(
						$this->WSC__Options_Data['daily'][$count][7],
						$this->WSC__Options_Data['daily'][$count][8],
						$this->WSC__Options_Data['daily'][$count][9],
						$this->WSC__Options_Data['daily'][$count][10],
						$this->WSC__Options_Data['daily'][$count][11],
						date("M j, Y", strtotime("$i day") )
					);
					break;
				}
				($count > 5) ? $count = 0:$count ++;		
			}
			
			if( $this->active_daily != '') { //Daily v9.5.1
			
			for ($i=1; $i<=4; $i++) if(strlen($this->storeclosing_day[$i]) == 1) $this->storeclosing_day[$i] = '0'.$this->storeclosing_day[$i];
			$opening_time_first = $this->storeclosing_day[1].$this->storeclosing_day[2];
			$closing_time_first = $this->storeclosing_day[3].$this->storeclosing_day[4];
			
			for ($i=7; $i<=10; $i++) if(strlen($this->storeclosing_day[$i]) == 1) $this->storeclosing_day[$i] = '0'.$this->storeclosing_day[$i];			
			$opening_time_second = $this->storeclosing_day[7].$this->storeclosing_day[8];
			$closing_time_second = $this->storeclosing_day[9].$this->storeclosing_day[10];
			
			if (isset($this->storeclosing_day[0]) && $this->local_time < $closing_time_first) { //First
				
				$this->store_check = self::storeclosing_datecheck($this->storeclosing_day[0], $opening_time_first, $closing_time_first);
				
				if($this->store_check == 'CLOSE' ){ //First Active
					
					$this->active_plan = ($this->active_plan == '') 
						? __( 'Daily (First)', WSC__DOMAIN ).' : '
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[1].':'.$this->storeclosing_day[2] ) ).'-'
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[3].':'.$this->storeclosing_day[4] ) )
						: __( 'Daily (First)', WSC__DOMAIN ).' : '
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[1].':'.$this->storeclosing_day[2] ) ).'-'
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[3].':'.$this->storeclosing_day[4] ) )
						.' & '.$this->active_plan;
					
					$this->active_time['daily'] = array(
						date( get_option('date_format'), current_time( 'timestamp', 0 )),
						date( get_option('time_format'), strtotime( $this->storeclosing_day[1].':'.$this->storeclosing_day[2] ) ),
						date('M j, Y', current_time( 'timestamp', 0 ))." ".$this->storeclosing_day[1].":".$this->storeclosing_day[2].":00"
					);
					
					if( $this->closed_upcoming == '' || $this->closed_upcoming == 'visibility') { 
						if ($this->storeclosing_notification != '' ) {
							$this->storeclosing_notification = ($this->storeclosing_day[5] !='')
								? $this->storeclosing_day[5] .'<br>'. $this->storeclosing_notification
								: $this->WSC__Options_Data['notification'][0] .'<br>'. $this->storeclosing_notification;
						} else {
							$this->storeclosing_notification = ($this->storeclosing_day[5] !='')
								? $this->storeclosing_day[5]
								: $this->WSC__Options_Data['notification'][0];
						}
						
					}
					
					add_action( 'wp_loaded', array( $this, 'storeclosing_disable' ), 10 ); //v9.5.3
					
				}
				
			} else if (isset($this->storeclosing_day[6]) && $this->local_time < $closing_time_second) { //Second
				
				$this->store_check = self::storeclosing_datecheck($this->storeclosing_day[6], $opening_time_second, $closing_time_second);
				
				if($this->store_check == 'CLOSE' ){ //Second Active
				
					$this->active_plan = ($this->active_plan == '') 
						? __( 'Daily (Second)', WSC__DOMAIN ).' : '
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[7].':'.$this->storeclosing_day[8] ) ).'-'
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[9].':'.$this->storeclosing_day[10] ) )
						: __( 'Daily (Second)', WSC__DOMAIN ).' : '
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[7].':'.$this->storeclosing_day[8] ) ).'-'
						.date( get_option('time_format'), strtotime( $this->storeclosing_day[9].':'.$this->storeclosing_day[10] ) )
						.' & '.$this->active_plan;

					$this->active_time['daily'] = array(
						date( get_option('date_format'), current_time( 'timestamp', 0 )),
						date( get_option('time_format'), strtotime( $this->storeclosing_day[7].':'.$this->storeclosing_day[8] ) ),
						date('M j, Y', current_time( 'timestamp', 0 ))." ".$this->storeclosing_day[7].":".$this->storeclosing_day[8].":00"
					);
					
					if( $this->closed_upcoming == '' || $this->closed_upcoming == 'visibility') { 
						if ($this->storeclosing_notification != '' ) {
							$this->storeclosing_notification = ($this->storeclosing_day[11] !='')
								? $this->storeclosing_day[11] .'<br>'. $this->storeclosing_notification
								: $this->WSC__Options_Data['notification'][0] .'<br>'. $this->storeclosing_notification;
						} else {
							$this->storeclosing_notification = ($this->storeclosing_day[11] !='')
								? $this->storeclosing_day[11]
								: $this->WSC__Options_Data['notification'][0];
						}
						
					}
					
					add_action( 'wp_loaded', array( $this, 'storeclosing_disable' ), 10 ); //v9.5.3
				
				}	
			} else { // Next Day(s)
							
				$this->active_plan = ($this->active_plan == '') 
					? __( 'Daily', WSC__DOMAIN ).' : '
					.date( get_option('time_format'), strtotime( $this->active_daily[0].':'.$this->active_daily[1] ) ).'-'
					.date( get_option('time_format'), strtotime( $this->active_daily[2].':'.$this->active_daily[3] ) )
					.' ( '.date( get_option('date_format'), strtotime( $this->active_daily[5] ) ).' )' 
					: __( 'Daily', WSC__DOMAIN ).' : '
					.date( get_option('time_format'), strtotime( $this->active_daily[0].':'.$this->active_daily[1] ) ).'-'
					.date( get_option('time_format'), strtotime( $this->active_daily[2].':'.$this->active_daily[3] ) )
					.' ( '.date( get_option('date_format'), strtotime( $this->active_daily[5] ) ).' ) & '
					.$this->active_plan;

				$this->active_time['daily'] = array(
					date( get_option('date_format'), strtotime( $this->active_daily[5] ) ),
					date( get_option('time_format'), strtotime( $this->active_daily[0].':'.$this->active_daily[1] ) ),
					date('M j, Y', strtotime($this->active_daily[5]))." ".$this->active_daily[0].":".$this->active_daily[1].":00"
				);
				
				if( $this->closed_upcoming == '' || $this->closed_upcoming == 'visibility') { 
					if ($this->storeclosing_notification != '' ) {
						$this->storeclosing_notification = ($this->active_daily[4] !='')
							? $this->active_daily[4] .'<br>'. $this->storeclosing_notification
							: $this->WSC__Options_Data['notification'][0] .'<br>'. $this->storeclosing_notification;
					} else {
						$this->storeclosing_notification = ($this->active_daily[4] !='')
							? $this->active_daily[4]
							: $this->WSC__Options_Data['notification'][0];
					}
					
				}
				
				add_action( 'wp_loaded', array( $this, 'storeclosing_disable' ), 10 ); //v9.5.3
				$this->store_check = 'CLOSE';
								
			}
			}	
			
			if ($this->closed_upcoming != 'yes'){		
				$this->storeclosing_show_switch();
			}
		}

		return false;
	}
	
	public function storeclosing_datecheck($active, $open, $close){
		return ($active != 'true' && $this->local_time >= $open && $this->local_time < $close) ? 'OPEN' : 'CLOSE';
	}
	
	public function storeclosing_theme($notification, $theme = '', $comefrom = ''){
		
		$icon = '';
		$StoreClosingPopupClose =($comefrom == "popup") 
			? "[StoreClosingPopupClose]" 
			: "";
		
		$notification_style = '';
		$storeclosing_notification_show = '';
		
		$countdown_show = (isset($this->WSC__Options_Data['settings'][21]) && $this->WSC__Options_Data['settings'][21]==true)
			? "display:block;"
			: "display:none;";

		$stamp_daily = (isset($this->active_time['daily'][0]))
			?( $this->active_time['daily'][0] > date( get_option('date_format'), current_time( 'timestamp', 0 )) )
				? $this->active_time['daily'][1]." (".$this->active_time['daily'][0]." )"
				: $this->active_time['daily'][1]
			: '';
		
		$stamp_upcoming = (isset($this->active_time['upcoming'][0]))
			? ( $this->active_time['upcoming'][0] > date( get_option('date_format'), current_time( 'timestamp', 0 )) )
				? $this->active_time['upcoming'][1]." (".$this->active_time['upcoming'][0]." )"
				: $this->active_time['upcoming'][1]
			: '';
			
		$countdown = self::storeclosing_countdown();
			
		$notification = str_replace ('[tstamp]', $stamp_daily, $notification);
		$notification = str_replace ('[pcstamp]', $stamp_upcoming, $notification);
		$notification = str_replace ('[postamp]', $stamp_upcoming, $notification);
		$notification = str_replace ('[countdown]', $countdown , $notification);
		
									
		//V9.5.8 Theme		
		switch ($theme) { 
			case 'default':
			
				$notification_style = "class='storeclosing_show storeclosing_".$theme."'";
				$storeclosing_notification_show = $notification;
							
				break;
			case 'custom':
			
				if($this->WSC__Options_Data['settings'][12] != 'none'){
					
					switch ($this->WSC__Options_Data['settings'][12]) {
						case 'info':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-warning' ></div>";
							break;
						case 'warning':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-dismiss' ></div>";
							break;
						case 'clock':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-clock' ></div>";
							break;
						case 'key':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-admin-network' ></div>";
							break;
						case 'speech':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-format-status' ></div>";
							break;
						case 'lock':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-lock' ></div>";
							break;
						case 'unlock':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-unlock' ></div>";
							break;
						case 'megaphone':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-megaphone' ></div>";
							break;
						case 'cart':
							$icon = "<div id='storeclosing_icon' class='dashicons-before dashicons-cart' ></div>";
							break;
						default:
							$icon = '';
							break;
					}
				} 
				
				$notification_style = "class='storeclosing_show storeclosing_".$theme."' style='background-color:".$this->WSC__Options_Data['settings'][1]."; color:".$this->WSC__Options_Data['settings'][2]."; 
				width:".$this->WSC__Options_Data['settings'][3]."; 
				padding:".$this->WSC__Options_Data['settings'][4]."; 
				font-size:".$this->WSC__Options_Data['settings'][5]."; 
				text-align:".$this->WSC__Options_Data['settings'][6]."; 
				border-style:".$this->WSC__Options_Data['settings'][7]."; border-width:".$this->WSC__Options_Data['settings'][8]."; border-color:".$this->WSC__Options_Data['settings'][9]."; border-radius:".$this->WSC__Options_Data['settings'][10]."; 
				margin:".$this->WSC__Options_Data['settings'][11]."; 
				line-height: normal;'";	
				
				$storeclosing_notification_show = $icon.'&nbsp;&nbsp;&nbsp;'.$notification;
			
				break;
			
			default:
				
				$notification_style = "class='storeclosing_".$comefrom."'";
				$storeclosing_notification_show = $notification;
				
				break;
		}
		
		$notification = "
			<div id='storeclosing_".$comefrom."' ".$notification_style.">
				".$StoreClosingPopupClose."
				".$storeclosing_notification_show." 
			</div>";
		
		return $notification;
		
	}
	
	public function storeclosing_show_switch(){	
		
		if(isset($this->WSC__Options_Data['notification'][19])) { // Product Notification
			$hook_product = (isset($this->WSC__Options_Data['notification'][20]) && $this->WSC__Options_Data['notification'][20] != '')
				? $this->WSC__Options_Data['notification'][20]
				: 'woocommerce_before_add_to_cart_button';
			add_action( $hook_product, array( $this, 'storeclosing_show' ), 10 );
		}
		
		if(isset($this->WSC__Options_Data['notification'][21])) { // Shop Notification
			$hook_shop = (isset($this->WSC__Options_Data['notification'][22]) && $this->WSC__Options_Data['notification'][22] != '')
				? $this->WSC__Options_Data['notification'][22]
				: 'woocommerce_before_shop_loop';
			add_action( $hook_shop, array( $this, 'storeclosing_show' ), 10 );
		}
		
		if(isset($this->WSC__Options_Data['notification'][23])) { // Cart Notification
			$hook_payment = (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] != '')
				? $this->WSC__Options_Data['notification'][24]
				: 'woocommerce_before_cart';
			add_action( $hook_payment, array( $this, 'storeclosing_show' ), 10 );
		}
		
		if(isset($this->WSC__Options_Data['notification'][25])) { // Payment Notification
			$hook_payment = (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] != '')
				? $this->WSC__Options_Data['notification'][26]
				: 'woocommerce_review_order_before_payment';
			add_action( $hook_payment, array( $this, 'storeclosing_show' ), 10 );
		}
		
		if(isset($this->WSC__Options_Data['popup'][0])) { // Popup Notification
			add_action( 'wp_head', array( $this, 'storeclosing_show_popup' ), 10 ); 
		}
	}
	
	function storeclosing_show($comefrom = '') {
		
		//V9.6.0 Dayparts Notification Message 
		self::storeclosing_dayparts();
	
		if ($this->storeclosing_notification != '' && $this->store_check == 'CLOSE'){
			
			$storeclosing_notification_show = self::storeclosing_theme(
				$this->storeclosing_notification, 
				$this->WSC__Options_Data['settings'][0],
				($comefrom != '') ? $comefrom : 'show'
			);
					
			if($comefrom != ''){ 
				return $storeclosing_notification_show; 
			} else { 
				echo $storeclosing_notification_show; 
			}
			
		}
	}
	
	function storeclosing_dayparts() {
	
		$morning_time_starting = $this->WSC__Options_Data['notification'][2].$this->WSC__Options_Data['notification'][3];
		$morning_time_ending = $this->WSC__Options_Data['notification'][4].$this->WSC__Options_Data['notification'][5];
		$noon_time_starting = $this->WSC__Options_Data['notification'][8].$this->WSC__Options_Data['notification'][9];
		$noon_time_ending = $this->WSC__Options_Data['notification'][10].$this->WSC__Options_Data['notification'][11];
		$afternoon_time_starting = $this->WSC__Options_Data['notification'][14].$this->WSC__Options_Data['notification'][15];
		$afternoon_time_ending = $this->WSC__Options_Data['notification'][16].$this->WSC__Options_Data['notification'][17];
		
		if(
			$this->WSC__Options_Data['notification'][6] != '' && 
			(isset($this->WSC__Options_Data['notification'][1]) && $this->WSC__Options_Data['notification'][1] == true) && 
			($morning_time_starting <= $this->local_time && $morning_time_ending > $this->local_time) )
		{ // Between Morning
		
			$result = strpos($this->storeclosing_notification, str_replace('[gm]','',$this->WSC__Options_Data['notification'][6]));
			if ($result === false) {
				$this->storeclosing_notification =  str_replace ('[gm]', $this->storeclosing_notification, $this->WSC__Options_Data['notification'][6]);
			}			
			
		} else if(
			$this->WSC__Options_Data['notification'][12] != '' && 
			(isset($this->WSC__Options_Data['notification'][7]) && $this->WSC__Options_Data['notification'][7] == true) && 
			($noon_time_starting <= $this->local_time && $noon_time_ending > $this->local_time) )
		{ // Between Noon
			
			$result = strpos($this->storeclosing_notification, str_replace('[gm]','',$this->WSC__Options_Data['notification'][12]));
			if ($result === false) {
				$this->storeclosing_notification =  str_replace ('[gm]', $this->storeclosing_notification, $this->WSC__Options_Data['notification'][12]);
			}
				
		} else if(
			$this->WSC__Options_Data['notification'][18] != '' && 
			(isset($this->WSC__Options_Data['notification'][13]) && $this->WSC__Options_Data['notification'][13] == true) && 
			($afternoon_time_starting <= $this->local_time && $afternoon_time_ending > $this->local_time) )
		{ // Between Afternoon
		
			$result = strpos($this->storeclosing_notification, str_replace('[gm]','',$this->WSC__Options_Data['notification'][18]));
			if ($result === false) {
				$this->storeclosing_notification =  str_replace ('[gm]', $this->storeclosing_notification, $this->WSC__Options_Data['notification'][18]);
			}	
		
		}
	
		
	}
	
	public function storeclosing_show_popup($page = '') {
		
		//V9.6.0 Exclude page
		$pages = explode( ',', $this->WSC__Options_Data['popup'][11] );
		if( !is_page( $pages ) ){
		
		// Popup
		$StoreClosingPopupMessage = $this->storeclosing_show('popup');
		
		$StoreClosingPopupClass = ($this->WSC__Options_Data['popup'][1] == 'top') 
			? $this->WSC__Options_Data['popup'][2].' storeclosing_popup_top'
			: $this->WSC__Options_Data['popup'][2].' storeclosing_popup_bottom';
			
		$StoreClosingThemeClass = WSC__SLUG."_".$this->WSC__Options_Data['settings'][0];
			
		$StoreClosingPopupClosingTime = ($this->WSC__Options_Data['popup'][4] != '' || $this->WSC__Options_Data['popup'][4] != 0) 
			? "<span id='storeclosing_popup_countdown' class='storeclosing_popup_countdown' data-storeclosing_popup='".date('M j, Y H:i:s', current_time( 'timestamp', 0 ) + $this->WSC__Options_Data['popup'][4] )."' data-days='Day(s)' data-wpnow='".date('M j, Y H:i:s', current_time( 'timestamp', 0 ))."'></span>" 
			: 0;
			
		$StoreClosingPopupClose = ($this->WSC__Options_Data['popup'][3] == 'yes') 
			? "<div id='storeclosing_popup_close' class='storeclosing_popup_close'> X ".__( 'Close', WSC__DOMAIN )."&nbsp;".$StoreClosingPopupClosingTime."&nbsp;&nbsp;</div>" 
			: "<div class='storeclosing_popup_close' style='display:none;'>".$StoreClosingPopupClosingTime."&nbsp;&nbsp;</div>";
			
		$StoreClosingPopupMessage = str_replace( '[StoreClosingPopupClose]', $StoreClosingPopupClose, $StoreClosingPopupMessage );
		
		$StoreClosingPopupStyle = "style='display:none; ";
		$StoreClosingPopupStyle .= ($this->WSC__Options_Data['popup'][6] != '' && $this->WSC__Options_Data['popup'][6] != 100) 
			? "opacity: 0.".$this->WSC__Options_Data['popup'][6]."; filter: alpha(opacity=".$this->WSC__Options_Data['popup'][6]."); " 
			: "opacity: 1; filter: alpha(opacity=100); ";
		$StoreClosingPopupStyle .= ($this->WSC__Options_Data['popup'][5] != '') ? "background-color: ".$this->WSC__Options_Data['popup'][5]."; " : "";
		$StoreClosingPopupStyle .= "'";
		
		if ($this->WSC__Options_Data['popup'][12] == 'pasive') {
			$StoreClosingPopupCookieExpries = 'pasive';
		} else {
			$StoreClosingPopupCookieExpries = 
				(($this->WSC__Options_Data['popup'][7] > 0) ? $this->WSC__Options_Data['popup'][7] * (24*60*60*1000) : 1) * (($this->WSC__Options_Data['popup'][8] > 0) ? $this->WSC__Options_Data['popup'][8] * (60*60*1000) : 1) * (($this->WSC__Options_Data['popup'][9] > 0) ? $this->WSC__Options_Data['popup'][9] * (60*1000) : 1) * (($this->WSC__Options_Data['popup'][10] > 0) ? $this->WSC__Options_Data['popup'][10] * (1000) : 1);
		
			$StoreClosingPopupCookieExpries = ($StoreClosingPopupCookieExpries <= 1) 
				? (7*24*60*60*1000) 
				: $StoreClosingPopupCookieExpries;
		}		

		echo "
			<div id='storeclosing_popup_main' class='".$StoreClosingPopupClass." ".$StoreClosingThemeClass."' ".$StoreClosingPopupStyle." align='center'>
				<input id='storeclosing_popup_cookieexpries' type='hidden' value='".$StoreClosingPopupCookieExpries."'>
				".$StoreClosingPopupMessage."
			</div>
		";
		}

	}
	
	function storeclosing_disable() {
		
		wp_enqueue_style( 'WSC__storeclosing_disable', WSC__PLUGIN_URL .  'includes/storeclosing-disable.css');
		
		if(isset($this->WSC__Options_Data['exclude'][0]) && $this->WSC__Options_Data['exclude'][0] == 'on' && isset($this->WSC__Options_Data['exclude'][1]) && $this->WSC__Options_Data['exclude'][1] != ''){//v9.5.5
			add_action('woocommerce_after_add_to_cart_button',array( $this, 'exclude_category_product_page' ));//v9.5.5
			
			//v9.6.4
			$hook_payment = (isset($this->WSC__Options_Data['notification'][24]) && $this->WSC__Options_Data['notification'][24] != '')
				? $this->WSC__Options_Data['notification'][24]
				: 'woocommerce_before_cart';
			add_action( $hook_payment, array( $this, 'exclude_category_checkout_page' ), 10 );
			
			//v9.6.4
			$hook_payment = (isset($this->WSC__Options_Data['notification'][26]) && $this->WSC__Options_Data['notification'][26] != '')
				? $this->WSC__Options_Data['notification'][26]
				: 'woocommerce_review_order_before_payment';
			add_action( $hook_payment, array( $this, 'exclude_category_checkout_page' ), 10 );
		}

	}
	
	function exclude_category_product_page() {
		
		//v9.6.4
		if ( self::storeclosing_excludetimecheck() ){
		
			global $post;
			$authorized_category = $this->WSC__Options_Data['exclude'][1];
		
			$terms = get_the_terms( $post->ID, 'product_cat' );
		
			foreach ($terms as $term) {
				if ( $authorized_category == $term->term_id || $authorized_category == $term->parent) {
					echo "<script>jQuery('#WSC__storeclosing_disable-css, #storeclosing_show').remove();</script>";
				}
			}
			
		}
	}
	
	function exclude_category_checkout_page() {
		
		global $post;
		
		$authorized_category = $this->WSC__Options_Data['exclude'][1];
		$authorized_product = false;
		
		$exclude_message = $this->WSC__Options_Data['exclude'][5];
		$exclude_class = $this->WSC__Options_Data['exclude'][2];
		$exclude_style = ($this->WSC__Options_Data['exclude'][3] != '') ? "background-color: ".$this->WSC__Options_Data['exclude'][3]." !important; " : "";
		$exclude_style .= ($this->WSC__Options_Data['exclude'][4] != '' && $this->WSC__Options_Data['exclude'][4] != 100) ? "opacity: 0.".$this->WSC__Options_Data['exclude'][4]."; filter: alpha(opacity=".$this->WSC__Options_Data['exclude'][4]."); " : "opacity: 1; filter: alpha(opacity=100); ";
		$exclude_style = ($exclude_style !='') ? $exclude_style = "style='".$exclude_style."'" : '';
		
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			unset($sub_unauthorized_product);
			$item = $cart_item['data'];
			$product = wc_get_product( $cart_item['product_id'] );
		
			$terms = get_the_terms( $cart_item['product_id'], 'product_cat' );
			foreach ($terms as $term) {
				
				if ( $authorized_category == $term->term_id || $authorized_category == $term->parent) {
					unset($sub_unauthorized_product);
					$authorized_product = true;
					break;
				} else {
					$sub_unauthorized_product = '<br> * '.$product->get_name();
				}
			
			}
			
			if ( !empty($sub_unauthorized_product) ) {
				( !empty($unauthorized_product) ) 
					? $unauthorized_product .= '<br> * '.$product->get_name() 
					: $unauthorized_product = '<br> * '.$product->get_name();
			}
	
		}
		
		if (! empty($unauthorized_product) || !self::storeclosing_excludetimecheck()) {
			$exclude_message = (isset($unauthorized_product)) 
				? str_replace( '[plist]', $unauthorized_product , $exclude_message )
				: str_replace( '[plist]', '' , $exclude_message ) ;
				
			echo ($exclude_message) ? "<div class='".$exclude_class."' ".$exclude_style.">".$exclude_message."</div>" : '';
		} 

		//v9.6.4
		if($authorized_product == true && !isset($unauthorized_product) && self::storeclosing_excludetimecheck()) {
			echo "<script>jQuery('#WSC__storeclosing_disable-css, #storeclosing_show').remove();</script>";
		}
		
	}
	
	public function storeclosing_excludetimecheck(){
	
		//v9.6.4
		$opening_time = $this->WSC__Options_Data['exclude'][6].$this->WSC__Options_Data['exclude'][7];
		$closing_time = $this->WSC__Options_Data['exclude'][8].$this->WSC__Options_Data['exclude'][9];
		
		if (($opening_time == "" && $closing_time == "") || ($opening_time == "0000" && $closing_time == "0000")) {
			return true;
		} else {
			return ($this->local_time >= $opening_time && $this->local_time < $closing_time) ? true : false;
		}
	
		
	}
		
	function storeclosing_notice() {
		$class = 'notice notice-success is-dismissible';
		$message = __( 'Deleted ! WooCommerce / Store Closing / Upcoming Plan (Date / time is old.)', WSC__DOMAIN );
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	
	function storeclosing_countdown(){
	
		$countdown = '';
	
		if (isset($this->active_time['manual'][0]) && $this->active_time['manual'][0] == 'manual') { // Manual
			$this->stamp_daily = '';
			return '';
		} else {
			if ( isset($this->active_time['daily'][0]) && isset($this->active_time['upcoming'][0]) ){ //Daily & Upcoming
				$daily = strtotime($this->active_time['daily'][0]) + strtotime($this->active_time['daily'][1]);
				$upcoming = strtotime($this->active_time['upcoming'][0]) + strtotime($this->active_time['upcoming'][1]);

				if( $daily < $upcoming ){ // Daily is before
				
					$countdown =  "<span id='storeclosing_countdown_".rand()."' class='storeclosing_countdown' data-storeclosing='".$this->active_time['daily'][2]."' data-days='Day(s)' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."'></span>";
					
				} else { // Upcoming is before
	
					$countdown =  "<span id='storeclosing_countdown_".rand()."' class='storeclosing_countdown' data-storeclosing='".$this->active_time['upcoming'][2]."' data-days='Day(s)' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."'></span>";
					
				}

			} else {
				if(isset($this->active_time['daily'][0])){ // Daily only
										
					$countdown =  "<span id='storeclosing_countdown_".rand()."' class='storeclosing_countdown' data-storeclosing='".$this->active_time['daily'][2]."' data-days='Day(s)' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."'></span>";
					
				}
				
				if(isset($this->active_time['upcoming'][0])){ // Upcoming only
				
					$countdown =  "<span id='storeclosing_countdown_".rand()."' class='storeclosing_countdown' data-storeclosing='".$this->active_time['upcoming'][2]."' data-days='Day(s)' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."'></span>";
					
				}

			}
			
		}
				
		return $countdown;
		
	}
	
	
	function WSC__Get_Active_Plan(){
		
		return $this->WSC__Active_Plan( array(
			'store_check' 				=> $this->store_check, 
			'active_plan' 				=> $this->active_plan, 
			'storeclosing_notification' => $this->storeclosing_notification, 
			'storeclosing_preview' 		=> $this->storeclosing_preview, 
			'active_daily' 				=> $this->active_daily,
			'active_time' 				=> $this->active_time
		));
		
	}
	
// End of Class
}
?>