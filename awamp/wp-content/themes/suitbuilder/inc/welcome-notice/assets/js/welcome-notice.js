(function($){
	$( document ).on( 'click', 'button.rt-welcome-notice-started', function(e){
		e.preventDefault();

		$this = this;
		if ( confirm( RTWELCOMENOTICE.confirm_msg ) ) {
			var buttonStatus = $( this ).data( 'status' );
			jQuery.ajax({
				type: 'post',
				url: RTWELCOMENOTICE.admin_url,
				data: {
					nonce: RTWELCOMENOTICE.nonce,
					action: 'rt_welcome_ajax_action',
					status: buttonStatus
				},
				beforeSend: function(){
					$( $this ).addClass( 'loading' );
				},
				success: function( response ){
					if( response ){
						window.location.reload();
					}
				}
			});
		}
	});
})(jQuery)