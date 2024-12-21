<?php
/**
 * Plugin Name:     Auto Checksum Verifier
 * Plugin URI:      https://appideas.com
 * Description:     A minimalistic security plugin that validates the checksums of WordPress Core files.
 * Author:          appideasdotcom
 * Author URI:      https://appideas.com
 * Text Domain:     auto-checksum-verifier
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Auto_Checksum_Verifier
 */

// Your code starts here.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-auto-checksum-verifier.php';
ACV_Auto_Checksum_Verifier::get_instance();
