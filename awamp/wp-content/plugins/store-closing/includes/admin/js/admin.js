jQuery(document).ready(function($) {

	// Tooltip
	$( document ).tooltip({
      track: true,
      items: "[data-infoimg], [title]",
      content: function() {
        var element = $( this );
        if ( element.is( "[data-infoimg]" ) ) {
          var position = $( this ).position();
          var height = position.top - 20;
		  console.log( "left: " + position.left + ", top: " + position.top ); 
		  
          return "<img style='max-height:"+height+"px' src='"+$(this).attr('data-infoimg')+"'>";
        }
        if ( element.is( "[title]" ) ) {
          return element.attr( "title" );
        }
      }
    });
	
	// Daily
	$(".daily_table input[type='number'], .upcoming_table input[type='number'], .exclude_table input[type='number']").change(function() {
		var value = $(this).val();
    	if ( value.length == 1 ) $(this).val('0'+value);
	});
	
	$(".daily input[type='checkbox']").change(function() {
		if(this.checked) {
			$('.'+this.id).removeClass('disable');
		}else{
			$('.'+this.id).addClass('disable');
		}
	});
	
	$(".daily input[type='number']").change(function() {
		var day = $(this).data("day");
		var section = $(this).data("section");
		var closing_hour = $('#'+section+'_closing_hour_'+day).val();
		var closing_minute = $('#'+section+'_closing_minute_'+day).val();
		var closing = closing_hour+closing_minute;
		var opening_hour = $('#'+section+'_opening_hour_'+day).val();
		var opening_minute = $('#'+section+'_opening_minute_'+day).val();
		var opening = opening_hour+opening_minute;
		
		if(opening > closing ) {
			$('#'+section+'_closing_hour_'+day).val(('0' + (opening_hour)).slice(-2));
			$('#'+section+'_closing_minute_'+day).val(('0' + (opening_minute)).slice(-2));
		}
	
	});
	
	
	//Upcoming
	$(".upcoming_table").each(function(){
		var id = $(this).data("id");
		generate_business_plan(id);
	});
	
	$(".upcoming_table input[type='checkbox']").change(function() {
		var id = $(this).data("id");
		
		if( !$('.tr_upcoming_wide_'+id).is(":visible") ) $('.tr_upcoming_wide_'+id).toggle();
		
		if(this.checked) {
			$('#upcoming_plan_'+id).removeClass('disable');
		}else{
			$('#upcoming_plan_'+id).addClass('disable');
		}
	});
	
	$(".upcoming_titleinfo").click(function() {
		var id = $(this).data("id");
		$('.tr_upcoming_wide_'+id).toggle();
	});
	
	$(".show_datearea .dashicons").click(function() {
		var id = $(this).data("id");
		if ( $('#show_daily_message_'+id).val() == 'hidden') {
			$('#show_daily_message_'+id).val('visibility');
			$(this).removeClass('dashicons-hidden');
			$(this).addClass('dashicons-visibility');
		} else {
			$('#show_daily_message_'+id).val('hidden');
			$(this).removeClass('dashicons-visibility');
			$(this).addClass('dashicons-hidden');
		}
	});
	
	$("#upcoming_temp").change(function() {
		if(this.checked) {
			$("input[name*='temp_storeclosing_upcoming']").each(function(){
				var name = this.name;
				var new_name = name.replace("temp_", "");
				$(this).attr('name', new_name);
			});
			$('#upcoming_plan_temp').removeClass('disable');
		}else{
			$("#upcoming_plan_temp :input").each(function(){
				var name = this.name;
				$(this).attr('name', 'temp_'+name);
			});
			$('#upcoming_plan_temp').addClass('disable');
		}
		
	});
	
	$(".upcoming_table :input").change(function() {
		var id = $(this).data("id");
		generate_business_plan(id);
	});
	
	$(".upcoming_delete").click(function() {
		var id = this.id;
		$("#upcoming_plan_"+id).remove();
	});
	
	function generate_business_plan(id){
		
		var min_width = 170;
		var business_plan = $("#business_plan_"+id);
		var storeclosing_daystart = $("#business_plan_"+id+" #storeclosing_daystart");
		var storeclosing_storeopened = $("#business_plan_"+id+" #storeclosing_storeopened");
		var storeclosing_storeclosed = $("#business_plan_"+id+" #storeclosing_storeclosed");
		var closing_date = $("#closing_date_"+id);
		var closing_hour = $("#closing_hour_"+id);
		var closing_minute = $("#closing_minute_"+id);
		var opening_date = $("#opening_date_"+id);
		var opening_hour = $("#opening_hour_"+id);
		var opening_minute = $("#opening_minute_"+id);
		var show_date = $("#show_date_"+id);
		var wpnow_date = $("#storeclosing_daystart").data( "wpnow_date" );
		var wpnow_hour = $("#storeclosing_daystart").data( "wpnow_hour" );
		var wpnow_minute = $("#storeclosing_daystart").data( "wpnow_minute" );
				
		var wpnow = wpnow_date+"-"+wpnow_hour+"-"+wpnow_minute+"-00 ";
		var wpnowArr=wpnow.split("-");
		
		var wpnow_DATE=new Date( parseInt(wpnowArr[0], 10),
								   parseInt(wpnowArr[1], 10)-1,
								   parseInt(wpnowArr[2], 10),
								   parseInt(wpnowArr[3], 10),
								   parseInt(wpnowArr[4], 10),
								   parseInt(wpnowArr[5], 10));		
		
		var closing = closing_date.val()+"-"+closing_hour.val()+"-"+closing_minute.val()+"-00 ";
		var closingArr=closing.split("-");
		
		var closing_DATE=new Date( parseInt(closingArr[0], 10),
								   parseInt(closingArr[1], 10)-1,
								   parseInt(closingArr[2], 10),
								   parseInt(closingArr[3], 10),
								   parseInt(closingArr[4], 10),
								   parseInt(closingArr[5], 10));
		
		var opening = opening_date.val()+"-"+opening_hour.val()+"-"+opening_minute.val()+"-00 ";
		var openingArr=opening.split("-");
		
		var opening_DATE=new Date( parseInt(openingArr[0], 10),
								   parseInt(openingArr[1], 10)-1,
								   parseInt(openingArr[2], 10),
								   parseInt(openingArr[3], 10),
								   parseInt(openingArr[4], 10),
								   parseInt(openingArr[5], 10));
								   
		var show = show_date.val()+"-00-00-00 ";
		var showArr=show.split("-");
		
		var show_DATE=new Date( parseInt(showArr[0], 10),
								   parseInt(showArr[1], 10)-1,
								   parseInt(showArr[2], 10),
								   parseInt(showArr[3], 10),
								   parseInt(showArr[4], 10),
								   parseInt(showArr[5], 10));
				
		if(opening_DATE < wpnow_DATE ) {	
			opening_date.val(wpnow_date);
			opening_hour.val(wpnow_hour);
			opening_minute.val(wpnow_minute);
			opening_DATE = wpnow_DATE;
		}
		
		if(closing_DATE > opening_DATE ) {		
			opening_date.val(closing_date.val());
			opening_hour.val(('0' + (closing_hour.val())).slice(-2));
			opening_minute.val(('0' + (closing_minute.val())).slice(-2));
			opening_DATE = closing_DATE;
		}
		if(show_DATE > closing_DATE ) {		
			show_date.val(closing_date.val());
		}
		
		now_seconds = Math.round(wpnow_DATE / (60*1000));
		closing_seconds = Math.round(closing_DATE / (60*1000));
		opening_seconds = Math.round(opening_DATE / (60*1000));

		total_seconds = Math.round(opening_seconds - now_seconds);				
		open_seconds = Math.round(closing_seconds - now_seconds);
		close_seconds = Math.round(opening_seconds - closing_seconds);
		
		business_plan_width = business_plan.width();
		daystart_width = storeclosing_daystart.width();	
		storeopened_width = storeclosing_storeopened.width();
		storeclosed_width = storeclosing_storeclosed.width();
		
		line = Math.round(total_seconds / 100);
		line_px = Math.round((business_plan_width - daystart_width) / 100);
				
		open_lines = Math.round((line_px * (open_seconds / line)));
		close_lines = Math.round((line_px * (close_seconds / line)));
		
		if(open_lines < min_width) { close_lines = close_lines - (min_width - open_lines); open_lines = min_width; }
		if(close_lines < min_width) { open_lines = open_lines - (min_width - close_lines); close_lines = min_width; }
		if(business_plan_width < (open_lines + close_lines + min_width)) { 
			(close_lines > open_lines ) ?
				close_lines = close_lines - ((open_lines + close_lines + min_width) - business_plan_width)
				:
				open_lines = open_lines - ((open_lines + close_lines + min_width) - business_plan_width); 
		}
		
		closing_info = closing_date.val() + ' '
					 + ('0' + closing_hour.val()).slice(-2) + ':'
					 + ('0' + closing_minute.val()).slice(-2);
					 
		opening_info = opening_date.val() + ' '
					 + ('0' + opening_hour.val()).slice(-2) + ':'
					 + ('0' + opening_minute.val()).slice(-2);
				
		$("#business_plan_"+id+" #storeclosing_storeopened .date_stamp").html(closing_info);	
		$("#business_plan_"+id+" #storeclosing_storeclosed .date_stamp").html(opening_info);	
		
		$("#business_plan_"+id+" #storeclosing_storeclosed").css('width', close_lines + 'px');
		$("#business_plan_"+id+" #storeclosing_storeopened").css('width', open_lines + 'px');
		
	}
	
	
	//Manual
	$("#storeclosing_manuel").change(function() {
		$("#storeclosing_manuel_message").hide();
		$("#storeclosing_manuel_loading").show();
	});
	
	$("#storeclosing_manuel").click(function() {
		$("#submit").trigger("click");
	});
	
	$('.storeclosing_manuel').removeClass('disable');
	
	if($('.storeclosing_manuel').is(":visible")){
		$("#submit").hide();
	}else{
		$("#submit").show();
	}
	
	// Notification
	$(".notification input[type='checkbox']").change(function() {
		if(this.checked) {
			$('.'+this.id).removeClass('disable');
		}else{
			$('.'+this.id).addClass('disable');
		}
	});
	
	//Setting
	// ColorPicker
	$( function() {
		function HEXfromRGB(r, g, b) {
			var hex = [
			r.toString( 16 ),
			g.toString( 16 ),
			b.toString( 16 )
			];
			$.each( hex, function( nr, val ) {
				if ( val.length === 1 ) {
				  hex[ nr ] = "0" + val;
				}
			});
			return hex.join( "" ).toUpperCase();
		}
		function RGBfromHEX(hex) {
		
			var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
			hex = hex.replace(shorthandRegex, function(m, r, g, b) {
				return r + r + g + g + b + b;
			});

			return result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
			
		}
		function refreshSwatch() {
			var red = $( "#storeclosing_colorred" ).slider( "value" ),
			green = $( "#storeclosing_colorgreen" ).slider( "value" ),
			blue = $( "#storeclosing_colorblue" ).slider( "value" ),
			hex = HEXfromRGB( red, green, blue );
			
			$( "#theme_color" ).val( "#" + hex );
			$( ".storeclosing_custom" ).css( "background-color", "#" + hex );
		}

		$( "#storeclosing_colorred, #storeclosing_colorgreen, #storeclosing_colorblue" ).slider({
			orientation: "horizontal",
			range: "min",
			max: 255,
			value: 127,
			slide: refreshSwatch,
			change: refreshSwatch
		});
		
		var theme_color = ( $( "#theme_color" ).val() != '' && $( "#theme_color" ).val())
			? $( "#theme_color" ).val()
			: '#9E6095'  ;
		
		RGB = RGBfromHEX(theme_color);
		$( "#storeclosing_colorred" ).slider( "value", parseInt(RGB[1], 16) );
		$( "#storeclosing_colorgreen" ).slider( "value", parseInt(RGB[2], 16) );
		$( "#storeclosing_colorblue" ).slider( "value", parseInt(RGB[3], 16) );

	});
	
	$("#notification_setting").change(function() {
		var value = this.value;
				
		if (value == 'custom'){
			$('.custom_settings').show();
		}else{
			$('.custom_settings').hide();
		}
		$("#storeclosing_preview").hide();
		$("#storeclosing_setting_loading").show();
		$("#submit").trigger("click");
	});
	$(".custom_settings").change(function() {
		var id = this.id;
		var value = this.value;

		if (id == 'width' || id == 'padding' || id == 'font-size' || id == 'border-width' || id == 'border-radius' || id == 'margin') {
			if(isNumber(value) == true) value = value + 'px';
			$('#'+id).val(value);
		}
		
		if (id == 'icon') {
			$('#storeclosing_icon').removeClass();
			
			switch(value) {
				case 'info':
					icon = 'dashicons-warning';
					break;
				case 'warning':
					icon = 'dashicons-dismiss';
					break;
				case 'clock':
					icon = 'dashicons-clock';
					break;
				case 'key':
					icon = 'dashicons-admin-network';
					break;
				case 'speech':
					icon = 'dashicons-format-status';
					break;
				case 'lock':
					icon = 'dashicons-lock';
					break;
				case 'unlock':
					icon = 'dashicons-unlock';
					break;
				case 'megaphone':
					icon = 'dashicons-megaphone';
					break;
				case 'cart':
					icon = 'dashicons-cart';
					break;
				default:
					icon = '';
			}
			$('#storeclosing_icon').addClass('dashicons-before '+icon);
			
		}else{
			$('.storeclosing_show').css(id,value);
		}
	});
	
	$("#backup, #restore, #cleardb").click(function() {
		var id = $(this).attr("id");
		jQuery.ajax ({
			url			: Ajax_Url.url,
			method		: 'POST',
			data		: { "WSC__"  : id, 'action' : 'wsc__admin_backup' },
			beforeSend :function() {
				$("#backup").css('display','none');
				$("#storeclosing_backup_loading").css('display','block');
			},
			success	:function(result) {
				
				if(id == 'restore' || id == 'cleardb') { 
					location.reload(); 
				} else {
					$("#storeclosing_backup_loading").css('display','none');
					$("#backup").css('display','block');
					$("#storeclosing_backup_date").text(result);
				}
			}
		});
		
	});
	
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	// Popup
	$("#popup_act").change(function() {
		$('#storeclosing_popup_loading').show();
		if(this.checked) {
			$('.popup_table').removeClass('disable');
			$('#popup_preview').show();
		}else{
			$('.popup_table').addClass('disable');
			$('#popup_preview').hide();
		}
		$("#submit").trigger("click");
	});
	
	$("#popup_position").change(function() {
		if(this.value === 'top') {
			$('#popup_message').css('top','0').css('bottom','auto');
		}else{
			$('#popup_message').css('bottom','0').css('top','auto');
		}
	});
	
	$("#popup_background").change(function() {
		$('#popup_message').css('background-color',this.value);
	});
	
	$("#popup_transparent").change(function() {
		if(this.value == 100) $('#popup_message').css('opacity','1').css('filter','alpha(opacity=100)');
		else $('#popup_message').css('opacity','.'+this.value).css('filter','alpha(opacity='+this.value+')');
	});
	
	$("#storeclosing_exclude_page").change(function() {
		$("#submit").trigger("click");
	});
	
	$(".storeclosing_exludepage_button").click(function() {
		if ($(this).hasClass('storeclosing_exludepage')) {
			$(this).removeClass('storeclosing_exludepage').addClass('storeclosing_exludepage_selected');
			
			var storeclosing_exludepage = $("#storeclosing_exludepage").val();
			
			if(storeclosing_exludepage == '') {
				$("#storeclosing_exludepage").val($(this).attr('id'));
			}else{
				$("#storeclosing_exludepage").val(storeclosing_exludepage +','+ $(this).attr('id'));
			}
			
			
		}else{
			$(this).removeClass('storeclosing_exludepage_selected').addClass('storeclosing_exludepage');
			
			var storeclosing_exludepage = $("#storeclosing_exludepage").val().replace($(this).attr('id'),'');
			$("#storeclosing_exludepage").val(storeclosing_exludepage);
			
			$("#storeclosing_exludepage").val('');
			$('.storeclosing_exludepage_selected').each(function (){
			
				var storeclosing_exludepage = $("#storeclosing_exludepage").val();
			
				if(storeclosing_exludepage == '') {
					$("#storeclosing_exludepage").val($(this).attr('id'));
				}else{
					$("#storeclosing_exludepage").val(storeclosing_exludepage +','+ $(this).attr('id'));
				}
				
			});
			
		}
	});
	
	$("#popup_cookie").change(function() {
		if($(this).val() == 'active') {
			$('.popup_cookie').removeClass('disable');
		}else{
			$('.popup_cookie').addClass('disable');
		}
		$("#submit").trigger("click");
	});
	
	// Exclude
	$("#exclude_act").change(function() {
		$('#storeclosing_exclude_loading').show();
		if(this.checked) {
			$('.exclude_table').removeClass('disable');
			$('#exclude_preview').show();
		}else{
			$('.exclude_table').addClass('disable');
			$('#exclude_preview').hide();
		}
		$("#submit").trigger("click");
	});
	
	//Dismiss
	if($('.dismiss').is(":visible")){
		$("#submit").hide();
	}else{
		$("#submit").show();
	}
	
});