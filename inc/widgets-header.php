<?php
/**
 * header
 *
 * @package Semiologic Reloaded
 **/

if ( !class_exists('sem_nav_menu') )
	include sem_path . '/inc/widgets-navmenu.php';

class header extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
        if ( !is_admin() ) {
        	add_action('wp', array($this, 'wire'), 20);
        }

		$widget_name = __('Header: Site Header', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'header',
			'description' => __('The site\'s header. Must be placed in the header area.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('header', $widget_name, $widget_ops, $control_ops);
	} # header()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		$instance = wp_parse_args($instance, header::defaults());
		extract($instance, EXTR_SKIP);

		$header = header::get();

		echo '<div id="header" class="wrapper'
				. ( $invert_header
					? ' invert_header'
					: ''
					)
				. '"'
 			. ' title="'
				. esc_attr(get_option('blogname'))
				. ' &bull; '
				. esc_attr(get_option('blogdescription'))
				. '" '
                . 'role="banner"'
				. ' itemscope="itemscope" itemtype="http://schema.org/WPHeader">';

		echo "\n";

		echo '<div id="header_top"><div class="hidden"></div></div>' . "\n";

		echo '<div id="header_bg">' . "\n";

		echo '<div class="wrapper_item">' . "\n";

		if ( !$header ) {
			echo '<div id="header_img" class="pad">' . "\n";

			$tagline = '<div id="tagline" class="tagline">'
				. get_option('blogdescription')
				. '</div>' . "\n";

			$site_name = '<div id="sitename" class="sitename">'
				. ( !( is_front_page() && !is_paged() )
					? ( '<a href="' . esc_url(user_trailingslashit(home_url())) . '">' . get_option('blogname') . '</a>' )
					: get_option('blogname')
					)
				. '</div>' . "\n";

			if ( $invert_header ) {
				echo $site_name;
				echo $tagline;
			} else {
				echo $tagline;
				echo $site_name;
			}

			echo '</div>' . "\n";
		} else {
			echo header::display($header);
		}

		echo '</div>' . "\n";

		echo '</div>' . "\n";

		echo '<div id="header_bottom"><div class="hidden"></div></div>' . "\n";

		echo '</div><!-- header -->' . "\n";

		global $did_header;
		global $did_navbar;
		$did_header = intval($did_navbar) + 1;
	} # widget()


	/**
	 * display()
	 *
	 * @param string $header
	 * @return string $html
	 **/

	function display($header = null) {
		if ( !$header )
			$header = header::get();

		if ( !$header )
			return;

		echo '<div id="header_img" class="pad">' . header::display_header_image($header);

		echo '</div>' . "\n";
	} # display()

	/**
	 * display_header_image()
	 *
	 * @param string $header
	 * @return string html
	 */
	function display_header_image($header) {
		if (false === $header_size = wp_cache_get('sem_header', 'sem_header'))
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
		list($width, $height) = $header_size;

		$html = '<img src="' . sem_url . '/icons/pixel.gif"'
			. ' height="' . intval($height) . '"'
			. ' alt="'
				. esc_attr(get_option('blogname'))
				. ' &bull; '
				. esc_attr(get_option('blogdescription'))
				. '"'
			. ' />';

		if ( !( is_front_page() && !is_paged() ) ) {
			$html = '<a'
			. ' href="' . esc_url(user_trailingslashit(home_url())) . '"'
			. ' title="'
				. esc_attr(get_option('blogname'))
				. ' &bull; '
				. esc_attr(get_option('blogdescription'))
				. '"'
			. '>' . $html . '</a>';
		}

		return $html;
	}

	/**
	 * display_image()
	 *
	 * @param string $header
	 * @return string $html
	 **/
	static function display_image($header = null) {
		if ( !$header )
			$header = header::get_header();

		if ( !$header )
			return;

		list($width, $height) = wp_cache_get('sem_header', 'sem_header');

		$header = esc_url(content_url() . $header);

		return '<img src="' . $header . '" height="' . $height . '" width="' . $width . '" alt="'
			. esc_attr(get_option('blogname'))
			. ' &bull; '
			. esc_attr(get_option('blogdescription'))
			. '" />';
	} # display_image()


    /**
     * letter()
     *
     *
     * @return void
     */

	static function letter() {
		$header = header::get();

		if ( !$header || $header != get_post_meta(get_the_ID(), '_sem_header', true) )
			return;

		echo header::display($header);
	} # letter()


	/**
	 * get_basedir()
	 *
	 * @return string $header_basedir
	 **/
	static function get_basedir() {
		static $header_basedir;

		if ( isset($header_basedir) )
			return $header_basedir;

		$header_basedir = '/header';
		if ( defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL )
			$header_basedir .= '/' . $_SERVER['HTTP_HOST'];
		if ( function_exists('is_multisite') && is_multisite() ) {
			$home_path = parse_url(home_url());
			$home_path = isset($home_path['path']) ? rtrim($home_path['path'], '/') : '';
			$header_basedir .= $home_path;
		}

		return $header_basedir;
	}


	/**
	 * get()
	 *
	 * @return string $header
	 **/

	static function get() {
		static $header;

		if ( !is_admin() && isset($header) )
			return $header;

		global $sem_options;

		# try post specific header
		if ( is_singular() ) {
			global $wp_the_query;
			$post_ID = intval($wp_the_query->get_queried_object_id());
		} else {
			$post_ID = false;
		}

		# try cached header
		if ( !is_admin() && !sem_header_cache_debug ) {
			switch ( is_singular() ) {
			case true:
				$header = get_post_meta($post_ID, '_sem_header', true);
				if ( !$header ) {
					$header = false;
					break;
				} elseif ( $header != 'default' ) {
					break;
				}
			default:
				$header = get_transient('sem_header');
			}
		} else {
			$header = false;
		}

		if ( !empty($header) ) {
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
			if ( $header_size ) {
				wp_cache_set('sem_header', $header_size, 'sem_header');
				return $header;
			}
		}

		$header_basedir = header::get_basedir();

		if ( defined('GLOB_BRACE') ) {
			$header_scan = "header{,-*}.{jpg,jpeg,png,gif}";
			$skin_scan = "header.{jpg,jpeg,png,gif}";
			$scan_type = GLOB_BRACE;
		} else {
			$header_scan = "header-*.{jpg,jpeg}";
			$skin_scan = "header.{jpg,jpeg}";
			$scan_type = false;
		}

		if ( is_singular() ) {
			# entry-specific header
			$header = glob(WP_CONTENT_DIR . "$header_basedir/$post_ID/$header_scan", $scan_type);
			if ( $header ) {
				$header = current($header);
				$header = str_replace(WP_CONTENT_DIR, '', $header);
				$header_size = @getimagesize(WP_CONTENT_DIR . $header);
				if ( $header_size ) {
					if ( get_post_meta($post_ID, '_sem_header', true) != $header )
						update_post_meta($post_ID, '_sem_header', $header);
					wp_cache_set('sem_header', $header_size, 'sem_header');
					return $header;
				}
			}
		}

		switch ( true ) {
		default:
			# uploaded header
			$header = glob(WP_CONTENT_DIR . "$header_basedir/$header_scan", $scan_type);
			if ( $header )
				break;

			# skin-specific header
			$active_skin = $sem_options['active_skin'];
			$header = glob(sem_path . "/skins/$active_skin/$skin_scan", $scan_type);
			if ( $header )
				break;

			# no header
			$header = false;
			break;
		}

		if ( is_singular() && get_post_meta($post_ID, '_sem_header', true) != 'default' )
			update_post_meta($post_ID, '_sem_header', 'default');

		if ( $header ) {
			$header = current($header);
			$header = str_replace(WP_CONTENT_DIR, '', $header);
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
			if ( false !== $header_size ) {
				wp_cache_set('sem_header', $header_size, 'sem_header');
				set_transient('sem_header', $header);
				return $header;
			}
		}

		set_transient('sem_header', '0');
		return false;
	} # get()


	/**
	 * wire()
	 *
	 * @param object &$wp
	 * @return void
	 **/

	function wire(&$wp) {

		add_action('wp_head', array($this, 'css'), 30);

	} # wire()


	/**
	 * css()
	 *
	 * @return void
	 **/

	function css() {
		$header = header::get();

		list($width, $height) = wp_cache_get('sem_header', 'sem_header');

		if ( !$height )
			return;

		$header = esc_url(content_url() . $header);

		echo <<<EOS

<style type="text/css">
.skin #header_img {
	background: url(${header}) no-repeat top center;
	height: ${height}px;
	width: 100%;
	border: 0px;
	position: relative;
	padding: 0px;
	margin: 0px auto;
}
.skin #header_img img {
    width: 100%;
}
</style>

