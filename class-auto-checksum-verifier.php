<?php
/**
 * Implementation of the Auto Checksum Verifier plugin functionality.
 */

class ACV_Auto_Checksum_Verifier {


	/**
	 * Singleton instance of ACV_Auto_Checksum_Verifier
	 *
	 * @var ACV_Auto_Checksum_Verifier
	 */
	protected static $instance;

	/**
	 * Only make one instance of ACV_Auto_Checksum_Verifier
	 *
	 * @return ACV_Auto_Checksum_Verifier
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof Auto_CheACV_Auto_Checksum_Verifiercksum_Verifier ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	protected function __construct() {

	}

}