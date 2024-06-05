<?php
/**
 * Data management interface.
 *
 * @package firebase-sso
 */

namespace Itumulak\WpSsoFirebase\Models\Interface;

/**
 * Data_Management_Interface
 */
interface Data_Management_Interface {
	/**
	 * Get a datum.
	 *
	 * @param  string $key
	 * @return string|bool|array
	 */
	public function get( string $key ) : string|bool|array;

	/**
	 * Get all data.
	 *
	 * @return array
	 */
	public function get_all(): array;

	/**
	 * Save data.
	 *
	 * @param  array $data
	 * @return bool
	 */
	public function save( array $data ) : bool;
}
