<?php
/**
 * Plugin Name: Bulk Plugin Toggle
 * Version:     1.0
 * Plugin URI:  https://coffee2code.com/wp-plugins/bulk-plugin-toggle/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bulk-plugin-toggle
 * Description: Adds "Toggle" as a bulk action for the plugins listing to toggle the activation state for selected plugins.
 *
 * Compatible with WordPress 4.9+ through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/bulk-plugin-toggle/
 *
 * @package Bulk_Plugin_Toggle
 * @author  Scott Reilly
 * @version 1.0
 */

/*
	Copyright (c) 2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_Bulk_Plugin_Toggle' ) ) :

class c2c_Bulk_Plugin_Toggle {
	protected static $show_admin_notice = false;

	public static function init() {
		add_action( 'admin_init',                  [ __CLASS__, 'remove_query_var' ] );
		add_filter( 'bulk_actions-plugins',        [ __CLASS__, 'add_bulk_toggle' ] );
		add_filter( 'handle_bulk_actions-plugins', [ __CLASS__, 'handle_bulk_toggle' ], 10, 3 );
		add_action( 'pre_current_active_plugins',  [ __CLASS__, 'add_admin_notice' ] );
	}	

	public static function remove_query_var() {
		$_SERVER['REQUEST_URI'] = remove_query_arg( [ 'toggle-multi' ], $_SERVER['REQUEST_URI'] );
	}

	public static function check_user_permissions( $die_with_error = true ) {
		$permitted = true;

		if ( ! current_user_can( 'activate_plugins' ) ) {
			$permitted = false;
			if ( $die_with_error ) {
				wp_die( __( 'Sorry, you are not allowed to activate plugins for this site.' ) );
			}
		}

		if ( ! current_user_can( 'deactivate_plugins' ) ) {
			$permitted = false;
			if ( $die_with_error ) {
				wp_die( __( 'Sorry, you are not allowed to deactivate plugins for this site.' ) );
			}
		}

		return $permitted;
	}

	public static function add_bulk_toggle( $actions ) {
		if ( ! isset( $_GET['plugin_status'] ) || in_array( $_GET['plugin_status'], [ 'all', 'upgrade' ] ) ) {
			$actions['toggle-selected'] = __( 'Toggle', 'bulk-plugin-toggle' );
		}

		return $actions;
	}

	protected static function activate_plugins( $plugins ) {
		if ( is_network_admin() ) {
			foreach ( $plugins as $i => $plugin ) {
				// Only activate plugins which are not already network activated.
				if ( is_plugin_active_for_network( $plugin ) ) {
					unset( $plugins[ $i ] );
				}
			}
		} else {
			foreach ( $plugins as $i => $plugin ) {
				// Only activate plugins which are not already active and are not network-only when on Multisite.
				if ( is_plugin_active( $plugin ) || ( is_multisite() && is_network_only_plugin( $plugin ) ) ) {
					unset( $plugins[ $i ] );
				}
				// Only activate plugins which the user can activate.
				if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
					unset( $plugins[ $i ] );
				}
			}
		}

		if ( empty( $plugins ) ) {
			return false;
		}

		activate_plugins( $plugins, self_admin_url( 'plugins.php?error=true' ), is_network_admin() );

		if ( ! is_network_admin() ) {
			$recent = (array) get_option( 'recently_activated' );
		} else {
			$recent = (array) get_site_option( 'recently_activated' );
		}

		foreach ( $plugins as $plugin ) {
			unset( $recent[ $plugin ] );
		}

		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', $recent );
		} else {
			update_site_option( 'recently_activated', $recent );
		}

		return true;
	}

	protected static function deactivate_plugins( $plugins ) {
		// Do not deactivate plugins which are already deactivated.
		if ( is_network_admin() ) {
			$plugins = array_filter( $plugins, 'is_plugin_active_for_network' );
		} else {
			$plugins = array_filter( $plugins, 'is_plugin_active' );
			$plugins = array_diff( $plugins, array_filter( $plugins, 'is_plugin_active_for_network' ) );

			foreach ( $plugins as $i => $plugin ) {
				// Only deactivate plugins which the user can deactivate.
				if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
					unset( $plugins[ $i ] );
				}
			}
		}

		if ( empty( $plugins ) ) {
			return false;
		}

		deactivate_plugins( $plugins, false, is_network_admin() );

		$deactivated = array();
		foreach ( $plugins as $plugin ) {
			$deactivated[ $plugin ] = time();
		}

		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', $deactivated + (array) get_option( 'recently_activated' ) );
		} else {
			update_site_option( 'recently_activated', $deactivated + (array) get_site_option( 'recently_activated' ) );
		}

		return true;
	}

	protected static function split_plugins( $plugins ) {
		$to_activate = $to_deactivate = [];

		$network_activate = array_filter( $plugins, 'is_plugin_active_for_network' );

		if ( is_network_admin() ) {
			$to_deactivate = $network_active;
		} else {
			$to_deactivate = array_filter( $plugins, 'is_plugin_active' );
			$to_deactivate = array_diff( $to_deactivate, $network_activate );
		}

		$to_activate = array_diff( $plugins, $to_deactivate );

		return [ $to_activate, $to_deactivate ];
	}

	public static function handle_bulk_toggle( $sendback, $action, $plugins ) {
		global $page, $s, $status;

		self::$show_admin_notice = false;

		if ( 'toggle-selected' !== $action ) {
			return $sendback;
		}

		if ( empty( $plugins ) ) {
			wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		}

		// Check user permissions.
		self::check_user_permissions();

		// Split plugins by which should be activated and which should be deactivated.
		list( $plugins_to_activate, $plugins_to_deactivate ) = self::split_plugins( $plugins );

		// Activate plugins.
		$did_activation = self::activate_plugins( $plugins_to_activate );

		// Deactivate plugins.
		$did_deactivations = self::deactivate_plugins( $plugins_to_deactivate );

		if ( ! $did_activation && ! $did_deactivations ) {
			wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		}

		wp_redirect( self_admin_url( "plugins.php?toggle-multi=true&plugin_status=$status&paged=$page&s=$s" ) );
		exit;

		return $sendback;
	}

	public static function add_admin_notice() {
		if ( empty( $_GET[ 'toggle-multi' ] ) ) {
			return;
		}

		printf(
			'<div id="message" class="updated notice is-dismissible"><p>%s</p></div>' . "\n",
			__( 'Selected plugins toggled.', 'bulk-plugin-toggle' )
		);
	}

}

add_action( 'plugins_loaded', [ 'c2c_Bulk_Plugin_Toggle', 'init' ] );

endif;
