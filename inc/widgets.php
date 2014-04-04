<?php
/**
 * sem_widgets
 *
 * @package Semiologic Reloaded
 **/

class sem_widgets {
	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.5.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Returns the instance.
	 *
	 * @since  0.5.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {

		$this->load_classes();

        add_action('widgets_init', array($this, 'register'));

        if ( is_admin() ) {
        	add_action('admin_print_scripts-widgets.php', array($this, 'admin_scripts'));
        	add_action('admin_print_styles-widgets.php', array($this, 'admin_styles'));
        }

        add_action('widget_tag_cloud_args', array($this, 'tag_cloud_args'));
        add_filter('widget_display_callback', array($this, 'widget_display_callback'), 10, 3);

        wp_cache_add_non_persistent_groups(array('sem_header'));
        wp_cache_add_non_persistent_groups(array('nav_menu_roots', 'page_ancestors', 'page_children'));
        wp_cache_add_non_persistent_groups(array('widget_queries', 'pre_flush_post'));
    }

	/**
	* load_classes()
	*
	* @return void
	**/

	function load_classes() {
		include sem_path . '/inc/widgets-header.php';
		include sem_path . '/inc/widgets-footer.php';
		include sem_path . '/inc/widgets-entries.php';
		include sem_path . '/inc/widgets-content.php';
	}
	    /**
	 * register()
	 *
	 * @return void
	 **/

	function register() {
		register_widget('entry_header');
		register_widget('entry_content');
		register_widget('entry_categories');
		register_widget('entry_tags');
		register_widget('entry_comments');
		register_widget('entry_navigation');
		register_widget('blog_header');
		register_widget('blog_footer');
		register_widget('header_boxes');
		register_widget('footer_boxes');
		register_widget('header');
		register_widget('navbar');
		register_widget('footer');
		register_widget('sem_breadcrumbs');
	} # register()
	
	
	/**
	 * admin_scripts()
	 *
	 * @return void
	 **/

	function admin_scripts() {
		if ( !class_exists('nav_menu') ) {
			$folder = sem_url . '/js';
			wp_enqueue_script('nav-menus', $folder . '/admin.js', array('jquery-ui-sortable'),  '20090903', true);

			add_action('admin_footer', array('sem_nav_menu', 'admin_footer'));
		}
	} # admin_scripts()
	
	
	/**
	 * admin_styles()
	 *
	 * @return void
	 **/

	function admin_styles() {
		$folder = sem_url . '/css';
		wp_enqueue_style('nav-menus', $folder . '/admin.css', null, '20090903');
	} # admin_styles()
	
	
	/**
	 * tag_cloud_args()
	 *
	 * @param array $args
	 * @return array $args
	 **/

	function tag_cloud_args($args) {
		$args = wp_parse_args($args, array('smallest' => '.8', 'largest' => '1.6', 'unit' => 'em'));
		return $args;
	} # tag_cloud_args()
	
	
	/**
	 * widget_display_callback()
	 *
	 * @param array $instance widget settings
	 * @param object $widget
	 * @param array $args sidebar settings
	 * @return array $instance
	 **/

	function widget_display_callback($instance, $widget, $args) {
		if ( $instance === false )
			return $instance;
		
		switch ( get_class($widget) ) {
		case 'WP_Widget_Calendar':
			return sem_widgets::calendar_widget($instance, $args);
		case 'WP_Widget_Search':
			return sem_widgets::search_widget($instance, $args);
		default:
			return $instance;
		}
	} # widget_display_callback()
	
	
	/**
	 * calendar_widget()
	 *
	 * @param array $instance widget args
	 * @param array $args sidebar args
	 * @return false
	 **/

	function calendar_widget($instance, $args) {
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);
		
		ob_start();
		get_calendar();
		$calendar = ob_get_clean();
		
		$calendar = str_replace('<table id="wp-calendar"', '<table class="wp-calendar"', $calendar);
		
		$title = apply_filters('widget_title', $title);
		
		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
		
		echo $calendar;
		
		echo $after_widget;
		
		return false;
	} # calendar_widget()
	
	
	/**
	 * search_widget()
	 *
	 * @param array $instance widget args
	 * @param array $args sidebar args
	 * @return false
	 **/

	static function search_widget($instance, $args) {
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);
		
		if ( is_search() )
			$query = apply_filters('the_search_form', get_search_query());
		else
			$query = '';
		
		$title = apply_filters('widget_title', $title);
		
		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
		
		echo '<form method="get"'
				. ' action="' . esc_url(user_trailingslashit(home_url())) . '"'
				. ' class="searchform" name="searchform"'
				. '>'
			. '<input type="text" class="s" name="s"'
				. ' value="' . esc_attr($query) . '"'
				. ' />'
			. ( in_array($args['id'], array('sidebar-1', 'sidebar-2') )
				? "<br />\n"
				: ''
				)
			. '<input type="submit" class="go button submit" value="' . esc_attr__('Search', 'sem-reloaded') . '" />'
			. '</form>';
		
		echo $after_widget;
		
		return false;
	} # search_widget()
} # sem_widgets


//$sem_widgets = new sem_widgets();
sem_widgets::get_instance();
