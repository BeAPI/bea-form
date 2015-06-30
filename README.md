# bea-form
Simple form class for handling form messages and errors and infos in WordPress

# Usage
The usage is very simple, you add messages to singleton class and display them easily.
The purpose of the class is to listen to changes on the page reload when the user submit form, and when there is a
breaking event, like action success or breaking error message, redirect the user to the same page but with a code. 

You have two methods for display the message the first one is to add messages directly on the class.

# Example :

## In the template
```php

<form method="post">
	<?php Bea_Form::get_instance()->display_contextual_or_post( 'my_action' ); ?>
	
	<label for="name" > Your name </label>
	<input type="text" name="name" id="name" required />
	<input type="hidden" name="action" value="my_action" />
	<?php wp_nonce_field( 'my-action' ); ?>
</form>
```

## In your controller

```php
add_action('wp', 'my_action_check'); 
function my_action_check() {
	$form = Bea_Form::get_instance();
	
	// First check the element is in post
	if ( 'my_action' !== $form::element_in_post( 'action' ) ) {
		return;
	}
	
	/**
	* Stop script and redirect to the same page with error
	*/
	if ( false === $form->check_nonce( 'my-action' ) ) {
		/**
		* This will redirect to : domain.com/my-url?code=0&action=my_action
		*/
		 wp_safe_redirect( add_query_arg( array( 'code' => 0, 'action' => $form::element_in_post( 'action' ) ), home_url( '/my-url' ) ) );
		 exit;
	}
	
	$name = trim( $form->element_in_post( 'my_action' ) );
	if( empty( $name ) ) {
		$form->add_general_error( __( 'There is an error on this form', 'my-text-domain' ) );
		$form->add_error( 'name', __( 'Please fill your name', 'my-text-domain' ) );
		return;
	}
	
	// Stop generation if there is any errors on the form
	if ( $form->have_form_error() ) {
		return;
	}
	
	/**
	 * Make your actions and then redirect to the same page
	 * This will redirect to : domain.com/my-url?code=1&action=my_action
	 */
	 wp_safe_redirect( add_query_arg( array( 'code' => 1, 'action' => $form::element_in_post( 'action' ) ), home_url( '/my-url' ) ) );
	 exit;
}
```

## Displaying the messages

And now we need to display the message on redirect, with the code and the action, they are not innocent.
You only have to add a filter with all the messages, the filter name is based on this pattern 

__bea\_form\_{action}\_messages__

```php
/**
 * Messages for the form options
 */
add_filter( 'bea_form_'.'my_action'.'_messages', 'my_form_messages' );

function my_form_messages() {
	return array(
		0 => array( 'type' => 'error', 'message' => 'Error !' ),
		1 => array( 'type' => 'success', 'message' => 'Success !' ),
		2 => array( 'type' => 'info', 'message' => 'Info !' ),
	);
}
```

The array key represents the error code and the values represent the types of messages you want to display and the messages.
