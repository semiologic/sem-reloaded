<?php
/**
 * sem_panels
 *
 * @package Semiologic Reloaded
 **/

class sem_panels {
	/**
	 * register()
	 *
	 * @return void
	 **/

	static function register() {
		# autofix panels
		sem_panels::switch_themes();
		
		global $sem_options;
		$before_widget = '<div class="widget %1$s %2$s">' . "\n"
						. '<div class="widget_top"><div class="hidden"></div></div>' . "\n"
						. '<div class="widget_bg">' . "\n"
						. '<div class="pad">';
		$after_widget = '</div>' . "\n"
						. '</div>' . "\n"
						. '<div class="widget_bottom"><div class="hidden"></div></div>' . "\n"
						. '</div>' . "\n";
		
		$before_title = '<div class="widget_title"><h2>';
		$after_title = '</h2></div>' . "\n";
		
		if ( strpos($sem_options['active_layout'], 't') !== false ) {
			register_sidebar(
				array(
					'id' => 'top_sidebar',
					'name' => __('Top Wide Sidebar', 'sem-reloaded'),
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title,
					)
				);
			register_sidebar(
				array(
					'id' => 'sidebar-1',
					'name' => __('Left Sidebar', 'sem-reloaded'),
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title,
					)
				);
			register_sidebar(
				array(
					'id' => 'sidebar-2',
					'name' => __('Right Sidebar', 'sem-reloaded'),
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title,
					)
				);
			register_sidebar(
				array(
					'id' => 'bottom_sidebar',
					'name' => __('Bottom Wide Sidebar', 'sem-reloaded'),
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title,
					)
				);
		} else {
			switch ( substr_count($sem_options['active_layout'], 's') ) {
			case 2:
				register_sidebar(
					array(
						'id' => 'sidebar-1',
						'name' => __('Left Sidebar', 'sem-reloaded'),
						'before_widget' => $before_widget,
						'after_widget' => $after_widget,
						'before_title' => $before_title,
						'after_title' => $after_title,
						)
					);
				register_sidebar(
					array(
						'id' => 'sidebar-2',
						'name' => __('Right Sidebar', 'sem-reloaded'),
						'before_widget' => $before_widget,
						'after_widget' => $after_widget,
						'before_title' => $before_title,
						'after_title' => $after_title,
						)
					);
				break;

			case 1:
				register_sidebar(
					array(
						'id' => 'sidebar-1',
						'name' => __('Sidebar', 'sem-reloaded'),
						'before_widget' => $before_widget,
						'after_widget' => $after_widget,
						'before_title' => $before_title,
						'after_title' => $after_title,
						)
					);
				break;
			}
		}

		foreach ( array(
			'the_header' => __('Header Area', 'sem-reloaded'),
			'the_header_boxes' => __('Header Boxes Bar', 'sem-reloaded'),
			'before_the_entries' => __('Before the Entries', 'sem-reloaded'),
			'the_entry' => __('Each Entry', 'sem-reloaded'),
			'after_the_entries' => __('After the Entries', 'sem-reloaded'),
			'the_footer_boxes' => __('Footer Boxes Bar', 'sem-reloaded'),
			'the_footer' => __('Footer Area', 'sem-reloaded'),
			'the_404' => __('Not Found Error (404)', 'sem-reloaded'),
		) as $panel_id => $panel_label ) {
			$before_title = '<h2>';
			$after_title = '</h2>' . "\n";
			
			switch ( $panel_id ) {
			case 'the_header':
				$before_widget = "\n\t"
					. '<div class="%1$s %2$s header_widget wrapper">' . "\n\t"
					. '<div class="header_widget_top"><div class="hidden"></div></div>' . "\n\t"
					. '<div class="header_widget_bg">' . "\n\t"
					. '<div class="wrapper_item">' . "\n\t"
					. '<div class="pad">' . "\n";
				$after_widget = "\n\t"
					. '</div>' . "\n\t"
					. '</div>' . "\n\t"
					. '</div>' . "\n\t"
					. '<div class="header_widget_bottom"><div class="hidden"></div></div>' . "\n\t"
					. '</div><!-- header_widget -->' . "\n\n";
				break;
			
			case 'the_footer':
				$before_widget = "\n\t"
					. '<div class="%1$s %2$s footer_widget wrapper">' . "\n\t"
					. '<div class="footer_widget_top"><div class="hidden"></div></div>' . "\n\t"
					. '<div class="footer_widget_bg">' . "\n\t"
					. '<div class="wrapper_item">' . "\n\t"
					. '<div class="pad">' . "\n";
				$after_widget = "\n\t"
					. '</div>' . "\n\t"
					. '</div>' . "\n\t"
					. '</div>' . "\n\t"
					. '<div class="footer_widget_bottom"><div class="hidden"></div></div>' . "\n\t"
					. '</div><!-- footer_widget -->' . "\n\n";
				break;
			
			case 'the_header_boxes':
			case 'the_footer_boxes':
				$before_widget = '<div class="inline_box %1$s %2$s">' . "\n"
					. '<div class="pad">' . "\n";
				$after_widget = '</div>' . "\n"
					. '</div><!-- inline_box -->' . "\n";
				break;
			
			case 'before_the_entries':
			case 'after_the_entries':
			case 'the_404':
				$before_widget = '<div class="%1$s %2$s main_widget">' . "\n"
					. '<div class="main_widget_top"><div class="hidden"></div></div>' . "\n"
					. '<div class="main_widget_bg">' . "\n"
					. '<div class="pad">' . "\n";
				$after_widget = '</div>' . "\n"
					. '</div>' . "\n"
					. '<div class="main_widget_bottom"><div class="hidden"></div></div>' . "\n"
					. '</div><!-- main_widget -->' . "\n";
				break;
			
			default:
				$before_widget = '<div class="spacer"></div>' . "\n"
					. '<div class="%1$s %2$s">' . "\n"
					. '<div class="pad">' . "\n";
				$after_widget = '</div>' . "\n"
					. '</div>' . "\n";
				break;
			}
			
			register_sidebar(
				array(
					'id' => $panel_id,
					'name' => $panel_label,
					'before_widget' => $before_widget,
					'after_widget' => $after_widget,
					'before_title' => $before_title,
					'after_title' => $after_title,
					)
				);
		}
	} # register()
	
	
	/**
	 * display()
	 *
	 * @param string $panel_id
	 * @return void
	 **/

