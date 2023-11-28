<?php
/*
 Plugin Name: BEA Form
 Version: 0.2.2
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
	 * @var BEA_Form
	 */
	private static $instance;

	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @var array
	 */
	private $messages = array(
		'info'    => array(),
		'error'   => array(),
		'success' => array(),
	);

	/**
	 * @var bool
	 */
	private $have_errors = false;

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

	/**
	 * Add an error on field
	 *
	 * @param $slug
	 * @param $message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function add_error( $slug, $message ) {
		if ( ! isset( $message ) || empty( $message ) ) {
			return false;
		}
		$this->have_errors     = true;
		$this->errors[ $slug ] = $message;

		return true;
	}

	/**
	 * Add general message
	 *
	 * @param string $type
	 * @param string $message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function add_general_message( $type, $message ) {
		if ( ! isset( $this->messages[ $type ] ) || ! isset( $message ) || empty( $message ) ) {
			return false;
		}

		$this->messages[ $type ][] = $message;

		return true;
	}

	/**
	 * Add general error message
	 *
	 * @param $message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function add_general_error( $message ) {
		$this->have_errors = true;

		return $this->add_general_message( 'error', $message );
	}

	/**
	 * Add general info message
	 *
	 * @param $message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function add_general_info( $message ) {
		return $this->add_general_message( 'info', $message );
	}

	/**
	 * Add a general success message
	 *
	 * @param $message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function add_general_success( $message ) {
		return $this->add_general_message( 'success', $message );
	}

	/**
	 * Check if field error
	 *
	 * @param $slug
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function is_field_error( $slug ) {
		return isset( $this->errors[ $slug ] );
	}

	/**
	 * Check if the field is success
	 *
	 * @param $slug
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function is_field_success( $slug ) {
		return isset( $this->messages['success'][ $slug ] );
	}

	/**
	 * Get a field error message
	 *
	 * @param $slug
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	public function the_field_error( $slug ) {
		return isset( $this->errors[ $slug ] ) ? $this->errors[ $slug ] : '' ;
	}

	/**
	 * get the field class
	 *
	 * @param string : the field slug
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function get_field_class( $slug, $classes = '' ) {
		return isset( $this->errors[ $slug ] ) ? $classes.' error' : $classes;
	}

	/**
	 * the field class
	 *
	 * @param string : the field slug
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function the_field_class( $slug, $classes = '' ) {
		echo self::get_field_class( $slug, $classes );
	}

	/**
	 * Check if has error
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function have_form_error() {
		return $this->have_errors;
	}

	/**
	 * Check if has success message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function have_form_success() {
		return ! empty( $this->messages['success'] );
	}

	/**
	 * Check if has info message
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function have_form_info() {
		return ! empty( $this->messages['info'] );
	}

	/**
	 * Check if the form has messages
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public function have_form_message() {
		foreach ( $this->messages as $type_mess => $messages ) {
			if ( ! empty( $messages ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the from messages
	 *
	 * @param string $type
	 *
	 * @return array|bool|string
	 * @author Nicolas Juen
	 */
	public function get_form_messages( $type = '' ) {
		if ( empty( $this->messages ) ) {
			return '';
		}

		if ( empty( $type ) ) {
			return $this->messages;
		}

		if ( isset( $this->messages[ $type ] ) ) {
			return $this->messages[ $type ];
		}

		return '';
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
		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		if ( ! empty( $index ) ) {
			// Return the element in post if present
			return isset( $_POST[ $index ][ $slug ] ) ? $_POST[ $index ][ $slug ] : null;
		}

		return isset( $_POST[ $slug ] ) ? $_POST[ $slug ] : null;
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
		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		if ( ! empty( $index ) ) {
			// Return the elment in post if present
			return isset( $_GET[ $index ][ $slug ] ) ? $_GET[ $index ][ $slug ] : null;
		}

		return isset( $_GET[ $slug ] ) ? $_GET[ $slug ] : null;
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
		// Launch session if needed
		if ( session_id() == '' ) {
			session_start();
		}

		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		if ( ! empty( $index ) ) {
			// Return the elment in post if present
			return isset( $_SESSION[ $index ][ $slug ] ) ? $_SESSION[ $index ][ $slug ] : null;
		}

		return isset( $_SESSION[ $slug ] ) ? $_SESSION[ $slug ] : null;
	}

	/**
	 * @param        $default_value
	 * @param        $type
	 * @param string $index
	 *
	 * @return string|null
	 * @author Nicolas Juen
	 */
	public static function element_in_get_or_default( $type, $default_value, $index = '' ) {
		if ( empty( $type ) ) {
			return null;
		}

		$data = self::element_in_get( $type, $index );

		return ! is_null( $data ) ? $data : $default_value;
	}

	/**
	 * @param        $default_value
	 * @param        $type
	 * @param string $index
	 *
	 * @return bool|null
	 * @author Nicolas Juen
	 */
	public static function element_in_post_or_default( $type, $default_value, $index = '' ) {
		if ( empty( $type ) ) {
			return null;
		}

		$data = self::element_in_post( $type, $index );

		return ! is_null( $data ) ? $data : $default_value;
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
		$data = self::element_in_post( $slug, $index );

		// Check there is data on the post
		if ( is_null( $data ) ) {
			$data = self::element_in_session( $slug, $index );
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
		// If no slug, return false
		if ( ! isset( $slug ) || empty( $slug ) ) {
			return null;
		}

		if ( ! empty( $index ) ) {
			// Return the element in post if present
			return isset( $_REQUEST[ $slug ][ $index ] ) ? $_REQUEST[ $slug ][ $index ] : null;
		}

		return isset( $_REQUEST[ $slug ] ) ? $_REQUEST[ $slug ] : null;
	}

	/**
	 * Return the contextual message
	 *
	 * @param string $action
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	public function get_contextual_message( $action = '' ) {
		// Get the messages
		/**
		 * Array of messages to display like :
		 *
		 * array(
		 *  0 => array( 'type' => 'error', 'message' => 'Error !' ),
		 *  1 => array( 'type' => 'success', 'message' => 'Success !' ),
		 *  2 => array( 'type' => 'info', 'message' => 'Info !' ),
		 * );
		 *
		 */
		$messages = apply_filters( 'bea_form_' . $action . '_messages', array() );
		$code     = isset( $_GET['code'] ) ? absint( $_GET['code'] ) : - 1;

		// Check the messages, types, code
		if ( empty( $messages ) || ! isset( $messages[ $code ] ) || ! isset( $_GET['action'] ) || $_GET['action'] !== $action || ! isset( $messages[ $code ]['type'] ) || ! isset( $messages[ $code ]['message'] ) ) {
			return '';
		}

		// Make the function name
		$type = 'add_general_' . $messages[ $code ]['type'];

		if ( ! method_exists( $this, $type ) ) {
			return '';
		}

		// Add the message
		$this->$type( $messages[ $code ]['message'] );

		return $this->display_form_messages();
	}

	/**
	 * Display contextual message
	 *
	 * @param string $action
	 *
	 * @author Nicolas Juen
	 */
	public function display_contextual_message( $action = '' ) {
		echo $this->get_contextual_message( $action );
	}

	/**
	 * Display the messages depending on the action
	 *
	 * @param string $action : action for the contextual messages
	 * @param string $post_action : the action on the $_POST
	 * @param string $post_action_value : the post action value to check on the $_POST data
	 *
	 * @return void
	 * @author Nicolas Juen
	 */
	public function display_contextual_or_post( $action = '', $post_action = 'action', $post_action_value = '' ) {
		$post_action_value = ! empty( $post_action_value ) ? $post_action_value : $action;

		if ( self::element_in_post( $post_action ) === $post_action_value ) {
			echo $this->display_form_messages();

			return;
		}

		$this->display_contextual_message( $action );
	}
	
	/**
	 * Display the messages depending on the action
	 *
	 * @param string $action : action for the contextual messages
	 * @param string $request_action : the action on the $_REQUEST
	 * @param string $request_action_value : the REQUEST action value to check on the $_REQUEST data
	 *
	 * @return void
	 * @author Nicolas Juen
	 */
	public function display_contextual_or_request( $action = '', $request_action = 'action', $request_action_value = '' ) {
		$request_action_value = ! empty( $request_action_value ) ? $request_action_value : $action;

		if ( self::element_in_request( $request_action ) === $request_action_value ) {
			echo $this->display_form_messages();

			return;
		}

		$this->display_contextual_message( $action );
	}

	/**
	 * Return the messages
	 *
	 * @param string $type
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	public function display_form_messages( $type = '' ) {
		$out = '';
		if ( false === $this->have_form_message() ) {
			return $out;
		}

		if ( empty( $type ) ) {
			foreach ( $this->messages as $messages_type => $messages ) {
				if ( empty( $messages ) ) {
					continue;
				}

				$out .= self::get_messages_lines( $messages, $messages_type );
			}
		} elseif ( isset( $this->messages[ $type ] ) ) {
			$out = self::get_messages_lines( $this->messages[ $type ], $type );
		}

		return $out;
	}

	/**
	 * Get a message line
	 *
	 * @param $message
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	private static function get_message_line( $message ) {
		$format = apply_filters( 'bea_form_message_line_format', '<li>%s</li>' );

		return apply_filters( 'bea_form_message_line', sprintf( $format, $message ) );
	}

	/**
	 * Get the messages lines
	 *
	 * @param $messages
	 * @param $type
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	private static function get_messages_lines( $messages, $type ) {
		$out = sprintf( apply_filters( 'bea_form_message_before_block_format', '<ul class="form message %s">', $type ), $type );

		foreach ( $messages as $message ) {
			$out .= self::get_message_line( $message );
		}

		$out .= apply_filters( 'bea_form_message_after_block_format', '</ul>', $type );

		return $out;
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
