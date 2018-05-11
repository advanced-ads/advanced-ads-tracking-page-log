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
    die;
}

class Advanced_Ads_Page_Impression
{
	private static $instance = null;
	
	// Tracking method. Possible values: "PHP" for server side tracking; "JS" for front end tracking; "ALL" for both method.
	private $method = 'JS';
	
	// place all WP actions
	private function __construct() {
		if ( !in_array( $this->method, array( 'PHP', 'JS', 'ALL' ) ) ) {
			$this->method = 'JS';
		}
		if ( 'PHP' === $this->method ) {
			
			add_action( 'wp_loaded', array( $this, 'php_track' ) );
			
		} elseif ( 'JS' === $this->method || 'ALL' === $this->method ) {
			
			add_action( 'wp_ajax_advanced_ads_pageview', array( $this, 'js_track' ) );
			add_action( 'wp_ajax_nopriv_advanced_ads_pageview', array( $this, 'js_track' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'js' ) );
			add_action( 'wp_head', array( $this, 'wp_head' ) );
			
			if ( 'ALL' === $this->method ) {
				add_action( 'wp_loaded', array( $this, 'php_track' ) );
			}
			
		}
	}
	
	// write in file (PHP)
	public function php_track() {
		if (
			!defined( 'DOING_AJAX' )
			&& !is_admin()
		) {
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
			$url = isset( $_SERVER['REQUEST_SCHEME'] ) ? $_SERVER['REQUEST_SCHEME'] . '://' : '://';
			$url .= isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
			$url .= isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$log_content = date_i18n( 'Y-m-d H:i:s' ) . ";pageviewPHP;–;$ip;$url;\"$user_agent\""  . "\n";
			error_log( $log_content, 3, WP_CONTENT_DIR . '/advanced-ads-tracking.csv' );
		}
	}
	
	// write in file (called from JS)
	public function js_track() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$log_content = date_i18n( 'Y-m-d H:i:s' ) . ";pageviewJS;–;$ip;$url;\"$user_agent\""  . "\n";
		error_log( $log_content, 3, WP_CONTENT_DIR . '/advanced-ads-tracking.csv' );
		echo 1;
		die;
	}
	
	// enqueue our JS after jQuery for maximum compatibility
	public function js() {
		wp_enqueue_script( 'advanced-ads/pageview', plugins_url( 'pageview.js', __FILE__ ), array( 'jquery' ), false, true );
	}
	
	// print inline JS (ajax url)
	public function wp_head() {
		?><script type="text/javascript">var advancedAdsPageviewUrl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';</script><?php
	}
	
	// create or return the unique instance
	public static function build() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			return self::$instance;
		} else {
			return self::$instance;
		}
	}
}

// create an instance only if `ADVANCED_ADS_TRACKING_DEBUG` is defined
if ( defined( 'ADVANCED_ADS_TRACKING_DEBUG') ) {
	Advanced_Ads_Page_Impression::build();
}