EOS;
	} # css()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['invert_header'] = isset($new_instance['invert_header']);

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = header::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Config', 'sem-reloaded') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('invert_header') . '"'
				. checked($invert_header, true, false)
				. ' />'
			. '&nbsp;'
			. __('Output the site\'s name before the tagline.', 'sem-reloaded')
			. '</label>'
			. '</p>' . "\n";
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array(
			'invert_header' => false,
			);
	} # defaults()
} # header



class navbar extends sem_nav_menu {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Header: Nav Menu', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'navbar',
			'description' => __('The header\'s navigation menu, with an optional search form. Must be placed in the header area.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('navbar', $widget_name, $widget_ops, $control_ops);

		$this->multi_level = true;

		$this->ul_menu_class = "header_menu";

		parent::__construct();
	} # navbar()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		$instance = wp_parse_args($instance, navbar::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$navbar_class = '';
		if ( $show_search_form )
			$navbar_class .= ' float_nav';
		if ( $sep )
			$navbar_class .= ' sep_nav';

		echo '<div id="navbar" class="wrapper navbar' . $navbar_class . '">' . "\n";

		echo '<div id="navbar_top"><div class="hidden"></div></div>' . "\n";

		echo '<div id="navbar_bg">' . "\n";

		echo '<div class="wrapper_item">' . "\n";

