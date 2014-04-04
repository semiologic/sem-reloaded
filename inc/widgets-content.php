<?php
/**
 * blog_header
 *
 * @package Semiologic Reloaded
 **/

class blog_header extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Blog: Header', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'blog_header archives_header',
			'description' => __('The title and description that appear on category, tag, search, 404 and date archive pages. Must be placed before each entry.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('blog_header', $widget_name, $widget_ops, $control_ops);
	} # blog_header()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'before_the_entries' || !is_archive() && !is_search() && !is_404() )
			return;

		$desc = '';

		extract($args, EXTR_SKIP);
		$instance = wp_parse_args($instance, blog_header::defaults());
		extract($instance, EXTR_SKIP);

		echo $before_widget;

		echo '<h1>';

		if ( is_category() ) {
			single_cat_title();
			$desc = trim(category_description());
		} elseif ( is_tag() ) {
			single_tag_title();
			$desc = trim(tag_description());
		} elseif ( is_date() ) {
			if ( is_year() )
				$date = date_i18n(__('Y', 'sem-reloaded'), strtotime(get_query_var('year') . '-01-01 GMT'), true);
			elseif ( is_month() )
				$date = single_month_title(' ', false);
			else
				$date = date_i18n(__('M jS, Y', 'sem-reloaded'), strtotime(get_query_var('year') . '-' . zeroise(get_query_var('monthnum'), 2) . '-' . zeroise(get_query_var('day'), 2) . ' GMT'), true);

			echo sprintf(trim($archives_title), $date);
			$desc = '<div class="posts_nav">'
				. blog_footer::date_nav()
				. '</div>' . "\n";
		} elseif ( is_author() ) {
			global $wp_the_query;

			$user = new WP_User($wp_the_query->get_queried_object_id());
            echo '<span class="vcard">'
             . '<a class="url fn n" href="' . esc_url( get_author_posts_url( $user->ID ) ) . '" title="' . esc_attr( $user->display_name ) . '" rel="me">'
             . $user->display_name
             . '</a></span>';
            $desc = trim($user->description);
		} elseif ( is_search() ) {
			echo sprintf($search_title, apply_filters('the_search_query', get_search_query()));
		} elseif ( is_404() ) {
			echo $title_404;
			$desc = $desc_404;
		}

		echo '</h1>' . "\n";

        if (is_author() && class_exists('author_image')) {
            $author_image = author_image::get($user->ID);
            echo $author_image;
        }

		if ( $desc ) {
			echo '<div class="archives_header_desc">'
            . wpautop(apply_filters('widget_text', $desc))
            . '</div>';
        }

        echo '<div style="clear: both;"></div>' . "\n";

		echo $after_widget;
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		foreach ( array_keys(blog_header::defaults()) as $field ) {
			switch ( $field ) {
			case 'desc_404':
				if ( current_user_can('unfiltered_html') )
					$instance[$field] = trim($new_instance[$field]);
				else
					$instance[$field] = $old_instance[$field];
				break;
			default:
				$instance[$field] = trim(strip_tags($new_instance[$field]));
			}
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
		$defaults = blog_header::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-reloaded') . '</h3>' . "\n";

		foreach ( $defaults as $field => $default ) {
			switch ( $field ) {
			case 'desc_404':
				echo '<p>'
					. '<label for="' . $this->get_field_id($field) . '">'
					. '<code>' . htmlspecialchars($default, ENT_QUOTES, get_option('blog_charset')) . '</code>'
					. '</label>'
					. '<br />' . "\n"
					. '<textarea class="widefat" cols="20" rows="3"'
						. ' id="' . $this->get_field_id($field) . '"'
						. ' name="' . $this->get_field_name($field) . '"'
						. ' >'
						. esc_html($$field)
						. '</textarea>'
					. '</p>' . "\n";
				break;
			default:
				echo '<p>'
					. '<label>'
					. '<code>' . $default . '</code>'
					. '<br />' . "\n"
					. '<input type="text" class="widefat"'
						. ' name="' . $this->get_field_name($field) . '"'
						. ' value="' . esc_attr($$field) . '"'
						. ' />'
					. '</label>'
					. '</p>' . "\n";
			}
		}
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array(
			'title_404' => __('404: Not Found', 'sem-reloaded'),
			'desc_404' => __('The page you\'ve requested was not found.', 'sem-reloaded'),
			'archives_title' => __('%s Archives', 'sem-reloaded'),
			'search_title' => __('Search: %s', 'sem-reloaded'),
			);
	} # defaults()
} # blog_header


/**
 * blog_footer
 *
 * @package Semiologic Reloaded
 **/

