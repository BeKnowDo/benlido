<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @package     WC-Google-Analytics-Pro/Integration
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2018, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.6.0
 *
 * @method \WC_Google_Analytics_Pro get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Performs any version-related changes.
	 *
	 * @since 1.6.0
	 *
	 * @param int $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		if ( ! empty( $installed_version ) ) {

			$update_path = array(
				'1.3.0' => 'update_to_1_3_0',
				'1.5.2' => 'update_to_1_5_2',
			);

			foreach ( $update_path as $update_to_version => $update_script ) {

				if ( version_compare( $installed_version, $update_to_version, '<' ) ) {

					$this->$update_script();

					$this->get_plugin()->log( sprintf( 'Updated to to %s', $update_to_version ) );
				}
			}
		}
	}


	/**
	 * Update to 1.3.0
	 *
	 * @since 1.6.0
	 */
	private function update_to_1_3_0() {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', array() );

		// pre 1.3.0 `__gaTracker` was the default function name - store an option & setting for it, so we don't break compatibility
		add_option( 'woocommerce_google_analytics_upgraded_from_gatracker', true );

		$settings['function_name'] = '__gaTracker';

		// convert profile > property
		if ( ! empty( $settings['profile'] ) ) {

			$parts = explode( '|', $settings['profile'] );

			$settings['property'] = $parts[0] . '|' . $parts[1];

			unset( $settings['profile'] );
		}

		// install default event names for new events
		$new_events = array(
			'provided_billing_email',
			'selected_payment_method',
			'placed_order',
		);

		$form_fields = $this->get_plugin()->get_integration()->get_form_fields();

		foreach ( $new_events as $event ) {

			$settings[ "{$event}_event_name" ] = $form_fields[ "{$event}_event_name" ]['default'];
		}

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		delete_transient( 'wc_google_analytics_pro_profiles' );

		// ensure that events and properties are reloaded after the upgrade
		$this->get_plugin()->get_integration()->load_events_and_properties();
	}


	/**
	 * Update to version 1.5.2
	 *
	 * @since 1.6.0
	 */
	private function update_to_1_5_2() {

		// in v1.5.0 some Subscriptions events were introduced but their default values were not saved in settings
		$saved_settings       = get_option( 'woocommerce_google_analytics_pro_settings', array() );
		$modified_settings    = false;
		$subscriptions_events = array(
			'activated_subscription'           => 'activated subscription',
			'subscription_trial_ended'         => 'subscription trial ended',
			'subscription_end_of_prepaid_term' => 'subscription prepaid term ended',
			'subscription_expired'             => 'subscription expired',
			'suspended_subscription'           => 'suspended subscription',
			'reactivated_subscription'         => 'reactivated subscription',
			'cancelled_subscription'           => 'cancelled subscription',
			'renewed_subscription'             => 'subscription billed',
		);

		foreach ( $subscriptions_events as $setting => $default_value ) {

			$setting = "{$setting}_event_name";

			// only set the value if it wasn't saved before
			if ( ! isset( $saved_settings[ $setting ] ) || ! is_string( $saved_settings[ $setting ] ) ) {

				$saved_settings[ $setting ] = $default_value;

				$modified_settings = true;
			}
		}

		if ( $modified_settings ) {

			update_option( 'woocommerce_google_analytics_pro_settings', $saved_settings );

			// ensure that events and properties are reloaded after the upgrade
			$this->get_plugin()->get_integration()->load_events_and_properties();
		}
	}


}
