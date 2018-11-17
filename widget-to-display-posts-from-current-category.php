<?php
// phpcs:disable Squiz.Commenting.FileComment.WrongStyle

/*
Plugin Name: Widget to Display Posts from Current Category
Description: This plugin allows you to display posts from the current category in the sidebar
Version: 0.2
Author: Alexander Kadyrov
Author URI: http://gruz0.ru/
Text Domain: widget-to-show-posts-in-current-category
License: MIT
License URI: https://github.com/gruz0/widget-to-display-posts-from-current-category/blob/master/LICENSE
*/

// phpcs:enable Squiz.Commenting.FileComment.WrongStyle

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function gruz0_subcategories_load_widget() {
	register_widget( 'Display_Posts_From_Current_Category' );
}

add_action( 'widgets_init', 'gruz0_subcategories_load_widget' );

class Display_Posts_From_Current_Category extends WP_Widget {
	const POSTS_PER_PAGE    = 10;
	const POST_TITLE_LENGTH = 30;

	function __construct() {
		parent::__construct(
			'gruz0_posts_in_current_widget',
			__( 'Current Category Posts', 'widget-to-show-posts-in-current-category' ),
			array( 'description' => __( 'Display posts from the current category', 'widget-to-show-posts-in-current-category' ) )
		);
	}

	public function widget( $args, $instance ) {
		if ( ! is_category() && ! is_single() ) {
			return;
		}

		// @todo: Add validations for $instance values to prevent attack
		if ( is_category() ) {
			$title         = apply_filters( 'widget_title', $instance['category_title'] );
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
		} else {
			$title = apply_filters( 'widget_single_title', $instance['single_title'] );

			// In a single post, display the defined category.
			global $post;
			$categories = implode( ',', wp_get_post_categories( $post->ID ) );
		}

		// @todo: Make order of posts variable
		// @todo: Display posts randomly
		$the_query = new WP_Query(
			array(
				'cat'            => $categories,
				'order'          => 'ASC',
				'posts_per_page' => $instance['posts_per_page'],
			)
		);

		if ( $the_query->have_posts() ) {
			echo $args['before_widget'];

			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo '<ul class="category-posts">';

			// @todo: Add some style capability
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$short_title = ( mb_strlen( get_the_title() ) > $instance['length'] )
					? mb_trim( mb_substr( get_the_title(), 0, $instance['length'] ) ) . '...'
					: get_the_title();

				echo '<li><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . $short_title . '</a></li>';
			}

			echo '</ul>';
			echo $args['after_widget'];
		}
	}

	/**
	 * Widget Settings Form
	 */
	public function form( $instance ) {
		// Define defaults.
		$category_title = isset( $instance['category_title'] ) ? $instance['category_title'] : __( 'From the same category', 'widget-to-show-posts-in-current-category' );
		$single_title   = isset( $instance['single_title'] ) ? $instance['single_title'] : __( 'More posts from this section', 'widget-to-show-posts-in-current-category' );
		$posts_per_page = isset( $instance['posts_per_page'] ) ? absint( $instance['posts_per_page'] ) : self::POSTS_PER_PAGE;
		$length         = isset( $instance['length'] ) ? absint( $instance['length'] ) : self::POST_TITLE_LENGTH;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'category_title' ); ?>"><?php _e( 'Category page widget title', 'widget-to-show-posts-in-current-category' ); ?>:</label>
			<input
				class="widefat" id="<?php echo $this->get_field_id( 'category_title' ); ?>"
				name="<?php echo $this->get_field_name( 'category_title' ); ?>" type="text"
				value="<?php echo esc_attr( $category_title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'single_title' ); ?>"><?php _e( 'Single post widget title', 'widget-to-show-posts-in-current-category' ); ?>:</label>
			<input
				class="widefat" id="<?php echo $this->get_field_id( 'single_title' ); ?>"
				name="<?php echo $this->get_field_name( 'single_title' ); ?>" type="text"
				value="<?php echo esc_attr( $single_title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Number of posts to display', 'widget-to-show-posts-in-current-category' ); ?>:</label>
			<input
				class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"
				name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text"
				value="<?php echo esc_attr( $posts_per_page ); ?>" maxlength="2" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'length' ); ?>"><?php _e( 'Post title length', 'widget-to-show-posts-in-current-category' ); ?>:</label>
			<input
				class="widefat" id="<?php echo $this->get_field_id( 'length' ); ?>"
				name="<?php echo $this->get_field_name( 'length' ); ?>" type="text"
				value="<?php echo esc_attr( $length ); ?>" maxlength="3" />
		</p>
		<?php
	}

	/**
	 * Save or Update old instances with new one
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                   = array();
		$instance['category_title'] = strip_tags( mb_trim( $new_instance['category_title'] ) );
		$instance['single_title']   = strip_tags( mb_trim( $new_instance['single_title'] ) );

		if ( empty( $new_instance['posts_per_page'] ) ) {
			$instance['posts_per_page'] = self::POSTS_PER_PAGE;
		} else {
			$new_posts_per_page         = absint( strip_tags( $new_instance['posts_per_page'] ) );
			$instance['posts_per_page'] = 0 === $new_posts_per_page ? self::POSTS_PER_PAGE : $new_posts_per_page;
		}

		if ( empty( $new_instance['length'] ) ) {
			$instance['length'] = self::POST_TITLE_LENGTH;
		} else {
			$new_length         = absint( strip_tags( $new_instance['length'] ) );
			$instance['length'] = 0 === $new_length ? self::POST_TITLE_LENGTH : $new_length;
		}

		return $instance;
	}
}

// Function to fix multibyte.
if ( ! function_exists( 'mb_trim' ) ) {
	function mb_trim( $string ) {
		return preg_replace( '/(^\s+)|(\s+$)/us', '', $string );
	}
}

if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}
