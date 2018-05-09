<?php
/**
 * Advanced Ads Tracking Page Log
 *
 * Plugin Name:       Advanced Ads Tracking Page Log
 * Plugin URI:        https://wpadvancedads.com/
 * Description:       Track every page impression when `ADVANCED_ADS_TRACKING_DEBUG` is enabled
 * Version:           0.1
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die; // -TODO use proper header
}

add_action( 'wp_loaded', 'advanced_ads_tracking_log_page_impression' );

function advanced_ads_tracking_log_page_impression(){
	if( !defined( 'DOING_AJAX' )
		&& ! is_admin()
		&& defined( 'ADVANCED_ADS_TRACKING_DEBUG') ){
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
			$url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$log_content = date_i18n( 'Y-m-d H:i:s' ) . ";page;–;$ip;$url;\"$user_agent\""  . "\n";
			error_log( $log_content, 3, WP_CONTENT_DIR . '/advanced-ads-tracking.csv' );
	}
}