	static function display($panel_id) {
		if ( $panel_id != 'the_entry' && !class_exists('widget_contexts') && is_letter() )
			return;
		
		switch ( $panel_id ) {
		case 'left_sidebar':
			dynamic_sidebar('sidebar-1');
			break;
		case 'right_sidebar':
			dynamic_sidebar('sidebar-2');
			break;
		case 'top_sidebar':
		case 'bottom_sidebar':
		case 'before_the_entries':
		case 'after_the_entries':
		case 'the_404':
		case 'the_entry':
			dynamic_sidebar($panel_id);
			break;
		case 'the_header':
			if ( !is_active_sidebar($panel_id) )
			 	break;
			
			global $did_header;
			global $did_navbar;
			global $did_top_widgets;
			global $did_middle_widgets;
			global $did_bottom_widgets;
			
			echo '<div id="header_wrapper">' . "\n";
			
			$did_header = false;
			$did_navbar = false;
			$did_top_widgets = false;
			$did_middle_widgets = false;
			$did_bottom_widgets = false;
			
			dynamic_sidebar($panel_id);
			
			if ( !$did_header && !$did_navbar && $did_top_widgets ) {
				echo '</div></div>' . "\n";
			} elseif ( $did_header && $did_navbar && $did_bottom_widgets ) {
				echo '</div></div>' . "\n";
			} elseif ( !( $did_header && $did_navbar ) && $did_middle_widgets ) {
				echo '</div></div>' . "\n";
			}
			
			echo '</div>' . "\n";
			
			break;
		case 'the_footer':
			if ( !is_active_sidebar($panel_id) )
			 	break;
			
			global $did_footer;
			global $did_top_widgets;
			global $did_bottom_widgets;
			
			echo '<div id="footer_wrapper">' . "\n";
			
			$did_top_widgets = false;
			$did_footer = false;
			$did_bottom_widgets = false;
			
			dynamic_sidebar($panel_id);
			
			if ( !$did_footer && $did_top_widgets ) {
				echo '</div></div>' . "\n";
			} elseif ( $did_bottom_widgets ) {
				echo '</div></div>' . "\n";
			}
			
			echo '</div>' . "\n";
			
			break;
		case 'the_header_boxes':
		case 'the_footer_boxes':
			if ( !is_active_sidebar($panel_id) )
			 	break;
			
			$id = ( $panel_id == 'the_header_boxes' ) ? 'header_boxes' : 'footer_boxes';
			$class = ( $panel_id == 'the_header_boxes' ) ? 'header_widget' : 'footer_widget';

			echo '<div class="spacer"></div>' . "\n"
				. '<div id="' . $id . '" class="wrapper inline_boxes ' . $class . '">' . "\n"
				. '<div id="' . $id . '_top" class="inline_boxes_top ' . $class . '_top"><div class="hidden"></div></div>' . "\n"
				. '<div id="' . $id . '_bg" class="inline_boxes_bg ' . $class . '_bg">' . "\n"
				. '<div class="wrapper_item">' . "\n";
			
			dynamic_sidebar($panel_id);
			
			echo '<div class="spacer"></div>' . "\n"
				. '</div>' . "\n"
				. '</div>' . "\n"
				. '<div id="' . $id . '_bottom" class="inline_boxes_bottom ' . $class . '_bottom"><div class="hidden"></div></div>' . "\n"
				. '</div><!-- ' . $id . ' -->' . "\n";
			
			break;
		}
	} # display()
	
	
	/**
	 * init_widgets()
	 *
	 * @return void
	 **/

