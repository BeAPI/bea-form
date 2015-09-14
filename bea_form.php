<?php
/*
 Plugin Name: BEA Form
 Version: 0.1
 Plugin URI: https://github.com/beapi/bea-fom
 Description: Simple form class for handling form messages and errors and infos in WordPress
 Author: Beapi
 Author URI: http://www.beapi.fr

 ----

 Copyright 2015 Beapi Technical team (technique@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * See the readme for use case :)
 *
 * Class BEA_Form
 */
class BEA_Form {

	/**
	 * Load nonces
	 */
	use Nonces, Messages, Element;

	/**
	 * @var BEA_Form
	 */
	private static $instance;

	/**
	 * @return BEA_Form
	 * @author Nicolas Juen
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get un element on $_POST
	 *
	 * @param string $slug
	 * @param string $index
	 *
	 * @return string|null
	 * @author Nicolas Juen
	 */
	public static function element_in_post( $slug, $index = '' ) {
		return Element::element_in( 'post', $slug, $index );
	}

	/**
	 * get un element on $_GET
	 *
	 * @param string $slug
	 * @param string $index
	 *
	 * @return string|null
	 * @author Nicolas Juen
	 */
	public static function element_in_get( $slug, $index = '' ) {
		return Element::element_in( 'get', $slug, $index );
	}

	/**
	 * get un element on $_SESSION
	 *
	 * @param string $slug
	 * @param string $index
	 *
	 * @return string|null
	 * @author Nicolas Juen
	 */
	public static function element_in_session( $slug = '', $index = '' ) {
		return Element::element_in( 'session', $slug, $index );
	}

	/**
	 * @param        $default_value
	 * @param        $type
	 * @param string $index
	 *
	 * @return string|null
	 * @author Nicolas Juen
	 */
	public static function element_in_get_or_default( $slug, $default_value, $index = '' ) {
		return Element::element_in_or_default( 'get', $slug, $default_value, $index );
	}

	/**
	 * @param        $default_value
	 * @param        $type
	 * @param string $index
	 *
	 * @return bool|null
	 * @author Nicolas Juen
	 */
	public static function element_in_post_or_default( $slug, $default_value, $index = '' ) {
		return Element::element_in_or_default( 'post', $slug, $default_value, $index );
	}

	/**
	 * Check if the given value is in session or post
	 *
	 * @param string $slug : the slug of the field to test
	 * @param string $index (optional) : the index
	 * @param string $default (optional) : the default value to return if needed
	 *
	 * @return  string|boolean|null : the value of the element
	 * @author Nicolas Juen
	 */
	public static function element_in_post_or_session_or_default( $slug, $index = '', $default = '' ) {
		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		// Check the data from post
		$data = self::element_in( 'post', $slug, $index );

		// Check there is data on the post
		if ( is_null( $data ) ) {
			$data = self::element_in( 'session', $slug, $index );
		}

		return ( ! is_null( $data ) ) ? $data : $default;
	}

	/**
	 * Get element in request
	 *
	 * @param string $slug
	 * @param string $index
	 *
	 * @return bool|null
	 * @author Nicolas Juen
	 */
	public static function element_in_request( $slug, $index = '' ) {
		return self::element_in( 'request', $slug, $index );
	}

	/**
	 * Get maximum file size from WordPress data
	 *
	 * @return bool|string
	 * @author Nicolas Juen
	 */
	public static function get_maximum_file_size() {
		// Size for the upload form

		$bytes = wp_max_upload_size();

		return size_format( $bytes );
	}
}