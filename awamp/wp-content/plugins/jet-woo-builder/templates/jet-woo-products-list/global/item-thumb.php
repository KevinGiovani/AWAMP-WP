<?php
/**
 * Loop item thumbnail
 */

$settings   = $this->get_settings();
$size       = $this->get_attr( 'thumb_size' );
$thumbnail  = jet_woo_builder_template_functions()->get_product_thumbnail( $size );
$open_link  = '';
$close_link = '';

if ( 'yes' !== $this->get_attr( 'show_image' ) || null === $thumbnail ) {
	return;
}

if ( 'yes' === $settings['is_linked_image'] ) {
	$open_link  = '<a href="' . esc_url( get_permalink() ) . '" ' . $target_attr . '>';
	$close_link = '</a>';
}
?>

<div class="jet-woo-product-thumbnail">
	<?php
	do_action( 'jet-woo-builder/templates/products-list/before-item-thumbnail' );
	echo $open_link . $thumbnail . $close_link;
	do_action( 'jet-woo-builder/templates/products-list/after-item-thumbnail' );
	?>
</div>