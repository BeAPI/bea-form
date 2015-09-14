<?php
trait Element {

	/**
	 * Return data from $_REQUEST if needed
	 *
	 * @param string $type
	 * @param $slug
	 * @param string $index
	 *
	 * @return null
	 * @author Nicolas Juen
	 */
	public static function element_in( $type = 'post', $slug, $index = '' ) {
		$var = '_POST';
		switch ( $type ) {
			case 'post':
				$var = $_POST;
			break;
			case 'get':
				$var = $_GET;
			break;
			case 'session':
				// Launch session if needed
				if ( '' === session_id() ) {
					session_start();
				}

				$var = $_SESSION;
			break;
			case 'request':
				$var = $_REQUEST;
				break;
		}

		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		if ( ! empty( $index ) ) {
			// Return the element in post if present
			return isset( $var[ $index ][ $slug ] ) ? $var[ $index ][ $slug ] : null;
		}

		return isset( $var[ $slug ] ) ? $var[ $slug ] : null;
	}

	/**
	 * Return the given value if found
	 *
	 * @param string $type
	 * @param $slug
	 * @param $default_value
	 * @param string $index
	 *
	 * @return null
	 * @author Nicolas Juen
	 */
	public static function element_in_or_default( $type = 'post', $slug, $default_value, $index = '' ) {
		if ( empty( $type ) ) {
			return null;
		}

		$data = self::element_in( $type, $slug, $index );

		return ! is_null( $data ) ? $data : $default_value;
	}
}
