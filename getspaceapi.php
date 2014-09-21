<?php
/**
 * Plugin Name: Get space api Widget
 * Plugin URI: http://daveborghuis.nl/getspaceapi_widget
 * Description: A widget that get space status bij JSON.
 * Version: 0.1.0
 * Author: Dave Borghuis
 * Author URI: http://Daveborghuis.nl
 * More information on http://spaceapi.net
 * 
 * LICENCE : GLP3
 * See included GPL3Licence.txt for full text
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * Please share you code if you made any improvements to the code.
 *
 * If you want to use this code in your project do so but don't 
 * forget to include my name (Dave Borghuis).
 **/

// Settings in menu
add_action( 'admin_menu', 'getspaceapi' );

function getspaceapi() {
	add_options_page( 'Get SpaceApi Options', 'Get SpaceApi', 'manage_options', 
		'getspaceapi', 'getspaceapi_options' );
}

function getspaceapi_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	//Get stored values from database
	$json_url = 'getspaceapi_json_url';
	$json_url_val = get_option( $json_url );

	$json_icon = 'getspaceapi_json_icon';
	$json_icon_val = get_option( $json_icon );
	
	$json_open = 'getspaceapi_json_open';
	$json_open_val = html_entity_decode(htmlspecialchars_decode(get_option( $json_open )));

	$json_closed = 'getspaceapi_json_closed';
	$json_closed_val = html_entity_decode(htmlspecialchars_decode(get_option( $json_closed )));

    $hidden_field_name = 'js_submit_hidden';

	// See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

        // Read their posted value
        $json_url_val  = $_POST[ $json_url ];

        if (isset($_POST[ $json_icon ])) 
        		{$json_icon_val  = 'Y';} 
        	else 
        		{$json_icon_val  = 'N';};

        $json_open_val  = stripslashes($_POST[ $json_open ]);
        $json_closed_val  = stripslashes($_POST[ $json_closed ]);

        // Save the posted value in the database
        update_option( $json_url , $json_url_val  );
		update_option( $json_icon , $json_icon_val  );
		update_option( $json_open , $json_open_val  );
        update_option( $json_closed , $json_closed_val  );

        // Put an settings updated message on the screen
		echo '<div class="updated"><p><strong>'. _e('settings saved.', 'menu-test' ).'</strong></p></div>';

    }
    ?>

	<form name="form1" method="post" action="">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	<p><?php _e("JSON URL : ", 'get_spaceapi_url' ); ?> 
	<input type="url" name="<?php echo $json_url; ?>" value="<?php echo $json_url_val; ?>" size="50">
	</p><hr />

	<p><?php _e("Show icon : ", 'get_spaceapi_showicon' ); ?> 
	<input type="checkbox" name="<?php echo $json_icon; ?>" value="<?php echo $json_icon; ?>" <?php if( $json_icon_val == 'Y') echo "checked"; ?> size="20">
	</p>

	<p><?php _e("Open Text : ", 'get_spaceapi_open' ); ?> 
	<input type="textarea" name="<?php echo $json_open; ?>" value="<?php echo htmlentities($json_open_val); ?>" size="50">
	</p>

	<p><?php _e("Closed Text : ", 'get_spaceapi_closed' ); ?> 
	<input type="textarea" name="<?php echo $json_closed; ?>" value="<?php echo htmlentities($json_closed_val); ?>" size="50">
	</p><hr />

	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
	</form>
	</div>

	<?php
}
	

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1.0
 */
add_action( 'widgets_init', 'getspaceapi_load_widgets' );

/**
 * Register our widget.
 * 'getspaceapi_Widget' is the widget class used below.
 *
 * @since 0.1.0
 */
function getspaceapi_load_widgets() {
	register_widget( 'getspaceapi_Widget' );
}

/**
 * getspaceapi Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.
 *
 * @since 0.1.1
 */
class getspaceapi_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function getspaceapi_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'getspaceapi', 'description' => __('An get spaceapi widget that displays open/closed status by JSON', 'getspaceapi') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'getspaceapi-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'getspaceapi-widget', __('Get spaceapi Widget', 'getspaceapi'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		$json_url_name = 'getspaceapi_json_url';
		$json_url = get_option( $json_url_name );

		//Get the status and decode this
		$json = json_decode(file_get_contents($json_url),false);

		$json_icon_name = 'getspaceapi_json_icon';
		$json_icon_val = get_option( $json_icon_name );

		$json_open_name = 'getspaceapi_json_open';
		$json_open_val = html_entity_decode(htmlspecialchars_decode(get_option( $json_open_name )));
	
		$json_closed_name = 'getspaceapi_json_closed';
		$json_closed_val = html_entity_decode(htmlspecialchars_decode(get_option( $json_closed_name )));
	
		// end more options

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Print icon if available
		if($json_icon_val == 'Y'){
			echo '<div><img style="display: block; margin: 0 auto; border-style:none; box-shadow: none;"  border="0" src="'; //remove border
			//Print Open/Clode text
			if ( $json->state->open )  {
				echo $json->icon->open;
			} else {
				echo $json->icon->closed;
			};
			echo '""></div>';
		};

		//Print Open/Clode text
		if (isset($json_open_val) && isset($json_closed_val)) {
			echo "<div>";
			if ( $json->state->open )  {
				//Print Open text
				if ( $json_open_val )
					echo $json_open_val;
			} else {
				//Print Closed text
				if ( $json_closed_val )
					echo $json_closed_val;
			};
			echo "</div>";
		};

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('getspaceapi', 'getspaceapi'), 'name' => __('John Doe', 'getspaceapi'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

?>