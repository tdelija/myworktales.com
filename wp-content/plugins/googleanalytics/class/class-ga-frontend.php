<?php
/**
 * GoogleAnalytics Frontend.
 *
 * @package GoogleAnalytics
 */

/**
 * Frontend.
 */
class Ga_Frontend {

	const GA_SHARETHIS_PLATFORM_URL = '//platform-api.sharethis.com/js/sharethis.js#source=googleanalytics-wordpress';

	/**
	 * Platform ShareThis.
	 *
	 * @return void
	 */
	public static function platform_sharethis() {
		$url = self::GA_SHARETHIS_PLATFORM_URL . '#product=ga';
		if ( get_option( Ga_Admin::GA_SHARETHIS_PROPERTY_ID ) ) {
			$url = $url . '&property=' . get_option( Ga_Admin::GA_SHARETHIS_PROPERTY_ID );
		}

		wp_register_script( GA_NAME . '-platform-sharethis', $url, null, null, false ); // phpcs:ignore
		wp_enqueue_script( GA_NAME . '-platform-sharethis' );
	}

	/**
	 * Adds frontend actions hooks.
	 */
	public static function add_actions() {
		if ( Ga_Helper::are_features_enabled() ) {
			add_action( 'wp_enqueue_scripts', 'Ga_Frontend::platform_sharethis' );
		}
		add_action( 'wp_head', 'Ga_Frontend::insert_ga_script' );
	}

	/**
	 * Insert GoogleAnalytics script.
	 *
	 * @return void
	 */
	public static function insert_ga_script() {
		if ( true === Ga_Helper::can_add_ga_code() || true === Ga_Helper::is_all_feature_disabled() ) {
			$web_property_id = self::get_web_property_id();
			$optimize        = get_option( 'googleanalytics_optimize_code' );
			$anonymization   = get_option( 'googleanalytics_ip_anonymization' );
			$debug_mode_on   = 'on' === get_option( 'googleanalytics_enable_debug_mode', 'off' );

			if ( Ga_Helper::should_load_ga_javascript( $web_property_id ) ) {
				$data = array(
					Ga_Admin::GA_WEB_PROPERTY_ID_OPTION_NAME => $web_property_id,
					'optimize'      => $optimize,
					'anonymization' => $anonymization,
				);

				include plugin_dir_path( __FILE__ ) . '../view/ga-code.php';
			}
		}
	}

	/**
	 * Gets and returns Web Property Id.
	 *
	 * @return string Web Property Id
	 */
	public static function get_web_property_id() {
		$web_property_id = get_option( Ga_Admin::GA_WEB_PROPERTY_ID_OPTION_NAME );
		if ( true === Ga_Helper::is_code_manually_enabled() || true === Ga_Helper::is_all_feature_disabled() ) {
			$web_property_id = get_option( Ga_Admin::GA_WEB_PROPERTY_ID_MANUALLY_VALUE_OPTION_NAME );
		}

		return $web_property_id;
	}
}
