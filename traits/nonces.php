<?php
trait Nonces {
	/**
	 * check the given nonce on given action
	 *
	 * @param        $action
	 * @param string $name
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public static function check_nonce( $action, $name = '_wpnonce' ) {
		return ! isset( $_REQUEST[ $name ] ) || ! wp_verify_nonce( $_REQUEST[ $name ], $action ) ? false : true;
	}

	/**
	 * Check the nonce in mpt
	 *
	 * @param        $action
	 * @param string $name
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public static function check_mpt_nonce( $action, $name = '_mptnonce' ) {
		if ( ! function_exists( 'mpt_verify_nonce' ) ) {
			return self::check_nonce( $action, $name );
		}

		return ! isset( $_REQUEST[ $name ] ) || ! mpt_verify_nonce( $_REQUEST[ $name ], $action ) ? false : true;
	}

}