		echo '<div class="pad">' . "\n";

		echo '<div id="header_nav" class="header_nav inline_menu menu" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">';

		parent::widget($args, $instance);

		echo '</div><!-- header_nav -->' . "\n";

		if ( $show_search_form ) {
			echo '<div id="search_form" class="search_form">';

			if ( is_search() )
				$search = apply_filters('the_search_form', get_search_query());
			else
				$search = $search_field;

			$search_caption = addslashes(esc_attr($search_field));
			if ( $search_caption ) {
				$onfocusblur = ' onfocus="if ( this.value == \'' . $search_caption . '\' )'
							. ' this.value = \'\';"'
						. ' onblur="if ( this.value == \'\' )'
						 	. ' this.value = \'' . $search_caption . '\';"';
			} else {
				$onfocus_blur = '';
			}

			$go = $search_button;

			if ( $go !== '' )
				$go = '<input type="submit" id="go" class="go button submit" value="' . esc_attr($go) . '" />';

			echo '<form method="get"'
					. ' action="' . esc_url(user_trailingslashit(home_url())) . '"'
					. ' id="searchform" name="searchform"'
					. '>'
				. '&nbsp;'				# force line-height
				. '<input type="text" id="s" class="s" name="s"'
					. ' value="' . esc_attr($search) . '"'
					. $onfocusblur
					. ' />'
				. $go
				. '</form>';

			echo '</div><!-- search_form -->';
		}

		echo '<div class="spacer"></div>' . "\n"
			. '</div>' . "\n"
			. '</div>' . "\n"
			. '</div>' . "\n";

		echo '<div id="navbar_bottom"><div class="hidden"></div></div>' . "\n";

		echo '</div><!-- navbar -->' . "\n";

		global $did_header;
		global $did_navbar;
		$did_navbar = intval($did_header) + 1;
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
		$instance['show_search_form'] = isset($new_instance['show_search_form']);
		$instance['search_field'] = trim(strip_tags($new_instance['search_field']));
		$instance['search_button'] = trim(strip_tags($new_instance['search_button']));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = navbar::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-reloaded') . '</h3>' . "\n";

		foreach ( array('search_field', 'search_button') as $field ) {
			echo '<p>'
				. '<label>'
				. '<code>' . $defaults[$field] . '</code>'
				. '<br />' . "\n"
				. '<input type="text" class="widefat"'
					. ' name="' . $this->get_field_name($field) . '"'
					. ' value="' . esc_attr($$field) . '"'
					. ' />'
				. '</label>'
				. '</p>' . "\n";
		}

		echo '<h3>' . __('Config', 'sem-reloaded') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('show_search_form') . '"'
				. checked($show_search_form, true, false)
				. ' />'
			. '&nbsp;'
			. __('Show a search form in the navigation menu.', 'sem-reloaded')
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
			'search_field' => __('Search', 'sem-reloaded'),
			'search_button' => __('Go', 'sem-reloaded'),
			'show_search_form' => true,
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
			if ( (int) get_post_meta($root_id, '_widgets_exclude', true) )
				continue;
			if ( (int) get_post_meta($root_id, '_menu_exclude', true) )
				continue;
			if ( !wp_cache_get($root_id, 'page_children') ) # only sections
				continue;

			$items[] = array(
				'type' => 'page',
				'ref' => $root_id,
				);
		}

		return $items;
	} # default_items()
} # navbar

/**
 * header_boxes
 *
 * @package Semiologic Reloaded
 **/

class header_boxes extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Header: Boxes Bar', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'header_boxes',
			'description' => __('Lets you decide where the Footer Boxes Bar panel goes. Must be placed in the header area.', 'sem-reloaded'),
			);

		$this->WP_Widget('header_boxes', $widget_name, $widget_ops);
	} # header_boxes()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		sem_panels::display('the_header_boxes');
	} # widget()
} # header_boxes


