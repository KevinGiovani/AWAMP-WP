<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function link__WSC( $actions ) {
	$new_actions = array(
		'settings' => sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'admin.php?page='.WSC__SLUG ),
			__( 'Settings', WSC__DOMAIN )
		),
	);

	return array_merge( $new_actions, $actions );
}
add_filter( 'plugin_action_links_' . WSC__PLUGIN_BASENAME, 'link__WSC' );

function rowmeta__WSC( $plugin_meta, $plugin_file ) {
	if ( $plugin_file != WSC__PLUGIN_BASENAME ) {
		return $plugin_meta;
	}

	$new_meta_links = array(
		'<a href="'.WSC__DOCUMENT_LINK.'" target="_blank">'.__( "Docs", WSC__DOMAIN ).'</a>',
		'<a href="'.WSC__SUPPORT_LINK.'" target="_blank">'.__( "Support", WSC__DOMAIN ).'</a>'
	);

	return array_merge( $plugin_meta, $new_meta_links );
}
add_filter( 'plugin_row_meta', 'rowmeta__WSC', 10, 2 );
?>