<?php
/**
 * Class: Jet_Woo_Builder_Document_Base
 * Name: Document Base
 * Slug: jet-woo-builder-archive-document
 */

use Elementor\Controls_Manager;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Document_Base extends Elementor\Core\Base\Document {

	public $first_product  = null;
	public $first_category = null;

	public function get_name() {
		return 'jet-woo-builder-archive-document';
	}

	public static function get_properties() {

		$properties = parent::get_properties();

		$properties['admin_tab_group'] = '';
		$properties['support_kit']     = true;

		return $properties;

	}

	protected function _register_controls() {

		parent::_register_controls();

		$properties      = $this::get_properties();
		$enable_settings = array_key_exists( 'woo_builder_template_settings', $properties ) ? $properties['woo_builder_template_settings'] : false;

		if ( ! $enable_settings ) {
			return;
		}

		$this->start_injection( [
			'of'       => 'post_status',
			'fallback' => [
				'of' => 'post_title',
			],
		] );

		$this->add_control(
			'hide_template_title',
			[
				'label' => __( 'Hide Title', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Not working? You can set a different selector for the title in Site Settings > Layout', 'elementor' ),
				'selectors' => [
					':root' => '--page-title-display: none',
				],
			]
		);

		$this->add_control(
			'template_layout',
			[
				'label'   => __( 'Template Layout', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''                                   => __( 'Default', 'jet-woo-builder' ),
					PageTemplatesModule::TEMPLATE_CANVAS        => __( 'Elementor Canvas', 'jet-woo-builder' ),
					PageTemplatesModule::TEMPLATE_HEADER_FOOTER => __( 'Elementor Full Width', 'jet-woo-builder' ),
				],
			]
		);

		$this->add_control(
			'default_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Default Page Template from your theme', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => '',
				],
			]
		);

		$this->add_control(
			'canvas_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'No header, no footer, just template content', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => PageTemplatesModule::TEMPLATE_CANVAS,
				],
			]
		);

		$this->add_control(
			'header_footer_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This template includes the header, full-width content and footer', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => PageTemplatesModule::TEMPLATE_HEADER_FOOTER,
				],
			]
		);

		$this->end_injection();

	}

	/**
	 * Query for first product ID.
	 *
	 * @return int|bool
	 */
	public function query_first_product() {

		if ( null !== $this->first_product ) {
			return $this->first_product;
		}

		$args = [
			'post_type'      => 'product',
			'post_status'    => [ 'publish', 'pending', 'draft', 'future' ],
			'posts_per_page' => 1,
		];

		$sample_product = get_post_meta( $this->get_main_id(), '_sample_product', true );

		if ( $sample_product ) {
			$args['p'] = $sample_product;
		}

		$wp_query = new WP_Query( $args );

		if ( ! $wp_query->have_posts() ) {
			return false;
		}

		$post = $wp_query->posts;

		return $this->first_product = $post[0]->ID;

	}

	/**
	 * Query for first product ID.
	 *
	 * @return int|bool
	 */
	public function query_first_category() {

		if ( null !== $this->first_category ) {
			return $this->first_category;
		}

		$product_categories = get_categories(
			[
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'pad_counts'   => false,
				'hierarchical' => 1,
				'hide_empty'   => false,
			]
		);


		if ( ! empty( $product_categories ) ) {
			$product_category = $product_categories[0];
		}

		return $this->first_category = $product_category->term_id;

	}

	/**
	 * Save meta for current post
	 *
	 * @param $post_id
	 */
	public function save_template_item_to_meta( $post_id ) {

		$content = Elementor\Plugin::instance()->frontend->get_builder_content( $post_id, false );
		$content = preg_replace( '/<style>.*?<\/style>/', '', $content );

		update_post_meta( $post_id, '_jet_woo_builder_content', $content );

	}

	public function enqueue_custom_fonts_epro( $post_css ) {
		if ( class_exists( 'ElementorPro\Modules\AssetsManager\AssetTypes\Fonts\Custom_Fonts' ) ) {
			$custom_fonts_manager = new ElementorPro\Modules\AssetsManager\AssetTypes\Fonts\Custom_Fonts();
			$fonts                = $custom_fonts_manager->get_fonts();

			if ( ! empty( $fonts ) ) {
				foreach ( $fonts as $font => $font_data ) {
					$custom_fonts_manager->enqueue_font( $font, $font_data, $post_css );
				}
			}
		}
	}

	/**
	 * Save data for archive document types
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function save_archive_templates( $data = [] ) {

		if ( ! $this->is_editable_by_current_user() || empty( $data ) ) {
			return false;
		}

		if ( ! empty( $data['settings'] ) ) {
			if ( Elementor\DB::STATUS_AUTOSAVE === $data['settings']['post_status'] ) {
				if ( ! defined( 'DOING_AUTOSAVE' ) ) {
					define( 'DOING_AUTOSAVE', true );
				}
			}

			$this->save_settings( $data['settings'] );

			//Refresh post after save settings.
			$this->post = get_post( $this->post->ID );
		}

		if ( ! empty( $data['elements'] ) ) {
			$this->save_elements( $data['elements'] );
		}

		$this->save_template_type();

		$this->save_version();

		$this->save_template_item_to_meta( $this->post->ID );

		// Update Post CSS
		$post_css = new Elementor\Core\Files\CSS\Post( $this->post->ID );

		$this->enqueue_custom_fonts_epro( $post_css );

		$post_css->update();

		return true;

	}

	/**
	 * Get elements data with new query
	 *
	 * @param null    $data
	 * @param boolean $with_html_content
	 *
	 * @return array
	 */
	public function get_elements_raw_data( $data = null, $with_html_content = false ) {

		jet_woo_builder()->documents->switch_to_preview_query();

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

		jet_woo_builder()->documents->restore_current_query();

		return $editor_data;

	}

	/**
	 * Render current element
	 *
	 * @param $data
	 *
	 * @return string
	 * @throws Exception
	 */
	public function render_element( $data ) {

		jet_woo_builder()->documents->switch_to_preview_query();

		$render_html = parent::render_element( $data );

		jet_woo_builder()->documents->restore_current_query();

		return $render_html;

	}

	/**
	 * Return elements data
	 *
	 * @param string $status
	 *
	 * @return array
	 */
	public function get_elements_data( $status = 'publish' ) {

		if ( ! isset( $_GET[ jet_woo_builder_post_type()->slug() ] ) || ! isset( $_GET['preview'] ) ) {
			return parent::get_elements_data( $status );
		}

		jet_woo_builder()->documents->switch_to_preview_query();

		$elements = parent::get_elements_data( $status );

		jet_woo_builder()->documents->restore_current_query();

		return $elements;

	}

}