	function init_widgets() {
		if ( !intval(get_option('init_sem_panels')) )
			return;
		
		if ( is_admin() ) {
			global $wp_filter;
			$filter_backup = $wp_filter['sidebars_widgets'];
			unset($wp_filter['sidebars_widgets']);
			$sidebars_widgets = wp_get_sidebars_widgets();
			$wp_filter['sidebars_widgets'] = $filter_backup;
			$sidebars_widgets = sem_panels::convert($sidebars_widgets);
			$sidebars_widgets = sem_panels::install($sidebars_widgets);
			if ( empty($_GET['preview']) && empty($_GET['stylesheet']) )
				wp_set_sidebars_widgets($sidebars_widgets);
			$sidebars_widgets = sem_panels::upgrade($sidebars_widgets);
			if ( empty($_GET['preview']) && empty($_GET['stylesheet']) ) {
				wp_set_sidebars_widgets($sidebars_widgets);
				update_option('init_sem_panels', '0');
			}
		} else {
			if ( empty($GLOBALS['_wp_sidebars_widgets']) )
				$GLOBALS['_wp_sidebars_widgets'] = get_option('sidebars_widgets', array('array_version' => 3));
			$GLOBALS['_wp_sidebars_widgets'] = sem_panels::convert($GLOBALS['_wp_sidebars_widgets']);
			$GLOBALS['_wp_sidebars_widgets'] = sem_panels::install($GLOBALS['_wp_sidebars_widgets']);
			$GLOBALS['_wp_sidebars_widgets'] = sem_panels::upgrade($GLOBALS['_wp_sidebars_widgets']);
		}
	} # init_widgets()
	
	
	/**
	 * install()
	 *
	 * @param array $sidebars_widgets
	 * @return array $sidebars_widgets
	 **/

