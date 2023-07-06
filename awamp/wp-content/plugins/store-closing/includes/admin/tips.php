<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function WSC__Admin_Store_Closing_Tip($tutorial_id){
	
	if($tutorial_id != '') {
	
	?>
	<style>
		#wsp-tip{
			z-index: 100;
			position: absolute;
			width:1px;
			height:1px;
		}
		#wsp-tip span{
			display:none;
		}
		.wsp-tip-skip{
			position: absolute;
			right:6px;
			color:#000000;
			margin:  -18px -26px;
			border-radius:50%;
			border:  dashed 1px #00aadc;
		}
		.wsp-tip-skip:hover{
			background-color: #00aadc;
			color:#FFFFFF;
			cursor:pointer;
		}
		.wsp-tip-arrow{
			position: absolute;
			color:#000000;
			font-size: 80px;
			top: -23px;
			left: -53px;
		}
		.wsp-tip-cursor{
			display:none;
			background-color:#000000;
			color :#FFFFFF;
			border:  dashed 2px #00aadc;
			padding:10px 20px;
			min-width:140px;
			min-height:100px;
			width: fit-content;
		}
		.wsp-tip-dir{
			float: right;
		}
		.wsp-tip-dir:hover{
			background: #FFFFFF;
			color: #000000;
			cursor:pointer;
		}
		img:hover{
			cursor:pointer;
		}

	</style>
	<div id="wsp-tip" class="wsp-tip-cover">
	<div class="wsp-tip-cursor">
		<form name="WSC__Close_Tips" action="#" method="POST">
			<input type="hidden" name="Close_Tips" value="Close_Tips">
			<div id="wsp-tip-skip" title="<?php echo __( 'Close', WSC__DOMAIN ); ?>" class="wsp-tip-skip dashicons dashicons-dismiss"></div>
		</form>
		<div id="wsp-tip-arrow" class="wsp-tip-arrow dashicons dashicons-undo"></div>
		<div id="wsp-tip-next" title="<?php echo __( 'Next', WSC__DOMAIN ); ?>" class="wsp-tip-dir dashicons dashicons-arrow-right-alt2"></div>
		<div id="wsp-tip-prev" title="<?php echo __( 'Prev', WSC__DOMAIN ); ?>" class="wsp-tip-dir dashicons dashicons-arrow-left-alt2"></div>
	</div>
	
<?php 
if(isset($tutorial_id) and preg_match('#.+#',$tutorial_id) and $tutorial_id != ''){
	
switch ($tutorial_id) {
case 1:
?>
	<span data-class="[name='WSC_addproduct']" data-returnurl="close_tip">
		<strong><?php echo __( 'START HERE', WSC__DOMAIN ); ?><strong>
		<p><?php echo __( 'You can set two different opening and closing times for the days of the week. The "checkbox" next to the day must be selected in order for the selected hours will be active. "WooCommerce" store will be opened online order at the selected time. Outside these hours, the "order" and "order complete" buttons will be invisible. If you have a message in the "Notification Message" field, it will be displayed.', WSC__DOMAIN ); ?></p>
		<p><strong><em><?php echo __( 'Very important :', WSC__DOMAIN ); ?></strong></em><br>
		<strong><?php echo __( " If the daily planner is selected for just one day, the plugin will run all week.
For this reason, please fill in the working hours of the shop weekly.", WSC__DOMAIN ); ?><strong></p>
	</span>

<?php
break;
default:
break;
}
	
}
?>
	
	</div>
	</form>	
	<script type='text/javascript'>
	jQuery( document ).ready( function($) {
		var span_count = 0;
		var returnurl = ($('#wsp-tip span').attr('data-returnurl')) ? $('#wsp-tip span').attr('data-returnurl') : 'FAQ';
		
		wsp_tip();

		function wsp_tip(){
			// Tip
			var max_width = $('#wsp-tip').parent().width();	
			var tips = new Array ();

			$('#wsp-tip:parent').css('width', max_width);
		
			$('#wsp-tip span').each(function () {
			  tips.push( $(this) );
			});
		
			if(tips.length > 0) {
				
				if ( $( tips[span_count].attr('data-class') ).length > 0 ){
					cursor_move($( tips[span_count].attr('data-class') ), tips[span_count].html() , max_width);
				}else{
					window.open("?post_type=product&page=<?php echo WSC__SLUG; ?>&tab="+returnurl, "_self");	
				}
				$('#wsp-tip-next').on('click', function(){
					if(span_count < tips.length - 1) { 
						span_count++; 
						cursor_move($(tips[span_count].attr('data-class')), tips[span_count].html() , max_width);
					}else{ 
						$('#wsp-tip').hide(); 
						window.open("?post_type=product&page=<?php echo WSC__SLUG; ?>&tab="+returnurl, "_self");
					}
				});
		
				$('#wsp-tip-prev').on('click', function(){
					if(span_count > 0) { 
						span_count--; 
						cursor_move($(tips[span_count].attr('data-class')), tips[span_count].html() , max_width);
					}
				});
			
			}
	
		}

		function cursor_move(object, text, max_width){
			var wsp_tip_cursor = $( ".wsp-tip-cursor" );
			var wsp_tip_skip = $( ".wsp-tip-skip" );
			
			var offset_object = object.offset();
			var width_object = object.width();
		
			$('.wsp-tip-cursor').fadeIn();
			$('.wsp-tip-cursor').css('max-width',Math.abs( max_width-offset_object.left-100));
	
			wsp_tip_cursor.offset({ top: offset_object.top, left: offset_object.left+width_object+60 });
			$( ".wsp-tip-cursor tip" ).remove();
			wsp_tip_cursor.append('<tip>'+text+'</tip>');
	
		}
		
		$('#wsp-tip-skip').click(function(){
			$('#wsp-tip').hide();
			window.open("?post_type=product&page=<?php echo WSC__SLUG; ?>&tab="+returnurl, "_self");
		});
		
		$('img').click(function(){
			window.open($(this).attr('src'), '_blank');
		});
	
	});
	</script>

	<?php
}}
?>