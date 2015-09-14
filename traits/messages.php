<?php
trait Messages {

	use Element;

	/**
	 * @var array
	 */
	private $messages = array(
		'info'    => array(),
		'error'   => array(),
		'success' => array(),
	);

	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @var bool
	 */
	private $have_errors = false;

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
	public function add_general_message( $type = 'success', $message ) {
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
	 * @param $slug
	 * @param string $classes
	 *
	 * @return bool
	 * @internal param $string : the field slug
	 *
	 * @author Nicolas Juen
	 */
	public function get_field_class( $slug, $classes = '' ) {
		return isset( $this->errors[ $slug ] ) ? $classes.' error' : $classes;
	}

	/**
	 * the field class
	 *
	 * @param $slug
	 * @param string $classes
	 *
	 * @return bool
	 * @internal param $string : the field slug
	 *
	 * @author Nicolas Juen
	 */
	public function the_field_class( $slug, $classes = '' ) {
		echo sanitize_html_class( self::get_field_class( $slug, $classes ) );
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
		$code     = (int) Element::element_in_or_default( 'get', 'code' );

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

		if ( BEA_Form::element_in_post( $post_action ) === $post_action_value ) {
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
}