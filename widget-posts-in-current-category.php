<?php
/*
Plugin Name: Widget to Show Posts in Current Category
Description: The plugin allows you to display records from the current category in the sidebar
Version: 0.2
Author: Alexander Gruzov
Author URI: http://gruz0.ru/
Contributor: RwkY

*/


//load the widget
function gruz0_subcategories_load_widget() {
	register_widget( 'gruz0_posts_in_current_category_widget' );
}

add_action( 'widgets_init', 'gruz0_subcategories_load_widget' );


class gruz0_posts_in_current_category_widget extends WP_Widget {

	/**
	 * gruz0_posts_in_current_category_widget constructor.
	 */
	function __construct() {
		parent::__construct(
		// unique ID in WP system
			'gruz0_posts_in_current_widget',

			// Title of the widget
			__( 'Current category records', 'gruz0_subcategories_widget_domain' ),

			// Description of the widget
			array( 'description' => 'Display current category description' )
		);
	}

	// Content of the wdiget
	public function widget( $args, $instance ) {

		// verify for current category and single post
		if ( ! is_category() && ! is_single() ) {
			return;
		}

		if ( is_category() ) {
			$title = apply_filters( 'widget_title', $instance['category_title'] );
		} else if ( is_single() ) {
			$title = apply_filters( 'widget_single_title', $instance['single_title'] );
		}

		if ( is_category() ) {
			$subcategories = array();
			$cat           = get_query_var( 'cat' );
			$categories    = get_categories( 'child_of=' . $cat );

			if ( $categories ) {
				foreach ( $categories as $category ) {
					$subcategories[] = $category->term_id;
				}
			}

			$categories = array_unique( $subcategories );
			$categories = $categories ? implode( ',', $categories ) : $cat;

		} else if ( is_single() ) {
			// in a single post, display the defined category
			global $post;
			$categories = implode( ',', wp_get_post_categories( $post->ID ) );
		}

		//@todo: make order of posts variable
		$the_query = new WP_Query( array(
			'cat'            => $categories,
			'order'          => 'ASC',
			'posts_per_page' => $instance['posts_per_page']
		) );

		if ( $the_query->have_posts() ) {
			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo '<ul class="category-posts">';
			//@todo add some style capability
			while ( $the_query->have_posts() ) : $the_query->the_post();
				echo '<li><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . ( ( mb_strlen( get_the_title() ) > $instance['length'] ) ? mb_trim( mb_substr( get_the_title(), 0, $instance['length'] ) ) . "..." : get_the_title() ) . '</a></li>';
			endwhile;
			echo '</ul>';
			echo $args['after_widget'];
		}
	}

	// Settings
	public function form( $instance ) {
		// Define defaults
		$category_title = isset( $instance['category_title'] ) ? $instance['category_title'] : __( 'From the same category', 'gruz0_subcategories_widget_domain' );
		$single_title   = isset( $instance['single_title'] ) ? $instance['single_title'] : __( 'More posts from this section', 'gruz0_subcategories_widget_domain' );
		$posts_per_page = isset( $instance['posts_per_page'] ) ? $instance['posts_per_page'] : 10;
		$length         = isset( $instance['length'] ) ? $instance['length'] : 30;
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'category_title' ); ?>"><?php _e( 'Widget title' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'category_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'category_title' ); ?>" type="text"
                   value="<?php echo esc_attr( $category_title ); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'single_title' ); ?>"><?php _e( 'Single title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'single_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'single_title' ); ?>" type="text"
                   value="<?php echo esc_attr( $single_title ); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Number of posts:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"
                   name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text"
                   value="<?php echo esc_attr( $posts_per_page ); ?>" maxlength="2"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'length' ); ?>"><?php _e( 'Character length:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'length' ); ?>"
                   name="<?php echo $this->get_field_name( 'length' ); ?>" type="text"
                   value="<?php echo esc_attr( $length ); ?>" maxlength="3"/>
        </p>
		<?php
	}

	// Save or Update old instances with new one
	public function update( $new_instance, $old_instance ) {
		$instance                   = array();
		$instance['category_title'] = ( ! empty( $new_instance['category_title'] ) ) ? strip_tags( $new_instance['category_title'] ) : '';
		$instance['single_title']   = ( ! empty( $new_instance['single_title'] ) ) ? strip_tags( $new_instance['single_title'] ) : '';
		$instance['posts_per_page'] = ( ! empty( $new_instance['posts_per_page'] ) ) ? intval( strip_tags( $new_instance['posts_per_page'] ) ) : '10';
		$instance['length']         = ( ! empty( $new_instance['length'] ) ) ? intval( strip_tags( $new_instance['length'] ) ) : '30';

		return $instance;
	}
}

// Function to fix multibyte
if ( ! function_exists( "mb_trim" ) ) {
	function mb_trim( $string ) {
		$string = preg_replace( "/(^\s+)|(\s+$)/us", "", $string );

		return $string;
	}
}