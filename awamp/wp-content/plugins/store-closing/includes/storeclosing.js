jQuery(document).ready(function($) {
	
	if ( $( '.storeclosing_countdown' ).length ) {
		
		$( '.storeclosing_countdown' ).each(function() {
			
			var countDownDate = new Date( $( this ).data( 'storeclosing' ) ).getTime();
			var wpnow = new Date( $( this ).data( 'wpnow' ) );
			var id = $(this).attr('id');
			var day_name = $( this ).data( 'days' );
			
			var countDown = setInterval(function() {
								
				var now = wpnow.setSeconds(wpnow.getSeconds() + 1);
												
				if(countDownDate > now) {
					var distance = countDownDate - now;
					
					var days = Math.floor(distance / (1000 * 60 * 60 * 24));
					var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);
					
					if (seconds < 10) {seconds = '0' + seconds;}
					if (minutes < 10) {minutes = '0' + minutes;}
					if (hours < 10) {hours = '0' + hours;}

					$( '#'+id ).html ((days !== 0 ? days+  ' ' + day_name : '') +' ' + hours + ':'+minutes+':'+seconds+' ');
					
					if (Math.floor(days+hours+minutes+seconds) <= 1) {
						$( '#'+id ).hide();
						$( '#storeclosing_popup_main').hide();
						$('#WSC__storeclosing_disable-css, #storeclosing_show').remove();
						clearInterval(countDown);
					}
					
				}
				
				
			}, 1000);
		  
		});
		
	}
	
	
	// Popup
	var storeclosing_cookie = read_cookie('storeclosing');
	
	if (!storeclosing_cookie || storeclosing_cookie === 'Invalid Date') {
		
		$( '#storeclosing_popup_main').show();
		create_cookie('storeclosing');
		
	}else{
		
		var date = new Date().toGMTString();
		storeclosing_cookie = new Date(storeclosing_cookie).toGMTString();
	
		if(storeclosing_cookie < date) {
			$( '#storeclosing_popup_main').show();
			create_cookie('storeclosing');
		}else{
			if($('#storeclosing_popup_cookieexpries').val() == 'pasive'){
				document.cookie = 'storeclosing=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
			} 
			$( '#storeclosing_popup_main').hide();
		}
			
	}

	if ( $( '.storeclosing_popup_countdown' ).length ) {
		
		$( '.storeclosing_popup_countdown' ).each(function() {
			
			var countDownDate = new Date( $( this ).data( 'storeclosing_popup' ) ).getTime();
			var wpnow = new Date( $( this ).data( 'wpnow' ) );
			var id = $(this).attr('id');
						
			var countDown_popup = setInterval(function() {
								
				var now = wpnow.setSeconds(wpnow.getSeconds() + 1);

				if(countDownDate > now) {
					var distance = countDownDate - now;
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);
					
					$( '#'+id ).html (seconds+' ');

					if (seconds <= 1) {
						$( '#storeclosing_popup_main').hide();
					}
					
					if (distance < 0) {
						clearInterval(countDown_popup);
						$( '#'+id ).innerHTML = '';
					}
					
				}
				
			}, 1000);
			
			$('.remindme_icon, #remindme_checkbox').click(function () {	clearInterval(countDown_popup); } );
		  
		});
		
	}
	
	$( '#storeclosing_popup_close' ).click(function() {
	  $( '#storeclosing_popup_main').hide();
	});
	
	function create_cookie(cookie_name) {
		var expires = '';
		var date = new Date();
		
		if($('#storeclosing_popup_cookieexpries').val() == 'pasive'){
			document.cookie = 'storeclosing=;expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
		} else {
			var storeclosing_popup_cookieexpries = $('#storeclosing_popup_cookieexpries').val() * 1;
			date.setTime(date.getTime() + ( storeclosing_popup_cookieexpries ) );
			expires = '; expires=' + date.toUTCString();
			document.cookie = cookie_name + '=' + date.toUTCString() + expires + '; path=/';
		}
		
	}
	
	function read_cookie(cookie_name) {
		var name = cookie_name + '=';
		var cookie_value = document.cookie.split(';');
		for(var i=0;i < cookie_value.length;i++) {
			var content = cookie_value[i];
			while (content.charAt(0) === ' ') {content = content.substring(1,content.length);}
			if (content.indexOf(name) === 0) {return content.substring(name.length,content.length);}
		}
		return null;
	}

});