class blog_footer extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Blog: Footer', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'blog_footer next_prev_posts',
			'description' => __('The next/previous blog posts links. Must be placed after each entry.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('blog_footer', $widget_name, $widget_ops, $control_ops);
	} # blog_footer()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		global $wp_the_query;
		$max_num_pages = (int) $wp_the_query->max_num_pages;

		if ( $args['id'] != 'after_the_entries' || is_singular() || ( !is_date() && $max_num_pages <= 1 ) )
			return;

		extract($args, EXTR_SKIP);
		$instance = wp_parse_args($instance, blog_footer::defaults());
		extract($instance, EXTR_SKIP);

		$paged = (int) get_query_var('paged');
		if ( !$paged )
			$paged = 1;

		$pages = array();

		if ( $max_num_pages > 1 ) {
			if ( $paged >= 2 )
				$pages[] = get_previous_posts_link(trim('&laquo; ' . $previous));

			$range = array($paged);

			for ( $i = 1; $i <= 3; $i++ ) {
				array_unshift($range, $paged - $i);
				array_push($range, $paged + $i);
			}

			while ( end($range) > $max_num_pages ) {
				reset($range);
				$i = current($range) - 1;
				array_unshift($range, $i);
				array_pop($range);
			}

			reset($range);
			while ( current($range) < 1 ) {
				$i = end($range) + 1;
				if ( $i <= $max_num_pages )
					array_push($range, $i);
				array_shift($range);
				reset($range);
			}

			if ( current($range) != 1 )
				$pages[] = '...';

			foreach ( $range as $i ) {
				if ( $i == $paged ) {
					$pages[] = '<strong>' . $i . '</strong>';
				} else {
					$pages[] = '<a href="' . get_pagenum_link($i) . '">'
						. $i
						. '</a>';
				}
			}

			if ( end($range) != $max_num_pages )
				$pages[] = '...';

			if ( $paged < $max_num_pages ) {
				$pages[] = get_next_posts_link(trim($next . ' &raquo;'));
			}
		}

		$pages = implode(' ', $pages);

		$dates = blog_footer::date_nav();

		$o = array();

		foreach ( array('pages', 'dates') as $var ) {
			if ( $$var )
				$o[] = $$var;
		}

		$o = "<p>" . implode("</p>\n<p>", $o) . "</p>\n";

		echo $before_widget
			. $o
			. $after_widget;
	} # widget()


	/**
	 * date_nav()
	 *
	 * @return string $nav
	 **/

	function date_nav() {
		if ( !is_date() )
			return false;

		$dates = array();

		global $wpdb;

		$y = get_query_var('year');
		$m = get_query_var('monthnum');
		$d = get_query_var('day');

		$m = $m ? zeroise($m, 2) : false;
		$d = $d ? zeroise($d, 2) : false;

		if ( $d )
			$stop = "$y-$m-$d";
		elseif ( $m )
			$stop = "$y-$m-01";
		else
			$stop = "$y-01-01";

		$sql = "
			SELECT	MAX(post_date)
			FROM	$wpdb->posts
			WHERE	post_date < '$stop'
			AND		post_type = 'post'
			AND		post_status = 'publish'
			";
		$cache_id = md5($sql);

		$date = wp_cache_get($cache_id, 'widget_queries');
		if ( $date === false ) {
			$date = $wpdb->get_var($sql);
			if ( !$date )
				$date = 0;
			wp_cache_add($cache_id, $date, 'widget_queries');
		}

		if ( $date ) {
			$date = strtotime("$date GMT");

			if ( $d ) {
				$dates[] = '<a href="' . get_day_link(gmdate('Y', $date), gmdate('m', $date), gmdate('d', $date)) . '">'
					. '&laquo; ' . date_i18n(__('M jS, Y', 'sem-reloaded'), $date, true)
					. '</a>';
			} elseif ( $m ) {
				$dates[] = '<a href="' . get_month_link(gmdate('Y', $date), gmdate('m', $date)) . '">'
					. '&laquo; ' . date_i18n(__('M, Y', 'sem-reloaded'), $date, true)
					. '</a>';
			} else {
				$dates[] = '<a href="' . get_year_link(gmdate('Y', $date)) . '">'
					. '&laquo; ' . date_i18n(__('Y', 'sem-reloaded'), $date, true)
					. '</a>';
			}
		}

		if ( $d ) {
			$dates[] = '<a href="' . get_month_link($y, $m) . '">'
				. date_i18n(__('M, Y', 'sem-reloaded'), strtotime("$y-$m-$d GMT"), true)
				. '</a>';
		}

		if ( $d )
			$stop = gmdate('Y-m-d', strtotime("$y-$m-$d GMT + 1 day"));
		elseif ( $m )
			$stop = gmdate('Y-m-d', strtotime("$y-$m-01 GMT + 1 month"));
		else
			$stop = gmdate('Y-m-d', strtotime("$y-01-01 GMT + 1 year"));

		$sql = "
			SELECT	MIN(post_date)
			FROM	$wpdb->posts
			WHERE	post_date >= '$stop'
			AND		post_type = 'post'
			AND		post_status = 'publish'
			";
		$cache_id = md5($sql);

		$date = wp_cache_get($cache_id, 'widget_queries');
		if ( $date === false ) {
			$date = $wpdb->get_var($sql);
			if ( !$date )
				$date = 0;
			wp_cache_add($cache_id, $date, 'widget_queries');
		}

		if ( $date ) {
			$date = strtotime("$date GMT");

			if ( $d ) {
				$dates[] = '<a href="' . get_day_link(gmdate('Y', $date), gmdate('m', $date), gmdate('d', $date)) . '">'
					. date_i18n(__('M jS, Y', 'sem-reloaded'), $date, true) . ' &raquo;'
					. '</a>';
			} elseif ( $m ) {
				$dates[] = '<a href="' . get_month_link(gmdate('Y', $date), gmdate('m', $date)) . '">'
					. date_i18n(__('M, Y', 'sem-reloaded'), $date, true) . ' &raquo;'
					. '</a>';
			} else {
				$dates[] = '<a href="' . get_year_link(gmdate('Y', $date)) . '">'
					. date_i18n(__('Y', 'sem-reloaded'), $date, true) . ' &raquo;'
					. '</a>';
			}
		}

		$dates = implode(' &bull; ', $dates);

		return $dates;
	} # date_nav()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		foreach ( array_keys(blog_footer::defaults()) as $field )
			$instance[$field] = trim(strip_tags($new_instance[$field]));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = blog_footer::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-reloaded') . '</h3>' . "\n";

		foreach ( $defaults as $field => $default ) {
			echo '<p>'
				. '<label>'
				. '<code>' . $default . '</code>'
				. '<br />' . "\n"
				. '<input type="text" class="widefat"'
					. ' name="' . $this->get_field_name($field) . '"'
					. ' value="' . esc_attr($$field) . '"'
					. ' />'
				. '</label>'
				. '</p>' . "\n";
		}
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array(
			'next' => __('Next', 'sem-reloaded'),
			'previous' => __('Previous', 'sem-reloaded'),
			);
	} # defaults()
} # blog_footer


