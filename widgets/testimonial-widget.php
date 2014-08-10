<?php

add_action( 'widgets_init', 'load_testimonial_widget' );
function load_testimonial_widget() {
	register_widget( 'pxls_testimonial_widget' );
}

class pxls_testimonial_widget extends WP_Widget {
	function pxls_testimonial_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'testimonial', 'description' => 'A widget that shows a testimonial' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'testimonial-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'testimonial-widget', 'Testimonial Widget', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$quote = $instance['quote'];
		$name = $instance['name'];
		$company = $instance['company'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title ) echo $before_title . $title . $after_title;

		/* Display quote from widget settings. */
		if ( $quote ) echo '<p id="quote">' . $quote . '</p>';
		if ( $name ) echo '<p id="name">' . $name . '</p>';
		if ( $company ) echo '<p id="company">' . $company . '</p>';

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quote'] = strip_tags( $new_instance['quote'] );
		$instance['name'] = $new_instance['name'];
		$instance['company'] = $new_instance['company'];

		return $instance;
	}
	
	
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'quote' => 'Lorum ipsum dolor sit amet, consectetur adipscing elit. nullam dapi-bus venenatis lacus, eget viverra mauris.', 'name' => 'John Smith', 'company' => 'RD Managing Director' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<!--<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>-->

		<p>
			<label for="<?php echo $this->get_field_id( 'quote' ); ?>">Testimonial:</label>
			<textarea id="<?php echo $this->get_field_id( 'quote' ); ?>" name="<?php echo $this->get_field_name( 'quote' ); ?>" style="width:100%;"><?php echo $instance['quote']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>">Name:</label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'company' ); ?>">Company:</label>
			<input id="<?php echo $this->get_field_id( 'company' ); ?>" name="<?php echo $this->get_field_name( 'company' ); ?>" value="<?php echo $instance['company']; ?>" style="width:100%;" />
		</p>

<?php
	}
} ?>