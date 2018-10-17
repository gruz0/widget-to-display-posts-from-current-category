<?php
/*
Plugin Name: Widget to Display Posts in Current Category
Description: The plugin allows you to display posts from the current category in the sidebar
Version: 0.1
Author: Alexander Kadyrov
Author URI: http://gruz0.ru/
*/

class gruz0_posts_in_current_category_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // ID виджета
        'gruz0_posts_in_current_widget',

        // Название виджета в административном интерфейсе WP
        __( 'Записи текущей категории', 'gruz0_subcategories_widget_domain' ),

        // Описание виджета
        array( 'description' => 'Выводит записи текущей категории' )
        );
    }

    // Создание виджета на фронт-енде
    public function widget( $args, $instance ) {

        // Виджет работает только на странице категорий и записях
        if ( !is_category() && !is_single() ) return;

        if ( is_category() ) {
            $title = apply_filters( 'widget_title', $instance[ 'category_title' ] );
        } else if ( is_single() ) {
            $title = apply_filters( 'widget_single_title', $instance[ 'single_title' ] );
        }

        if ( is_category() ) {
            $subcategories = array();
            $cat = get_query_var( 'cat' );
            $categories = get_categories( 'child_of=' . $cat );

            if ( $categories ) {
                foreach( $categories as $category ) {
                    $subcategories[] = $category->term_id;
                }
            }

            $categories = array_unique( $subcategories );
            $categories = $categories ? implode( ',', $categories ) : $cat;

        } else if ( is_single() ) {
            // В единичной записи выводим содержимое раздела только если определена одна категория
            global $post;
            $categories = implode( ',', wp_get_post_categories( $post->ID ) );
        }

        $the_query = new WP_Query( array(
            'cat'            => $categories,
            'order'          => 'ASC',
            'posts_per_page' => $instance[ 'posts_per_page' ]
        ) );

        if ( $the_query->have_posts() ) {
            echo $args['before_widget'];
            if ( !empty( $title ) ) {
                echo $args['before_title'] . $title . $args[ 'after_title' ];
            }

            echo '<ul class="category-posts">';
            while ( $the_query->have_posts() ) : $the_query->the_post();
                echo '<li><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . ( ( mb_strlen( get_the_title() ) > $instance[ 'length' ] ) ? mb_trim( mb_substr( get_the_title(), 0, $instance[ 'length' ] ) )."..." : get_the_title()) . '</a></li>';
            endwhile;
            echo '</ul>';
            echo $args[ 'after_widget' ];
        }
    }

    // Настройки виджета
    public function form( $instance ) {
        // Получаем текущие значения полей
        $category_title = isset( $instance[ 'category_title' ] ) ? $instance[ 'category_title' ] : __( 'Содержание раздела', 'gruz0_subcategories_widget_domain' );
        $single_title = isset( $instance[ 'single_title' ] ) ? $instance[ 'single_title' ] : __( 'Еще записи из этого раздела', 'gruz0_subcategories_widget_domain' );
        $posts_per_page = isset( $instance[ 'posts_per_page' ] ) ? $instance[ 'posts_per_page' ] : 10;
        $length = isset( $instance[ 'length' ] ) ? $instance[ 'length' ] : 30;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'category_title' ); ?>">Заголовок виджета для категорий:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'category_title' ); ?>" name="<?php echo $this->get_field_name( 'category_title' ); ?>" type="text" value="<?php echo esc_attr( $category_title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'single_title' ); ?>">Заголовок виджета для записи:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'single_title' ); ?>" name="<?php echo $this->get_field_name( 'single_title' ); ?>" type="text" value="<?php echo esc_attr( $single_title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Выводить количество записей:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>" maxlength="2" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'length' ); ?>"><?php _e( 'Максимальное количество символов:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'length' ); ?>" name="<?php echo $this->get_field_name( 'length' ); ?>" type="text" value="<?php echo esc_attr( $length ); ?>" maxlength="3" />
        </p>
        <?php
    }

    // Обновление настроек виджета
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'category_title' ] = ( ! empty( $new_instance[ 'category_title' ] ) ) ? strip_tags( $new_instance[ 'category_title' ] ) : '';
        $instance[ 'single_title' ] = ( ! empty( $new_instance[ 'single_title' ] ) ) ? strip_tags( $new_instance[ 'single_title' ] ) : '';
        $instance[ 'posts_per_page' ] = ( ! empty( $new_instance[ 'posts_per_page' ] ) ) ? intval(strip_tags( $new_instance[ 'posts_per_page' ] ) ) : '10';
        $instance[ 'length' ] = ( ! empty( $new_instance[ 'length' ] ) ) ? intval(strip_tags( $new_instance[ 'length' ] ) ) : '30';
        return $instance;
    }
}

// Регистрируем виджет
function gruz0_subcategories_load_widget() {
    register_widget( 'gruz0_posts_in_current_category_widget' );
}
add_action( 'widgets_init', 'gruz0_subcategories_load_widget' );

// Вспомогательная функция для работы с мультибайтовыми строками
if (!function_exists("mb_trim"))
{
    function mb_trim( $string )
    {
        $string = preg_replace( "/(^\s+)|(\s+$)/us", "", $string );
        return $string;
    }
}
