<?php
/**
 * Uninstall Photo Trail.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'photo_trail_settings' );
