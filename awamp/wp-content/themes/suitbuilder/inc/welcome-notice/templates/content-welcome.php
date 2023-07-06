<?php
/**
 * Template part for displaying the welcome notice
 *
 */
?>
<?php 
	$theme_detail = get_query_var( 'rt_welcome_notice_theme', false );
	if( !$theme_detail ){
		return;
	}
	$theme_name = $theme_detail[ 'name' ];

	$is_available = Suitbuilder_Welcome_Notice::is_plugin_installed();
	$is_activated = Suitbuilder_Welcome_Notice::is_plugin_activated();

	if( $is_available && !$is_activated ){
		$text = esc_html__( 'Clicking the button will activate it.', 'suitbuilder' );
	}else{
		$text = esc_html__( 'Clicking the button will install and activate it.', 'suitbuilder' );
	}
?>
<div class="notice notice-info is-dismissible">
    <div class="rt-welcome-welcome-notice">
		<div class="notice-content">
			<div class="notice-heading">
				<p>
					<?php printf( '%s %s',
						esc_html__( 'Thank you for installing', 'suitbuilder' ),
						$theme_name
					); ?>						
				</p>
			</div>
			<?php
				printf( '<p>%s <a href="%s" target="_blank">%s</a> %s %s</p>',
					esc_html__( 'Welcome! In order to import our', 'suitbuilder' ),
					esc_url( 'https://riseblocks.com/rt-easy-builder/starter-templates/' ),
					esc_html__( 'Starter Templates,', 'suitbuilder' ),
					esc_html__( 'we’ve assembled an importer plugin to get you started.', 'suitbuilder' ),
					$text
				);
			?>
			<div class="rt-welcome-notice-container">
				<button href="<?php echo esc_url( '#' ) ?>" class="rt-welcome-notice-started button-primary" data-status=<?php  echo $is_available && !$is_activated ? 'active' : 'install'; ?> >
					<?php
					if( $is_available && !$is_activated ){
						esc_html_e( 'Activate Plugin', 'suitbuilder' );
					}else{						
						printf( '%s %s',
							esc_html__( 'Get started with', 'suitbuilder' ),
							$theme_name
						);
					}
					?>
				</button>
			</div>
		</div>
	</div>
</div>