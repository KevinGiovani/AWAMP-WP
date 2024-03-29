<?php
/**
 * Loop item thumbnail
 */

$size         = $this->get_attr( 'thumb_size' );
$badge_text   = jet_woo_builder()->macros->do_macros( $this->get_attr( 'sale_badge_text' ) );
$thumb_effect = 'yes' === $this->get_attr( 'enable_thumb_effect' );
$thumbnail    = jet_woo_builder_template_functions()->get_product_thumbnail( $size, $thumb_effect );
$sale_badge   = jet_woo_builder_template_functions()->get_product_sale_flash( wp_kses_post( $badge_text ) );

$open_link  = '';
$close_link = '';

if ( 'yes' === $this->get_attr( 'add_thumb_link' ) ) {
	$open_link  = '<a href="' . esc_url( get_permalink() ) . '" ' . $target_attr . '>';
	$close_link = '</a>';
}

if ( null === $thumbnail ) {
	return;
}
?>
<div class="jet-woo-product-thumbnail">
	<?php do_action( 'jet-woo-builder/templates/products/before-item-thumbnail' ) ?>
	<?php echo $open_link . $thumbnail . $close_link; ?>
	<div class="jet-woo-product-img-overlay"></div>
	<?php
	if ( null != $sale_badge && 'yes' === $this->get_attr( 'show_badges' ) ) {
		echo sprintf( '<div class="jet-woo-product-badges">%s</div>', $sale_badge );
	}

	do_action( 'jet-woo-builder/templates/products/after-item-thumbnail' )
	?>
</div>