	function install($sidebars_widgets) {
		if ( !empty($sidebars_widgets['the_entry']) )
			return $sidebars_widgets;
		
		global $wp_widget_factory;
		global $wp_registered_sidebars;
		
		$default_widgets = array(
			'the_header' => array(
				'header',
				'navbar',
				'header_boxes',
				),
			'before_the_entries' => array(
				'blog_header',
				),
			'the_entry' => array(
				'entry_header',
				'entry_content',
				'entry_tags',
				'entry_categories',
				'bookmark_me',
				'related_widget',
				'entry_comments',
				),
			'after_the_entries' => array(
				'blog_footer',
				),
			'the_footer' => array(
				'footer_boxes',
				'footer',
				),
			'sidebar-2' => array(
				'newsletter_manager',
				'subscribe_me',
				!class_exists('sem_admin_menu') ? 'WP_Widget_Meta' : null,
				),
			'sidebar-1' => array(
				class_exists('nav_menu') ? 'nav_menu' : null,
				class_exists('fuzzy_widget') ? 'fuzzy_widget' : null,
				'WP_Widget_Categories',
				'WP_Widget_Archives',
				),
			'the_404' => array(
				class_exists('fuzzy_widget') ? 'fuzzy_widget' : null,
				class_exists('silo_map') ? 'silo_map' : null,
				),
			);
		
		$registered_sidebars = array_keys($wp_registered_sidebars);
		$registered_sidebars = array_diff($registered_sidebars, array('wp_inactive_widgets'));
		foreach ( $registered_sidebars as $sidebar )
            if (isset($sidebars_widgets[$sidebar])) {
                $sidebars_widgets[$sidebar] = (array) $sidebars_widgets[$sidebar];
            }
        if (isset($sidebars_widgets['wp_inactive_widgets']))
		    $sidebars_widgets['wp_inactive_widgets'] = (array) $sidebars_widgets['wp_inactive_widgets'];
        else
            $sidebars_widgets['wp_inactive_widgets'] = array();
		
		# convert left/right sidebars into sidebar-1/-2
		foreach ( array(
			'sidebar-1' => array(
				'left',
				'left-sidebar',
				'left_sidebar',
				),
			'sidebar-2' => array(
				'right',
				'right-sidebar',
				'right_sidebar',
				),
			) as $sidebar_id => $old_sidebar_ids ) {
			if ( !empty($sidebars_widgets[$sidebar_id]) )
				continue;
			foreach ( $old_sidebar_ids as $old_sidebar_id ) {
				if ( !empty($sidebars_widgets[$old_sidebar_id]) ) {
					$sidebars_widgets[$sidebar_id] = $sidebars_widgets[$old_sidebar_ids];
					unset($sidebars_widgets[$old_sidebar_ids]);
					break;
				}
			}
		}
		
		foreach ( $default_widgets as $panel => $widgets ) {
			if ( isset($sidebars_widgets[$panel]) && empty($sidebars_widgets[$panel]) )
				$sidebars_widgets[$panel] = (array) $sidebars_widgets[$panel];
			else
				continue;
			
			if ( $panel == 'sidebar-2' && !empty($sidebars_widgets['sidebar-1']) )
				continue;
			
			foreach ( $widgets as $widget ) {
				if ( !is_a($wp_widget_factory->widgets[$widget], 'WP_Widget') )
					continue;
				
				$widget_ids = array_keys((array) $wp_widget_factory->widgets[$widget]->get_settings());
				$widget_id_base = $wp_widget_factory->widgets[$widget]->id_base;
				$new_widget_number = $widget_ids ? max($widget_ids) + 1 : 2;
				foreach ( $widget_ids as $key => $widget_id )
					$widget_ids[$key] = $widget_id_base . '-' . $widget_id;
				
				# check if active already
				foreach ( $widget_ids as $widget_id ) {
					if ( in_array($widget_id, $sidebars_widgets[$panel]) )
						continue 2;
				}

				# use an inactive widget if available
				foreach ( $widget_ids as $widget_id ) {
					foreach ( array_keys($sidebars_widgets) as $sidebar ) {
						if ( !is_array($sidebars_widgets[$sidebar]) )
							continue;
						
						$key = array_search($widget_id, $sidebars_widgets[$sidebar]);
						
						if ( $key === false )
							continue;
						elseif ( in_array($sidebar, $registered_sidebars) ) {
							continue 2;
						}
						
						unset($sidebars_widgets[$sidebar][$key]);
						$sidebars_widgets[$panel][] = $widget_id;
						continue 3;
					}
					
					$sidebars_widgets[$panel][] = $widget_id;
					continue 2;
				}
				
				# create a widget on the fly
				$new_settings = $wp_widget_factory->widgets[$widget]->get_settings();
				
				$new_settings[$new_widget_number] = array();
				$wp_widget_factory->widgets[$widget]->_set($new_widget_number);
				$wp_widget_factory->widgets[$widget]->_register_one($new_widget_number);
				
				$widget_id = "$widget_id_base-$new_widget_number";
				$sidebars_widgets[$panel][] = $widget_id;
				
				$wp_widget_factory->widgets[$widget]->save_settings($new_settings);
			}
		}
		
		if ( isset($sidebars_widgets['array_version']) && $sidebars_widgets['array_version'] == 3 )
			$sidebars_widgets['wp_inactive_widgets'] = array_merge($sidebars_widgets['wp_inactive_widgets']);
		else
			unset($sidebars_widgets['wp_inactive_widgets']);
		
		return $sidebars_widgets;
	} # install()
	
	
	/**
	 * upgrade()
	 *
	 * @param array $sidebars_widgets
	 * @return array $sidebars_widgets
	 **/

