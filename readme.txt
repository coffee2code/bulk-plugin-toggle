=== Bulk Plugin Toggle ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: plugins, toggle, bulk actions, admin, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.9
Tested up to: 6.7
Requires PHP: 5.4
Stable tag: 1.0.3

Adds "Toggle" as a bulk action for the plugins listing to toggle the activation state for selected plugins.

== Description ==

This plugin adds "Toggle" as a bulk action for the plugins listing to toggle the activation state for selected plugins.

From the admin listing of plugins you can now select multiple plugins and choose "Toggle" from the "Bulk actions" dropdown. When applied, all selected plugins that are currently activated will become deactivated, and all selected plugins that are currently deactivated will become activated.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/bulk-plugin-toggle/) | [Plugin Directory Page](https://wordpress.org/plugins/bulk-plugin-toggle/) | [GitHub](https://github.com/coffee2code/bulk-plugin-toggle/) | [Author Homepage](https://coffee2code.com/)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or install the plugin code inside the plugins directory for your site (typically `/wp-content/plugins/`).
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. From the admin 'Plugins' page, select the checkboxes for plugins whose activation state you'd like to toggle, then from the "Bulk Actions" dropdown (found above-left and below-left of the table) choose "Toggle" and press the "Apply" button to submit. Any selected plugins that were active will get deactivated and any that were deactive will get activated.


== Screenshots ==

1. The "Toggle" option in the "Bulk Actions" dropdown for the admin plugins listing.


== Frequently Asked Questions ==

= Why isn't "Toggle" present in the bulk actions dropdown for plugins for me? =

The "Toggle" bulk action is only available to users who have both the "activate_plugins" and "deactivate_plugins" capabilities, which generally implies administrators. The toggle is also only shown when looking at the "All" or "Update Available" views.

= Does this plugin include unit tests? =

Yes. The tests are not packaged in the release .zip file or included in plugins.svn.wordpress.org, but can be found in the [plugin's GitHub repository](https://github.com/coffee2code/bulk-plugin-toggle/).


== Changelog ==

= 1.0.3 (2025-01-19) =
* Change: Prevent translations from containing unintended markup
* Change: Note compatibility through WP 6.7+
* Change: Update copyright date (2025)
* Change: Tweak formatting in `README.md`
* New: Add `.gitignore` file
* Change: Remove development and testing-related files from release packaging
* Unit tests:
    * Hardening: Prevent direct web access to `bootstrap.php`
    * Allow tests to run against current versions of WordPress
    * New: Add `composer.json` for PHPUnit Polyfill dependency
    * Change: Prevent PHP warnings due to missing core-related generated files
    * Change: In bootstrap, add backcompat for PHPUnit pre-v6.0
    * Change: In bootstrap, store path to plugin file constant
    * New: Add script to install WP for unit testing

= 1.0.2 (2021-11-27) =
* Change: Note compatibility through WP 5.8+
* Change: Unit tests: In bootstrap, move definition of constant for plugin file directory to top of file

= 1.0.1 (2021-07-13) =
* Change: Note compatibility through WP 5.7+
* Change: Add a tad more to the plugin's longer description
* Change: Update copyright date (2021)
* Change: Trivial code tweaks
* Change: Fix typo in inline parameter documentation
* Change: Unit tests: Move `phpunit/` into `tests/`

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/bulk-plugin-toggle/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.0.3 =
Trivial update: prevented translations from containing unintended markup, noted compatibility through WP 6.7+, removed unit tests from release packaging, and updated copyright date (2025).

= 1.0.2 =
Trivial update: noted compatibility through WP 5.8+.

= 1.0.1 =
Trivial update: noted compatibility through WP 5.7+, rearranged unit test files and directories slightly, tweaked some documentation, and updated copyright date (2021).

= 1.0 =
Initial public release.
