<?php 
// регистрируем поддержку новых возможностей 
if ( ! function_exists( 'universal_theme_setup' ) ) :
	function universal_theme_setup() {
		// выводим тег title
		add_theme_support( 'title-tag' ); 

		//добавляем возможность устанавливать миниатюру записи
		add_theme_support( 'post-thumbnails', array( 'post' ) );  
		
		//динамически выводим логотип из админ панели
		add_theme_support( 'custom-logo', [
			'width'       => 163,
			'flex-height' => true,
			'header-text' => 'universal-logo',
			'unlink-homepage-logo' => false, // WP 5.5
		] );

		//регистрируем меню
		register_nav_menus( [
			'header_menu' => 'menu_in_header',
			'footer_menu' => 'menu_in_footer'
		] );
	




	}
endif;
// вешаем функцию 'universal_theme_setup' на хук-событие 'after_setup_theme'
add_action( 'after_setup_theme', 'universal_theme_setup' );

/**
 * Регистрируем виджет
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function universal_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Main Sidebar', 'universal-theme' ),
			'id'            => 'main-sidebar',
			'description'   => esc_html__( 'Добавить виджет.', 'universal-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Recent Posts Sidebar', 'universal-theme' ),
			'id'            => 'recent-posts-sidebar',
			'description'   => esc_html__( 'Добавить виджет.', 'universal-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'universal_theme_widgets_init' );


/**
 * Добавление нового виджета Download Widget.
 */
class Download_Widget extends WP_Widget {