	function upgrade($sidebars_widgets) {
		global $wp_widget_factory;
		
		if ( !is_active_widget(false, false, 'blog_header') ) {
			$sidebars_widgets['before_the_entries'] = (array) $sidebars_widgets['before_the_entries'];
			$key = array_search('archives_header', $sidebars_widgets['before_the_entries']);
			$widget_id = $wp_widget_factory->widgets['blog_header']->id;
			if ( $key !== false )
				$sidebars_widgets['before_the_entries'][$key] = $widget_id;
			else
				array_unshift($sidebars_widgets['before_the_entries'], $widget_id);
		}
		
		if ( !is_active_widget(false, false, 'blog_footer') ) {
			$sidebars_widgets['after_the_entries'] = (array) $sidebars_widgets['after_the_entries'];
			$key = array_search('next_prev_posts', $sidebars_widgets['after_the_entries']);
			if ( $key === false )
				$key = array_search('nextprev-posts', $sidebars_widgets['after_the_entries']);
			$widget_id = $wp_widget_factory->widgets['blog_footer']->id;
			if ( $key !== false )
				$sidebars_widgets['after_the_entries'][$key] = $widget_id;
			else
				array_push($sidebars_widgets['after_the_entries'], $widget_id);
		}
		
		if ( !is_active_widget(false, false, 'header_boxes') ) {
			$sidebars_widgets['the_header'] = (array) $sidebars_widgets['the_header'];
			$widget_id = $wp_widget_factory->widgets['header_boxes']->id;
			array_push($sidebars_widgets['the_header'], $widget_id);
		}
		
		if ( !is_active_widget(false, false, 'footer_boxes') ) {
			$sidebars_widgets['the_footer'] = (array) $sidebars_widgets['the_footer'];
			$widget_id = $wp_widget_factory->widgets['footer_boxes']->id;
			array_unshift($sidebars_widgets['the_footer'], $widget_id);
		}
		
		return $sidebars_widgets;
	} # upgrade()
	
	
	/**
	 * convert()
	 *
	 * @param array $sidebars_widgets
	 * @return array $sidebars_widgets
	 **/

	function convert($sidebars_widgets) {
		if ( empty($sidebars_widgets['ext_sidebar']) )
			return $sidebars_widgets;
		
		if ( empty($sidebars_widgets['sidebar-2']) )
			$sidebars_widgets['sidebar-2'] = $sidebars_widgets['ext_sidebar'];
		
		unset($sidebars_widgets['ext_sidebar']);
		
		return $sidebars_widgets;
	} # convert()
	
	
	/**
	 * switch_themes()
	 *
	 * @return void
	 **/

	function switch_themes() {
		if ( !get_option('init_sem_panels') ) {
			$sidebars_widgets = wp_get_sidebars_widgets();
			foreach ( array('before_the_entries', 'the_entry', 'after_the_entries') as $sidebar ) {
				if ( empty($sidebars_widgets[$sidebar]) ) {
					update_option('init_sem_panels', '1');
					break;
				}
			}
		}
	} # switch_themes()
} # sem_panels

sem_panels::register();

if ( !defined('DOING_CRON') )
	add_action('init', array('sem_panels', 'init_widgets'), 2000);
?>