<?php
/**
 * Uninstall Contact Floating.
 *
 * @package ContactFloating
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'contact_floating_options' );

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	if ( empty( $site_ids ) ) {
		return;
	}

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		delete_option( 'contact_floating_options' );
		restore_current_blog();
	}
}
