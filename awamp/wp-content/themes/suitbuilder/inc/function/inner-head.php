<?php 

if (!function_exists('suitbuilder_single_page_title')) :

	/**
	* suitbuilder_inner_head_section
	*
	* @since suitbuilder 1.0.0
	*
	* @hooked null
	*/

    function suitbuilder_single_page_title() {
		global $suitbuilder_customizer_all_values;
		if( !$suitbuilder_customizer_all_values['suitbuilder-enable-inner-header'] ){
			return null;
		}

		$Latest_blog_header =  $suitbuilder_customizer_all_values['suitbuilder-blog-header-title-text'];
		$enable_recent_post = $suitbuilder_customizer_all_values[ 'suitbuilder-enable-post-inner-header' ];
		
 		if( is_home( ) && $enable_recent_post ) {
			if( $enable_recent_post ){
				$recent_posts = wp_get_recent_posts(array(
				    'numberposts' => 3,
				    'post_status' => 'publish'
				));
				if( $recent_posts ): ?>
					<div class="suitbuilder-recent-post">
						<?php foreach ( $recent_posts as $p ) :?>
							<div style="background-image: url( '<?php echo esc_url( get_the_post_thumbnail_url( $p[ 'ID' ] ) ); ?>' ), url('<?php echo esc_attr( get_template_directory_uri() . '/assets/image/default-banner.jpg' ); ?>' )" class="single-recent-post">
									<div class="suitbuilder-feature-news-content">
										<h2 class="suitbuilder-news-title">
											<a href="<?php the_permalink( $p[ 'ID' ] ); ?>"><?php echo esc_html( get_the_title( $p[ 'ID' ] ) );?></a>
										</h2>	
										<?php if( $suitbuilder_customizer_all_values['suitbuilder-inner-banner-enable-meta'] ){?>
											<div class="entry-meta">
												<?php suitbuilder_posted_on( $p[ 'ID' ] ); ?>
											</div><!-- .entry-meta -->
										<?php }
										$excerpt_length = $suitbuilder_customizer_all_values['suitbuilder-latest-blog-excerpt-lenght'];
										if( $suitbuilder_customizer_all_values['suitbuilder-inner-banner-enable-content'] ){?>
											<p><?php echo suitbuilder_excerpt( $excerpt_length, false, '...', $p[ 'ID' ] ); ?></p>
										<?php } ?>
									</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif;
			}
		}else{
			if( is_home( ) && empty( $Latest_blog_header ) ){
				return;
			}else{ ?>
				<div class="wrapper page-inner-title">
					<div class="container position-relative z-index ">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<header class="entry-header">
									<?php if (is_singular()){ ?>
									<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
									<?php } 
									elseif (is_404()) { ?>
										<h2 class="entry-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'suitbuilder' ); ?></h2>
									<?php }
									elseif (is_archive()) {
										the_archive_title( '<h2 class="entry-title">', '</h2>' );
										the_archive_description( '<div class="taxonomy-description">', '</div>' );
									}
									elseif (is_search()) { ?>
									<?php /* translators: %s: search page result */ ?>
										<h2 class="entry-title"><?php printf( esc_html__( 'Search Results for: %s', 'suitbuilder' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
									<?php }
									else{ ?>
										 
		 								<h2 class="entry-title"><?php echo esc_html( $Latest_blog_header ); ?></h2>
								<?php }
									?>
								</header><!-- .entry-header -->
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php }
	}
endif;
add_action( 'suitbuilder_page_inner_title', 'suitbuilder_single_page_title', 15 );