	// Регистрация виджета используя основной класс
	function __construct() {
		// вызов конструктора выглядит так:
		// __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
		parent::__construct(
			'download_widget', // ID виджета, если не указать (оставить ''), то ID будет равен названию класса в нижнем регистре
			'Полезные файлы',
			array( 'description' => 'Скачивание файлов', 'classname' => 'widget-download' )
		);

		// скрипты/стили виджета, только если он активен
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action('wp_enqueue_scripts', array( $this, 'add_download_widget_scripts' ));
			add_action('wp_head', array( $this, 'add_download_widget_style' ) );
		}
	}

	/**
	 * Вывод виджета во Фронт-энде
	 *
	 * @param array $args     аргументы виджета.
	 * @param array $instance сохраненные данные из настроек
	 */
	function widget( $args, $instance ) {
		$title = $instance['title'];
		$description = $instance['description'];
		$link = $instance['link'];

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( ! empty( $description ) ) {
			echo '<p class="widget-description">' . $description . '</p>';
		}
		if ( ! empty( $link ) ) {
			echo '<a target="_blanc" class="widget-link" href="' . $link . '">
			<svg class="icon-download" width="17" height="17" fill="#fff">
				<use xlink:href="' . get_template_directory_uri() . '/assets/images/sprite.svg#download"></use>
			</svg>
			<span>Скачать</span>
			</a>';
		}
		echo $args['after_widget'];
	}

	/**
	 * Админ-часть виджета
	 *
	 * @param array $instance сохраненные данные из настроек
	 */
	function form( $instance ) {
		$title = @ $instance['title'] ?: 'Введите заголовок';
		$description = @ $instance['description'] ?: 'Введите описание виджета';
		$link = @ $instance['link'] ?: 'Укажите ссылку';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Заголовок:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Описание:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" value="<?php echo esc_attr( $description ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Ссылка:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>">
		</p>
		<?php 
	}

	/**
	 * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance новые настройки
	 * @param array $old_instance предыдущие настройки
	 *
	 * @return array данные которые будут сохранены
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
		$instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';

		return $instance;
	}

	// скрипт виджета
	function add_download_widget_scripts() {
		// фильтр чтобы можно было отключить скрипты
		if( ! apply_filters( 'show_download_widget_script', true, $this->id_base ) )
			return;

		$theme_url = get_stylesheet_directory_uri();

		wp_enqueue_script('download_widget_script', $theme_url .'/download_widget_script.js' );
	}

	// стили виджета
	function add_download_widget_style() {
		// фильтр чтобы можно было отключить стили
		if( ! apply_filters( 'show_download_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">
			.my_widget a{ display:inline; }
		</style>
		<?php
	}

} 
// конец класса Download_Widget

// регистрация Download_Widget в WordPress
function register_download_widget() {
	register_widget( 'Download_Widget' );
}
add_action( 'widgets_init', 'register_download_widget' );

/**
 * Добавление нового виджета Social Links Widget.
 */
class Social_Links_Widget extends WP_Widget {

	// Регистрация виджета используя основной класс
	function __construct() {
		// вызов конструктора выглядит так:
		// __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
		parent::__construct(
			'social_links_widget', // ID виджета, если не указать (оставить ''), то ID будет равен названию класса в нижнем регистре: social_links_widget
			'Социальный ссылки',
			array( 'description' => 'Описание виджета', 'classname' => 'widget-social' )
		);

		// скрипты/стили виджета, только если он активен
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action('wp_enqueue_scripts', array( $this, 'add_social_links_widget_scripts' ));
			add_action('wp_head', array( $this, 'add_social_links_widget_style' ) );
		}
	}

	/**
	 * Вывод виджета во Фронт-энде
	 *
	 * @param array $args     аргументы виджета.
	 * @param array $instance сохраненные данные из настроек
	 */
	function widget( $args, $instance ) {
		$title = $instance['title'];
		$facebook = $instance['facebook'];
		$twitter = $instance['twitter'];
		$youtube = $instance['youtube'];
		$instagram = $instance['instagram'];
		

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		echo '<ul class="widget-links-list">';

		if ( ! empty( $facebook ) ) {
			echo '<li><a target="_blanc" class="widget-link facebook" href="' . $facebook . '">
			<svg class="icon-facebook" width="20" height="20" fill="#fff">
			<use xlink:href="' . get_template_directory_uri() . '/assets/images/sprite.svg#facebook"></use>
			</svg>
			</a></li>';
		}

		if ( ! empty( $twitter ) ) {
			echo '<li><a target="_blanc" class="widget-link twitter" href="' . $twitter . '">
			<svg class="icon-twitter" width="20" height="20" fill="#fff">
			<use xlink:href="' . get_template_directory_uri() . '/assets/images/sprite.svg#twitter"></use>
			</svg>
			</a></li>';
		}

		if ( ! empty( $youtube ) ) {
			echo '<li><a target="_blanc" class="widget-link youtube" href="' . $youtube . '">
			<svg class="icon-youtube" width="20" height="20" fill="#fff">
			<use xlink:href="' . get_template_directory_uri() . '/assets/images/sprite.svg#youtube"></use>
			</svg>
			</a></li>';
		}
		
		if ( ! empty( $instagram ) ) {
			echo '<li><a target="_blanc" class="widget-link instagram" href="' . $instagram . '">
			<svg class="icon-instagram" width="20" height="20" fill="#fff">
			<use xlink:href="' . get_template_directory_uri() . '/assets/images/sprite.svg#instagram"></use>
			</svg>
			</a></li>';
		}

		echo '</ul>';

		echo $args['after_widget'];
	}

	/**
	 * Админ-часть виджета
	 *
	 * @param array $instance сохраненные данные из настроек
	 */
	function form( $instance ) {
		$title = @ $instance['title'] ?: 'Заголовок';
		$facebook = @ $instance['facebook'] ?: 'Укажите ссылку на facebook';
		$twitter = @ $instance['twitter'] ?: 'Укажите ссылку twitter';
		$youtube = @ $instance['youtube'] ?: 'Укажите ссылку youtube';
		$instagram = @ $instance['instagram'] ?: 'Укажите ссылку instagram';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Заголовок:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'facebook:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'twitter:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo esc_attr( $twitter ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e( 'youtube:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" type="text" value="<?php echo esc_attr( $youtube ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'instagram:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text" value="<?php echo esc_attr( $instagram ); ?>">
		</p>
		<?php 
	}

	/**
	 * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance новые настройки
	 * @param array $old_instance предыдущие настройки
	 *
	 * @return array данные которые будут сохранены
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['facebook'] = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
		$instance['twitter'] = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';
		$instance['youtube'] = ( ! empty( $new_instance['youtube'] ) ) ? strip_tags( $new_instance['youtube'] ) : '';
		$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? strip_tags( $new_instance['instagram'] ) : '';

		return $instance;
	}

	// скрипт виджета
	function add_social_links_widget_scripts() {
		// фильтр чтобы можно было отключить скрипты
		if( ! apply_filters( 'show_social_links_widget_script', true, $this->id_base ) )
			return;

		$theme_url = get_stylesheet_directory_uri();

		wp_enqueue_script('social_links_widget_script', $theme_url .'/social_links_widget_script.js' );
	}

	// стили виджета
	function add_social_links_widget_style() {
		// фильтр чтобы можно было отключить стили
		if( ! apply_filters( 'show_social_links_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">
			.my_widget a{ display:inline; }
		</style>
		<?php
	}

} 
// конец класса Social_Links_Widget

// регистрация Social_Links_Widget в WordPress
function register_social_links_widget() {
	register_widget( 'Social_Links_Widget' );
}
add_action( 'widgets_init', 'register_social_links_widget' );


/**
 * Добавление нового виджета Recent Posts Widget.
 */
class Recent_Posts_Widget extends WP_Widget {

	// Регистрация виджета используя основной класс
	function __construct() {
		// вызов конструктора выглядит так:
		// __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
		parent::__construct(
			'recent_posts_widget', // ID виджета, если не указать (оставить ''), то ID будет равен названию класса в нижнем регистре: recent_posts_widget
			'Свежие записи',
			array( 'description' => 'Вывод последних постов', 'classname' => 'widget-recent' )
		);

		// скрипты/стили виджета, только если он активен
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action('wp_enqueue_scripts', array( $this, 'add_recent_posts_widget_scripts' ));
			add_action('wp_head', array( $this, 'add_recent_posts_widget_style' ) );
		}
	}

	/**
	 * Вывод виджета во Фронт-энде
	 *
	 * @param array $args     аргументы виджета.
	 * @param array $instance сохраненные данные из настроек
	 */
	function widget( $args, $instance ) {
		$title = $instance['title'];
		$count = $instance['count'];
		

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			if (! empty ( $count )) {

			echo $args['before_title'] . $title . $args['after_title'];

			echo '<div class="widget-recent-wrapper">';
				
			global $post;

			$myposts = get_posts([ 
				'numberposts' => $count
			]);

			if( $myposts ){
				foreach( $myposts as $post ){
					setup_postdata( $post );
					?>
						<a href="<?php the_permalink( ); ?>" class="widget-recent-permalink">
							<img src="<?php the_post_thumbnail_url('thumbnail');?>" alt="<?php the_title(); ?>">
							<div class="widget-recent-info">
								<h4><?php echo mb_strimwidth(get_the_title(), 0, 35, "...");?></h4>
								<span>
									<?php $time_diff = human_time_diff( get_post_time('U'), current_time('timestamp') );
									echo "$time_diff назад";?>
								</span>
							</div>
						</a>
					<?php 
				}
			} else {
				// Постов не найдено
			}
			wp_reset_postdata(); // Сбрасываем $post
			}
		}
		
		echo '</div>';

		echo '<span class="farther"><a href="#">Read more</a></span>';

		echo $args['after_widget'];
	}

	/**
	 * Админ-часть виджета
	 *
	 * @param array $instance сохраненные данные из настроек
	 */
	function form( $instance ) {
		$title = @ $instance['title'] ?: 'Заголовок';
		$count = @ $instance['count'] ?: 'Укажите количество выводимых постов';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Заголовок:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Количество постов:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>">
		</p>
		<?php 
	}

	/**
	 * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance новые настройки
	 * @param array $old_instance предыдущие настройки
	 *
	 * @return array данные которые будут сохранены
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';

		return $instance;
	}

	// скрипт виджета
	function add_recent_posts_widget_scripts() {
		// фильтр чтобы можно было отключить скрипты
		if( ! apply_filters( 'show_recent_posts_widget_script', true, $this->id_base ) )
			return;

		$theme_url = get_stylesheet_directory_uri();

		wp_enqueue_script('recent_posts_widget_script', $theme_url .'/recent_posts_widget_script.js' );
	}

	// стили виджета
	function add_recent_posts_widget_style() {
		// фильтр чтобы можно было отключить стили
		if( ! apply_filters( 'show_recent_posts_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">
			.my_widget a{ display:inline; }
		</style>
		<?php
	}

} 
// конец класса Recent_Posts_Widget

// регистрация Recent_Posts_Widget в WordPress
function register_recent_posts_widget() {
	register_widget( 'Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'register_recent_posts_widget' );


// подключаем скрипты и стили
function enqueue_universal_style() {
	wp_enqueue_style( 'Roboto-Slab', '//fonts.googleapis.com/css2?family=Roboto+Slab:wght@700&display=swap'); //подключаем шрифт Roboto
	wp_enqueue_style( 'style', get_stylesheet_uri() ); //подключаем syle.css из папки с темой
	wp_enqueue_style( 'universal-theme-style', get_template_directory_uri( ) . '/assets/css/universal-theme.css', 'style', time()); //подключаем свои стили	
}
// вешаем функцию 'enqueue_universal_style' на хук-событие 'wp_enqueue_scripts'
add_action( 'wp_enqueue_scripts', 'enqueue_universal_style' );

// отключаем создание миниатюр файлов для указанных размеров
add_filter( 'intermediate_image_sizes', 'delete_intermediate_image_sizes' );
function delete_intermediate_image_sizes( $sizes ){
	// размеры которые нужно удалить
	return array_diff( $sizes, [
		'medium_large',
		'large',
		'1536x1536',
		'2048x2048',
	] );
}

add_filter('widget_tag_cloud_args', function( $args ){
	$args['unit'] = 'px';
	$args['smallest'] = 14;
	$args['largest'] = 14;
	$args['number'] = 10;
	$args['orderby'] = 'count';

	return $args;
});