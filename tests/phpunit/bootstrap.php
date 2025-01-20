<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Bulk_Plugin_Toggle
 */

// Prevent web access.
( php_sapi_name() !== 'cli' ) && die();

define( 'C2C_BULK_PLUGIN_TOGGLE_PLUGIN_DIR', dirname( __DIR__, 2 ) );
define( 'C2C_BULK_PLUGIN_TOGGLE_PLUGIN_FILE', C2C_BULK_PLUGIN_TOGGLE_PLUGIN_DIR . '/bulk-plugin-toggle.php' );

$polyfill_path = C2C_BULK_PLUGIN_TOGGLE_PLUGIN_DIR . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
if ( file_exists( $polyfill_path ) ) {
	require $polyfill_path;
} else {
	echo "Error: PHPUnit Polyfills dependency not found.\n";
	echo "Run: composer require --dev yoast/phpunit-polyfills:\"^2.0\"\n";
	exit;
}

! defined( 'WP_RUN_CORE_TESTS' ) && define( 'WP_RUN_CORE_TESTS', false );

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

// Backward compatibility (PHPUnit < 6).
$phpunit_backcompat = array(
	'\PHPUnit\Framework\TestCase' => 'PHPUnit_Framework_TestCase',
);
foreach ( $phpunit_backcompat as $new => $old ) {
	if ( ! class_exists( $new ) && class_exists( $old ) ) {
		class_alias( $old, $new );
	}
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );

// Check if installed in a src checkout.
if ( ! $_tests_dir && false !== ( $pos = stripos( __FILE__, '/src/wp-content/plugins/' ) ) ) {
	$_tests_dir = substr( __FILE__, 0, $pos ) . '/tests/phpunit/';
}
// Elseif no path yet, assume a temp directory path.
elseif ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib/tests/phpunit/';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php\n";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require C2C_BULK_PLUGIN_TOGGLE_PLUGIN_FILE;
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
