# Automatic Checksum Verifier for WordPress Core files #

**Contributors:** appideasdotcom\
**Donate link:** https://appideas.com\
**Tags:** comments, spam\
**Requires at least:** 4.5\
**Tested up to:** 6.6.2\
**Requires PHP:** 5.6\
**Stable tag:** 1.0.0\
**License:** GPLv2 or later\
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

A minimalistic security plugin that validates the checksums of WordPress Core files.

## Description ##

A minimalistic security plugin that validates the checksums of WordPress Core files.

## Installation ##
- Install through WordPress Admin > Plugins > Add New Plugin > Upload Plugin

## Configuration ##
- Go to WordPress Admin > Settings > Checksums
  - Add your email address
- Install and activate the wp-crontrol plugin WordPress Admin > Settings > Plugins > Add New Plugin > Search
  - Go to Tools > Cron Events
  - Add a new event with the Hook Name "acv_verify_checksums" and your own schedule.

## Linting ##
`composer` is only needed for linting
```
composer install # one time
./vendor/bin/phpcs -ps ./
```