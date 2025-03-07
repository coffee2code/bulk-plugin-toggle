# Changelog

## 1.0.3 _(2025-01-19)_
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

## 1.0.2 _(2021-11-27)_
* Change: Note compatibility through WP 5.8+
* Change: Unit tests: In bootstrap, move definition of constant for plugin file directory to top of file

## 1.0.1 _(2021-07-13)_
* Change: Note compatibility through WP 5.7+
* Change: Add a tad more to the plugin's longer description
* Change: Update copyright date (2021)
* Change: Trivial code tweaks
* Change: Fix typo in inline parameter documentation
* Change: Unit tests: Move `phpunit/` into `tests/`

## 1.0 _(2020-08-07)_
* Initial public release.