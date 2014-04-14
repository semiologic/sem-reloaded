<?php
/**
 * footer
 *
 * @package Semiologic Reloaded
 **/

if ( !class_exists('sem_nav_menu') )
	include sem_path . '/inc/widgets-navmenu.php';

class footer extends sem_nav_menu {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Footer: Nav Menu', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'footer',
			'description' => __('The footer\'s navigation menu, with an optional copyright notice. Must be placed in the footer area.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('footer', $widget_name, $widget_ops, $control_ops);

		$this->ul_menu_class = "footer_menu";

		parent::__construct();
	} # footer()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_footer' )
			return;

		$instance = wp_parse_args($instance, footer::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$footer_class = '';
		if ( $sep )
			$footer_class .= ' sep_nav';
		if ( $float_footer && $copyright ) {
			$footer_class .= ' float_nav';
			if ( $sep )
				$footer_class .= ' float_sep_nav';
		}

		echo '<div id="footer" class="wrapper' . $footer_class . '" role="contentinfo" itemscope="itemscope" itemtype="http://schema.org/WPFooter">' . "\n";

		echo '<div id="footer_top"><div class="hidden"></div></div>' . "\n";

		echo '<div id="footer_bg">' . "\n"
			. '<div class="wrapper_item">' . "\n"
			. '<div class="pad">' . "\n";

		echo '<div id="footer_nav" class="footer_nav inline_menu menu" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">';

		sem_nav_menu::widget($args, $instance);

		echo '</div><!-- footer_nav -->' . "\n";

		$year = date('Y');
		$site_name = strip_tags(get_option('blogname'));

		$copyright = sprintf($copyright, $site_name, $year);

		if ( $copyright ) {
			echo '<div id="copyright_notice">';
			echo $copyright;
			echo '</div><!-- #copyright_notice -->' . "\n";
		}

		echo '<div class="spacer"></div>' . "\n"
			. '</div>' . "\n"
			. '</div>' . "\n"
			. '</div>' . "\n";

		echo '<div id="footer_bottom"><div class="hidden"></div></div>' . "\n";

		echo '</div><!-- footer -->' . "\n";

		global $did_footer;
		$did_footer = true;
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance = parent::update($new_instance, $old_instance);
		$instance['float_footer'] = isset($new_instance['float_footer']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['copyright'] = trim($new_instance['copyright']);
		} else {
			$instance['copyright'] = $old_instance['copyright'];
		}

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = footer::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-reloaded') . '</h3>' . "\n";

		foreach ( array('copyright') as $field ) {
			echo '<p>'
				. '<label for="' . $this->get_field_id($field) . '">'
				. '<code>' . htmlspecialchars($defaults[$field], ENT_QUOTES, get_option('blog_charset')) . '</code>'
				. ( isset($defaults[$field . '_label'])
					? '<br />' . "\n" . '<code>' . $defaults[$field . '_label'] . '</code>'
					: ''
					)
				. '</label>'
				. '<br />' . "\n"
				. '<textarea class="widefat" cols="20" rows="4"'
				. ' id="' . $this->get_field_id($field) . '"'
				. ' name="' . $this->get_field_name($field) . '"'
				. ( !current_user_can('unfiltered_html')
					? ' disabled="disabled"'
					: ''
					)
				. ' >'
				. esc_html($$field)
				. '</textarea>'
				. '</p>' . "\n";
		}

		echo '<h3>' . __('Config', 'sem-reloaded') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('float_footer') . '"'
				. checked($float_footer, true, false)
				. ' />'
			. '&nbsp;'
			. __('Place the footer navigation menu and the copyright on a single line.', 'sem-reloaded')
			. '</label>'
			. '</p>' . "\n";

		parent::form($instance);
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array_merge(array(
			'copyright' => __('Copyright %1$s, %2$s', 'sem-reloaded'),
			'copyright_label' => __('%1$s - Site name, %2$s - Year', 'sem-reloaded'),
			'float_footer' => false,
			), parent::defaults());
	} # defaults()


	/**
	 * default_items()
	 *
	 * @return array $items
	 **/

	function default_items() {
		$items = array(array('type' => 'home'));

		$roots = wp_cache_get(0, 'page_children');

		if ( !$roots )
			return $items;

		$front_page_id = get_option('show_on_front') == 'page'
			? (int) get_option('page_on_front')
			: 0;

		foreach ( $roots as $root_id ) {
			if ( $root_id == $front_page_id )
				continue;
			if ( get_post_meta($root_id, '_widgets_exclude', true) )
				continue;
			if ( get_post_meta($root_id, '_menu_exclude', true) )
				continue;
			if ( wp_cache_get($root_id, 'page_children') ) # only non-sections
				continue;

			$items[] = array(
				'type' => 'page',
				'ref' => $root_id,
				);
		}

		return $items;
	} # default_items()
} # footer

/**
 * footer_boxes
 *
 * @package Semiologic Reloaded
 **/

class footer_boxes extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Footer: Boxes Bar', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'footer_boxes',
			'description' => __('Lets you decide where the Footer Boxes Bar panel goes. Must be placed in the footer area.', 'sem-reloaded'),
			);

		$this->WP_Widget('footer_boxes', $widget_name, $widget_ops);
	} # footer_boxes()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_footer' )
			return;

		sem_panels::display('the_footer_boxes');
	} # widget()
} # footer_boxes