/**
 * breadcrumbs
 *
 * @package Semiologic Reloaded
 **/

class sem_breadcrumbs extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Breadcrumb Navigation', 'sem-reloaded');
		$widget_ops = array(
			'classname' => 'sem_breadcrumbs',
			'description' => __('Breadcrumb Navigation. Must be placed before each entry.', 'sem-reloaded'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('sem_breadcrumbs', $widget_name, $widget_ops, $control_ops);
	} # blog_footer()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {

		if ( $args['id'] != 'before_the_entries' && $args['id'] != 'the_header' )
			return;

		extract($args, EXTR_SKIP);
		$instance = wp_parse_args( $instance, $this->defaults() );
		extract($instance, EXTR_SKIP);


		$o = $this->breadcrumbs( $instance );

		echo $before_widget
			. $o
			. $after_widget;
	} # widget()

	/**
	 * breadcrumbs()
	 *
	 * @param array $instance
	 * @return string $o
	 **/
	function breadcrumbs( $instance ) {

		extract($instance, EXTR_SKIP);

		$before = '<span class="current">'; // tag before the current crumb
		$after = '</span>'; // tag after the current crumb
		$delimiters = array( '>', '/', '&raquo;');

		global $post;
		$homeLink = get_bloginfo('url');
		$o = '';

		if (is_home() || is_front_page()) {

			if ($show_on_home == true) {
				$o .= '<div id="breadcrumbs" itemprop="breadcrumb">';
				$o .= '<a href="' . $homeLink . '" rel="home">' . $home_name . '</a>';
				$o .= '</div>';
			}
		} else {

			$o .= '<div id="breadcrumbs" itemprop="breadcrumb">';
			$o .= '<a href="' . $homeLink . '" rel="home">' . $home_name . '</a> ';
			$o .=  $delimiters[$delimiter] . ' ';

				if ( is_category() ) {
					$thisCat = get_category(get_query_var('cat'), false);
					if ($thisCat->parent != 0)
						$o .= get_category_parents($thisCat->parent, TRUE, ' ' . $delimiters[$delimiter] . ' ');
					$o .= $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;

				} elseif ( is_search() ) {
					$o .= $before . 'Search results for "' . get_search_query() . '"' . $after;

				} elseif ( is_day() ) {
					$o .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiters[$delimiter] . ' ';
					$o .= '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiters[$delimiter] . ' ';
					$o .= $before . get_the_time('d') . $after;

				} elseif ( is_month() ) {
					$o .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiters[$delimiter] . ' ';
					$o .= $before . get_the_time('F') . $after;

				} elseif ( is_year() ) {
					$o .= $before . get_the_time('Y') . $after;

				} elseif ( is_single() && !is_attachment() ) {
					if ( get_post_type() != 'post' ) {
						$post_type = get_post_type_object(get_post_type());
						$slug = $post_type->rewrite;
						$o .= '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
						if ($show_current == true)
							$o .= ' ' . $delimiters[$delimiter] . ' ' . $before . get_the_title() . $after;
					} else {
						$cat = get_the_category(); $cat = $cat[0];
						$cats = get_category_parents($cat, TRUE, ' ' . $delimiters[$delimiter] . ' ');
						if ($show_current == false)
							$cats = preg_replace("#^(.+)\s$delimiters[$delimiter]\s$#", "$1", $cats);
						$o .= $cats;
						if ($show_current == true)
							$o .= $before . get_the_title() . $after;
					}

				} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
					$post_type = get_post_type_object(get_post_type());
					$o .= $before . $post_type->labels->singular_name . $after;

				} elseif ( is_attachment() ) {
					$parent = get_post($post->post_parent);
					$cat = get_the_category($parent->ID); $cat = $cat[0];
					$o .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					$o .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
					if ($show_current == 1)
						$o .= ' ' . $delimiters[$delimiter] . ' ' . $before . get_the_title() . $after;

				} elseif ( is_page() && !$post->post_parent ) {
				    if ($show_current == 1)
					    $o .= $before . get_the_title() . $after;

				} elseif ( is_page() && $post->post_parent ) {
					$parent_id  = $post->post_parent;
					$breadcrumbs = array();
					while ($parent_id) {
						$page = get_post($parent_id);
						$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
						$parent_id  = $page->post_parent;
					}
					$breadcrumbs = array_reverse($breadcrumbs);
					for ($i = 0; $i < count($breadcrumbs); $i++) {
						$o .= $breadcrumbs[$i];
						if ($i != count($breadcrumbs)-1)
							$o .= ' ' . $delimiters[$delimiter] . ' ';
					}
					if ($show_current == 1)
						$o .= ' ' . $delimiters[$delimiter] . ' ' . $before . get_the_title() . $after;

				} elseif ( is_tag() ) {
					$o .= $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

				} elseif ( is_author() ) {
					global $author;
					$userdata = get_userdata($author);
					$o .= $before . 'Articles posted by ' . $userdata->display_name . $after;

				} elseif ( is_404() ) {
					$o .= $before . 'Error 404' . $after;
				}

				if ( get_query_var('paged') ) {
					if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() )
						$o .= ' (';
					$o .= __('Page') . ' ' . get_query_var('paged');
					if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() )
						$o .= ')';
				}

			$o .= '</div>';
		}

		return $o;
	} // end breadcrumbs()
	
	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['show_on_home'] =  isset($new_instance['show_on_home']);
		$instance['delimiter'] = (int) ($new_instance['delimiter']);
		$instance['home_name'] = trim(strip_tags($new_instance['home_name']));
		$instance['show_current'] = isset($new_instance['show_current']);

		return $instance;
	} # update()

	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/
	function form($instance) {
		$instance = wp_parse_args($instance, $this->defaults());
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Config', 'sem-reloaded') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
			. ' name="' . $this->get_field_name('show_on_home') . '"'
			. checked($show_on_home, true, false)
			. ' />'
			. '&nbsp;'
			. __('Display breadcrumb navigation on the home/front page.', 'sem-reloaded')
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
			. ' name="' . $this->get_field_name('show_current') . '"'
			. checked($show_current, true, false)
			. ' />'
			. '&nbsp;'
			. __('Show current post/page title in breadcrumbs.', 'sem-reloaded')
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. __('Delimiter to use between levels:', 'sem-reloaded') . '&nbsp;'
			. '<select name="' . $this->get_field_name('delimiter') . '">' . "\n"
			. '<option value="0"'
				. selected($delimiter, 0, false)
				. '>' .  '>' . '</option>' . "\n"
			. '<option value="1"'
				. selected($delimiter, 1, false)
				. '>' . '/' . '</option>' . "\n"
			. '<option value="2"'
				. selected($delimiter, 2, false)
				. '>' . '&raquo;' . '</option>' . "\n"
			. '</select>'
			. '</label>'
			. '</p>' . "\n";

      	echo '<h3>' . __('Captions', 'sem-reloaded') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Text for \'Home\' link', 'sem-reloaded') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('home_name') . '"'
			. ' value="' . esc_attr($home_name) . '"'
			. ' />'
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
			'show_on_home' => false, // 1 - show breadcrumbs on the homepage, 0 - don't show
			'delimiter' => 0, // delimiter between crumbs
			'home_name' => __('Home', 'sem-reloaded'), // text for the 'Home' link
			'show_current' => true, // 1 - show current post/page title in breadcrumbs, 0 - don't show
			);
	} # defaults()
} # sem_breadcrumbs
