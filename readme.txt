=== Auto Checksum Verifier ===
Contributors: appideasdotcom,Dotcomjungle
Tags: comments, spam
Requires at least: 5.0
Tested up to: 6.7.1
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimalistic security plugin that validates the checksums of WordPress Core files
and emails a site admin if issues are detected.

== Description ==

Most WordPress website hacks rely on changed or added files in WordPress Core.

This lightweight plugin skips the bulky overhead of a full-fledged security suite
and detects most WordPress hacks in milliseconds. Issues are emailed to a desired
email address or the site admin.

This will not prevent hacks from taking place, but it will provide the knowledge
you need if and when such an event happens.

Why? Properly developed, secured and maintained WordPress websites are rarely hacked.
On the other hand, security plugins that detect and fix hack attempts are very
"heavy" and invariably affect the speed of your website traffic on every page load.
This tool provides a non-resource-intrusive means of detecting these rare occurrences.

Note that this plugin is primarily for agencies and developers to deploy on websites
that they maintain regularly. If you have been hacked or do not know how to respond
to a hack report, we recommend installing one of the more thorough security
plugins, or contacting one of our development partners for assistance.
APP(ideas) at https://appideas.com or Dotcomjungle at https://www.dotcomjungle.com/

== Installation ==

- Normal installation: From WordPress admin > Plugins > Add New Plugin, search for
"Automatic Checksum Verifier." Once found, click "Install Now" and "Activate."
- Manual installation: WordPress Admin > Plugins > Add New Plugin > Upload Plugin

== Configuration ==

- Go to WordPress Admin > Settings > Checksums
  - Add your email address
  - Optional: Check "Include root files" to detect when unexpected files have been
added to your Core files
- Optional: To control the schedule, install and activate the wp-crontrol plugin
from WordPress Admin > Settings > Plugins > Add New Plugin > Search
  - Go to Tools > Cron Events
  - Add a new event with the Hook Name "acv_verify_checksums" and your own schedule,
  or modify the current schedule for the "acv_verify_checksums" event.
  - Note: If you do not install the wp-crontrol, Automatic Checksum Verifier will
run at 1 AM daily, local server time.

== Screenshots ==

== Changelog ==

= 1.0 =
* Initial version

