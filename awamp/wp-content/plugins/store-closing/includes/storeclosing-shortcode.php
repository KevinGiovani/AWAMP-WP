<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WSC__Short_Code extends WSC__StoreClosing{			
	
	public function __construct() {
	
		$this->WSC__Options_Data = parent::WSC__Options_Data();
				
		add_shortcode( 'StoreClosing', array( $this, 'storeclosing_shortcode' ) );
			
	}
	
	public function storeclosing_shortcode($atts){
		
		$atts = (is_array($atts)) ? $atts : '';
		$now_day =  date('w', current_time( 'timestamp', 0 )) - 1;
		$row_count = 0;
		$shortcode_content = '';
		
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
		//	
		
		if($upcoming_plans > 0 && is_array($atts) && esc_html( $atts[0] ) == 'Opening'){

			foreach ($this->WSC__Options_Data['upcoming'] as $upcoming_plan => $upcoming_plans){

				if(isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][0]) && $this->WSC__Options_Data['upcoming'][$upcoming_plan][5] >= date('Y-m-d', current_time( 'timestamp', 0 )) && $this->WSC__Options_Data['upcoming'][$upcoming_plan][8] != '' && empty($this->WSC__Options_Data['manual'])){ // Upcoming on
				
					$opening_time = str_replace ('[postamp]', '<storeclosing_upcoming_date>'.date('d.m.Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]) ).'</storeclosing_upcoming_date> <storeclosing_upcoming_hour>'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][6].':'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][7].'</storeclosing_upcoming_hour>', $this->WSC__Options_Data['upcoming'][$upcoming_plan][8]);
					$opening_time = str_replace ('[countdown]', "<span id='storeclosing_shortcode_countdown_opening' class='storeclosing_countdown' data-storeclosing='".date('M j, Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][5]) )." ".$this->WSC__Options_Data['upcoming'][$upcoming_plan][6].":".$this->WSC__Options_Data['upcoming'][$upcoming_plan][7].":00' data-days='".__('Day(s)', 'store-closing' )."' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."' ></span>", $opening_time);
	
					if($this->WSC__Options_Data['upcoming'][$upcoming_plan][5] >= date('Y-m-d', current_time( 'timestamp', 0 ) )){
						$shortcode_content .= "<div class='storeclosing_upcoming_open'>
						<div class='storeclosing_inframe'>".$opening_time."</div></div>";
					}
				break;
				}
			}
		}else if($upcoming_plans > 0 && is_array($atts) && esc_html( $atts[0] ) == 'Closing'){
		
			foreach ($this->WSC__Options_Data['upcoming'] as $upcoming_plan => $upcoming_plans){
			
				if(isset($this->WSC__Options_Data['upcoming'][$upcoming_plan][0]) && $this->WSC__Options_Data['upcoming'][$upcoming_plan][1] >= date('Y-m-d', current_time( 'timestamp', 0 )) && $this->WSC__Options_Data['upcoming'][$upcoming_plan][4] != '' && empty($this->WSC__Options_Data['manual'])){ // Upcoming on
					$closing_time = str_replace ('[pcstamp]', '<storeclosing_upcoming_date>'.date('d.m.Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]) ).'</storeclosing_upcoming_date> <storeclosing_upcoming_hour>'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][2].':'.$this->WSC__Options_Data['upcoming'][$upcoming_plan][3].'</storeclosing_upcoming_hour>', $this->WSC__Options_Data['upcoming'][$upcoming_plan][4]);
					
					$closing_time = str_replace ('[countdown]', "<span id='storeclosing_shortcode_countdown_closing' class='storeclosing_countdown' data-storeclosing='".date('M j, Y', strtotime($this->WSC__Options_Data['upcoming'][$upcoming_plan][1]) )." ".$this->WSC__Options_Data['upcoming'][$upcoming_plan][2].":".$this->WSC__Options_Data['upcoming'][$upcoming_plan][3].":00' data-days='".__('Day(s)', 'store-closing' )."' data-wpnow='".date('M j, Y H:i', current_time( 'timestamp', 0 ))."' ></span>", $closing_time);
	
					if($this->WSC__Options_Data['upcoming'][$upcoming_plan][1] >= date('Y-m-d', current_time( 'timestamp', 0 ) )){
						$shortcode_content .= "<div class='storeclosing_upcoming_close'>
						<div class='storeclosing_inframe'>
						".$closing_time."
						</div></div>";
					}
				break;
				}
			}
		}else if($upcoming_plans > 0 && is_array($atts) && esc_html( $atts[0] ) == 'Upcoming'){
			
			$shortcode_content = "		
			<div class='storeclosing_upcoming_table storeclosing_table'>
			<table width='90%' cellpadding='0' cellspacing='0' >
				<tbody>
					<tr>
						<th class='storeclosing_upcoming_table_title_closing' align='center' colspan='2'>". __('Closing', 'store-closing' )."</th>
						<th class='storeclosing_upcoming_table_title_opening' align='center' colspan='2'>". __('Opening', 'store-closing' )."</th>
					</tr>
					<tr>
						<td class='storeclosing_upcoming_table_title_closingdate' align='center' >". __('Date', 'store-closing' )."</td>
						<td class='storeclosing_upcoming_table_title_closingtime' align='center' >". __('Time', 'store-closing' )."</td>
						<td class='storeclosing_upcoming_table_title_openingdate' align='center' >". __('Date', 'store-closing' )."</td>
						<td class='storeclosing_upcoming_table_title_openingtime' align='center' >". __('Time', 'store-closing' )."</td>
					</tr>
			";
		
			if($upcoming_plans > 0) {
				$this->WSC__Options_Data['upcoming'] = array_values($this->WSC__Options_Data['upcoming']);
				usort($this->WSC__Options_Data['upcoming'],function($a,$b){
					
					$result = strcmp($a[1],$b[1]);
					$result .= strcmp($a[2],$b[2]);
					$result .= strcmp($a[3],$b[3]);
					
					return $result;
				});
			}
			
			foreach($this->WSC__Options_Data['upcoming'] as $plan_key => $upcoming_plans){
			if(isset($upcoming_plans[0])){
			$row_count++;
			if($row_count%2==1){ $row="storeclosing_table_row_dark"; } else{ $row="storeclosing_table_row_light";}
			$shortcode_content .= "
					<tr>
						<td class='storeclosing_upcoming_table_closingdate ".$row."' align='center'>".date('d F Y', strtotime($upcoming_plans[1]) )."</td>
						<td class='storeclosing_upcoming_table_closingtime ".$row."' align='center'>".$upcoming_plans[2].":".$upcoming_plans[3]."</td>
						<td class='storeclosing_upcoming_table_openingdate ".$row."' align='center'>".date('d F Y', strtotime($upcoming_plans[5]) )."</td>
						<td class='storeclosing_upcoming_table_openingtime ".$row."' align='center'>".$upcoming_plans[6].":".$upcoming_plans[7]."</td>
					</tr>
			";
			}}
			$shortcode_content .= "
				</tbody>
			</table>
			</div>           
			";
		}else if(!empty($atts[0]) && is_array($atts) && esc_html( $atts[0] ) == 'Daily'){	
		
			$shortcode_content = "		
			<div class='storeclosing_daily'>
			<table class='storeclosing_table' width='90%' cellpadding='0' cellspacing='0'>
					<tbody>
					<tr>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Closing', 'store-closing' )."</th>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Opening', 'store-closing' )."</th>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Closing', 'store-closing' )."</th>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Opening', 'store-closing' )."</th>
					</tr>
			";
					
					foreach($this->WSC__Options_Data['daily'] as $day_name=>$times){
						if (isset($this->WSC__Options_Data['daily'][$day_name][0])) {
							$storeclosing_active_day = ($day_name == $now_day)?'storeclosing_active_day':'';
							$row_count++;
							if($row_count%2==1){ $row="storeclosing_table_row_dark"; } else{ $row="storeclosing_table_row_light";}
			$shortcode_content .= "
						<tr class='". $storeclosing_active_day."'>
							<td align='right' class='storeclosing_daily_table_row ".$row."' >". $this->WSC__Options_Data['daysweek'][$day_name]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[3].":". $times[4]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >-</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[1].":". $times[2]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >&nbsp;</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[9].":". $times[10]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >-</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[7].":". $times[8]."</td>
						</tr>
			";
					}}
			$shortcode_content .= "
				</tbody></table>
			</div>           
			";				
			
		}else if(!empty($atts[0]) && is_array($atts) && esc_html( $atts[0] ) == 'DailyFirst'){		
			
			$shortcode_content = "		
			<div class='storeclosing_daily'>
			<table class='storeclosing_table' width='90%' cellpadding='0' cellspacing='0'>
					<tbody>
					<tr>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Closing', 'store-closing' )."</th>
						<th class='storeclosing_daily_table_title' align='center' >-</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Opening', 'store-closing' )."</th>
					</tr>
			";
					
					foreach($this->WSC__Options_Data['daily'] as $day_name=>$times){
						if (isset($this->WSC__Options_Data['daily'][$day_name][0])) {
							$storeclosing_active_day = ($day_name == $now_day)?'storeclosing_active_day':'';
							$row_count++;
							if($row_count%2==1){ $row="storeclosing_table_row_dark"; } else{ $row="storeclosing_table_row_light";}
			$shortcode_content .= "
						<tr class='". $storeclosing_active_day."'>
							<td align='right' class='storeclosing_daily_table_row ".$row."' >". $this->WSC__Options_Data['daysweek'][$day_name]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[3].":". $times[4]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >-</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[1].":". $times[2]."</td>
						</tr>
			";
					}}
					
			$shortcode_content .= "
				</tbody></table>
			</div>           
			";		
					
			
		}else if(!empty($atts[0]) && is_array($atts) && esc_html( $atts[0] ) == 'DailySecond'){		
			
			$shortcode_content = "		
			<div class='storeclosing_daily'>
			<table class='storeclosing_table' width='90%' cellpadding='0' cellspacing='0'>
					<tbody>
					<tr>
						<th class='storeclosing_daily_table_title' align='center' >&nbsp;</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Closing', 'store-closing' )."</th>
						<th class='storeclosing_daily_table_title' align='center' >-</th>
						<th class='storeclosing_daily_table_title' align='center' >". __('Opening', 'store-closing' )."</th>
					</tr>
			";
					
					foreach($this->WSC__Options_Data['daily'] as $day_name=>$times){
						if (isset($this->WSC__Options_Data['daily'][$day_name][6])) {
							$storeclosing_active_day = ($day_name == $now_day)?'storeclosing_active_day':'';
							$row_count++;
							if($row_count%2==1){ $row="storeclosing_table_row_dark"; } else{ $row="storeclosing_table_row_light";}
			$shortcode_content .= "
						<tr class='". $storeclosing_active_day."'>
							<td align='right' class='storeclosing_daily_table_row ".$row."' >". $this->WSC__Options_Data['daysweek'][$day_name]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[9].":". $times[10]."</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >-</td>
							<td align='center' class='storeclosing_daily_table_row ".$row."' >". $times[7].":". $times[8]."</td>
						</tr>
			";
					}}
			$shortcode_content .= "
				</tbody></table>
			</div>           
			";	
					
			
		}else{

			$shortcode_content =  $this->WSC__Show('ShortCode');
		
		}

		return $shortcode_content;
	}
	
// End of Class
}
?>