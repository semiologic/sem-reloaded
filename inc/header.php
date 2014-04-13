<?php
/**
 * sem_header
 *
 * @package Semiologic Reloaded
 **/

class sem_header {

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
		add_action('toplevel_page_sem_menu', array($this, 'save_options'), 0);
        add_action('save_post', array($this, 'save_entry'), 30);
    }

	
	/**
	 * save_options()
	 *
	 * @return void
	 **/
	
	function save_options() {
		if ( !$_POST || !current_user_can('switch_themes') )
			return;
		
		check_admin_referer('sem_header');
		
		#dump($_POST, $_FILES);
		
		global $sem_options;
		$header = header::get();
		$active_skin = $sem_options['active_skin'];
		
		if ( !empty($_FILES['header_file']['name']) ) {
			if ( $header && strpos($header, "/skins/$active_skin/") === false ) {
				if ( !is_writable(WP_CONTENT_DIR . $header) ) {
					echo '<div class="error">'
						. "<p>"
							. "<strong>"
							. sprintf(__('%s is not writable.', 'sem-reloaded'), 'wp-content' . $header)
							. "</strong>"
						. "</p>\n"
						. "</div>\n";
					return;
				} elseif ( !@unlink(WP_CONTENT_DIR . $header) ) {
					echo '<div class="error">'
						. "<p>"
							. "<strong>"
							. sprintf(__('Failed to delete %s.', 'sem-reloaded'), 'wp-content' . $header)
							. "</strong>"
						. "</p>\n"
						. "</div>\n";
					return;
				}
			}

			preg_match("/\.([^.]+)$/", $_FILES['header_file']['name'], $ext);
			$ext = end($ext);
			$ext = strtolower($ext);

			if ( !in_array($ext, defined('GLOB_BRACE') ? array('jpg', 'jpeg', 'png', 'gif') : array('jpg', 'jpeg')) ) {
				echo '<div class="error">'
					. "<p>"
						. "<strong>"
						. __('Invalid File Type.', 'sem-reloaded')
						. "</strong>"
					. "</p>\n"
					. "</div>\n";
				return;
			} else {
				$entropy = intval(get_site_option('sem_entropy')) + 1;
				update_site_option('sem_entropy', $entropy);
				
				$name = WP_CONTENT_DIR . header::get_basedir() . '/header-' . $entropy . '.' . $ext;
				
				@move_uploaded_file($_FILES['header_file']['tmp_name'], $name);
				
				$stat = stat(dirname($name));
				$perms = $stat['mode'] & 0000666;
				@chmod($name, $perms);
			}
		} elseif ( $header && isset($_POST['delete_header']) && strpos($header, "/skins/$active_skin/") === false ) {
			if ( !is_writable(WP_CONTENT_DIR . $header) ) {
				echo '<div class="error">'
					. "<p>"
						. "<strong>"
						. sprintf(__('%s is not writable.', 'sem-reloaded'), 'wp-content' . $header)
						. "</strong>"
					. "</p>\n"
					. "</div>\n";
				return;
			} elseif ( !@unlink(WP_CONTENT_DIR . $header) ) {
				echo '<div class="error">'
					. "<p>"
						. "<strong>"
						. sprintf(__('Failed to delete %s.', 'sem-reloaded'), 'wp-content' . $header)
						. "</strong>"
					. "</p>\n"
					. "</div>\n";
				return;
			}
		}
		
		delete_transient('sem_header');
		
		echo '<div class="updated fade">'
			. '<p><strong>'
			. __('Settings saved.', 'sem-reloaded')
			. '</strong></p>'
			. '</div>' . "\n";
	} # save_options()
	
	
	/**
	 * edit_options()
	 *
	 * @return void
	 **/
	
	static function edit_options() {
		echo '<div class="wrap">';
		
		echo '<form enctype="multipart/form-data" method="post" action="">';
		
		wp_nonce_field('sem_header');
		
		global $sem_options;
		
		$header = header::get();
		$active_skin = $sem_options['active_skin'];
		
		echo '<h2>' . __('Manage Header', 'sem-reloaded') . '</h2>' . "\n";
		
		echo '<p>'
			. __('The header\'s height will automatically adjust to fit your image. The width to use will depend on your <a href="admin.php?page=layout">layout</a>\'s canvas width, and on your <a href="admin.php?page=skin">skin</a> (strip 20px if you\'re using the Kubrick skin).', 'sem-reloaded')
			. '</p>' . "\n";
		
		if ( $header ) {
			echo '<h3>' . __('Current Header', 'sem-reloaded') . '</h3>';

			echo '<p>' . header::display_image($header) . '</p>' . "\n";
			
			if ( is_writable(WP_CONTENT_DIR . $header) ) {
				echo '<p>'
					. '<label>'
					. '<input type="checkbox" name="delete_header" />'
					. '&nbsp;'
					. __('Delete Header', 'sem-reloaded')
					. '</label>'
					. '</p>' . "\n";
				
				echo '<div class="submit">'
					. '<input type="submit" value="' . esc_attr(__('Save Changes', 'sem-reloaded')) . '" />'
					. '</div>' . "\n";
			} elseif ( strpos($header, "/skins/$active_skin/") !== false ) {
				echo '<p>'
					. sprintf(__('This header (%s) is hard-coded in your <a href="admin.php?page=skin">skin</a>. You cannot delete it, but you can override it.', 'sem-reloaded'), 'wp-content' . $header)
					. '</p>' . "\n";
			} else {
				echo '<p>'
					. sprintf(__('This header (%s) is not writable by the server. Please delete it manually to change it.', 'sem-reloaded'), 'wp-content' . $header)
					. '</p>' . "\n";
			}
		}
		
		$site_basedir = header::get_basedir();
		wp_mkdir_p(WP_CONTENT_DIR . $site_basedir);
		
		if ( !$header || is_writable(WP_CONTENT_DIR . $header) || strpos($header, "/skins/$active_skin/") !== false ) {
			if ( is_writable(WP_CONTENT_DIR . $site_basedir) ) {
				echo '<h3>'
					. '<label for="header_file">'
						. ( defined('GLOB_BRACE')
							? __('Upload a New Header (jpg, jpeg, png, gif)', 'sem-reloaded')
							: __('Upload a New Header (jpg, jpeg)', 'sem-reloaded')
							)
						. '</label>'
					. '</h3>' . "\n";
				
				echo '<p>'
					. '<input type="file" class="widefat" id="header_file" name="header_file" />'
					. '</p>' . "\n";
			} elseif ( !is_writable(WP_CONTENT_DIR) ) {
				echo '<p>'
					. __('Your wp-content folder is not writable by the server', 'sem-reloaded')
					. '</p>' . "\n";
			} else {
				echo '<p>'
					. __('Your wp-content/header folder is not writable by the server', 'sem-reloaded')
					. '</p>' . "\n";
			}
			
			echo '<p>'
				. sprintf(__('Maximum file size is %s based on your server\'s configuration.', 'sem-reloaded'), size_format(apply_filters('import_upload_size_limit', wp_max_upload_size())))
				. '</p>' . "\n";
			
			echo '<div class="submit">'
				. '<input type="submit" value="' . esc_attr(__('Save Changes', 'sem-reloaded')) . '" />'
				. '</div>' . "\n";
		}
		
		echo '</form>' . "\n";
		
		echo '</div>' . "\n";
	} # edit_options()
	
	
	/**
	 * edit_entry()
	 *
	 * @param object $post
	 * @return void
	 **/
	
	static function edit_entry($post)
	{
		$post_ID = $post->ID;
		
		if ( defined('GLOB_BRACE') ) {
			$header_scan = "header{,-*}.{jpg,jpeg,png,gif}";
			$scan_type = GLOB_BRACE;
		} else {
			$header_scan = "header-*.{jpg,jpeg}";
			$scan_type = false;
		}
		
		$header = glob(WP_CONTENT_DIR . header::get_basedir() . "/$post_ID/$header_scan", $scan_type);
		
		if ( $header ) {
			$header = current($header);
			$header = str_replace(WP_CONTENT_DIR, '', $header);
		} else {
			$header = false;
		}
		
		if ( $header ) {
			echo '<h4>'
				. __('Current Header', 'sem-reloaded')
				. '</h4>' . "\n";

			echo '<div style="overflow: hidden;">' . "\n";

			echo '<p>' . header::display_image($header) . '</p>' . "\n";
			
			echo '</div>' . "\n";
			
			if ( is_writable(WP_CONTENT_DIR . $header) ) {
				echo '<p>'
					. '<label>'
					. '<input type="checkbox" name="delete_header" />'
					. '&nbsp;'
					. __('Delete Header', 'sem-reloaded')
					. '</label>'
					. '</p>' . "\n";
				
				echo '<p>'
					. '<input type="submit" name="save" class="button" tabindex="5" value="' . esc_attr(__('Save', 'sem-reloaded')) . '" />'
					. '</p>' . "\n";
			} else {
				echo '<p>'
					. sprintf(__('This header (%s) is not writable by the server. Please delete it manually to change it.', 'sem-reloaded'), 'wp-content' . $header)
					. '</p>' . "\n";
			}
		}
		
		wp_mkdir_p(WP_CONTENT_DIR . header::get_basedir());
		
		if ( !$header || is_writable(WP_CONTENT_DIR . $header) ) {
			if ( is_writable(WP_CONTENT_DIR . '/header') ) {
				echo '<h4>'
					. '<label for="header_file">'
						. ( defined('GLOB_BRACE')
							? __('Upload a New Header (jpg, jpeg, png, gif)', 'sem-reloaded')
							: __('Upload a New Header (jpg, jpeg)', 'sem-reloaded')
							)
						. '</label>'
					. '</h4>' . "\n";
				
				echo '<p>'
					. '<input type="file" id="header_file" name="header_file" />'
					. '&nbsp;'
					. '<input type="submit" name="save" class="button" tabindex="5" value="' . esc_attr(__('Save', 'sem-reloaded')) . '" />'
					. '</p>' . "\n";
			} elseif ( !is_writable(WP_CONTENT_DIR) ) {
				echo '<p>'
					. __('Your wp-content folder is not writable by the server', 'sem-reloaded')
					. '</p>' . "\n";
			} else {
				echo '<p>'
					. __('Your wp-content/header folder is not writable by the server', 'sem-reloaded')
					. '</p>' . "\n";
			}
			
			echo '<p>'
				. sprintf(__('Maximum uploadable file size is %s based on your server\'s configuration.', 'sem-reloaded'), size_format(apply_filters('import_upload_size_limit', wp_max_upload_size())))
				. '</p>' . "\n";
		}
	} # edit_entry()


    /**
     * save_entry()
     *
     * @param $post_id
     * @internal param int $post_ID
     * @return void
     */
	
	function save_entry($post_id) {
		if ( !$_POST || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id) )
			return;
		
		$post_id = (int) $post_id;
		
		if ( defined('GLOB_BRACE') ) {
			$header_scan = "header{,-*}.{jpg,jpeg,png,gif}";
			$scan_type = GLOB_BRACE;
		} else {
			$header_scan = "header-*.{jpg,jpeg}";
			$scan_type = false;
		}
		
		$header = glob(WP_CONTENT_DIR . header::get_basedir() . "/$post_id/$header_scan", $scan_type);
		
		if ( $header ) {
			$header = current($header);
			$header = str_replace(WP_CONTENT_DIR, '', $header);
		} else {
			$header = false;
		}
		
		if ( @ $_FILES['header_file']['name'] ) {
			preg_match("/\.([^.]+)$/", $_FILES['header_file']['name'], $ext);
			$ext = strtolower(end($ext));
			
			if ( !in_array($ext, defined('GLOB_BRACE') ? array('jpg', 'jpeg', 'png', 'gif') : array('jpg', 'jpeg')) ) {
				return;
			} elseif ( !wp_mkdir_p(WP_CONTENT_DIR . header::get_basedir() . '/' .  $post_id) ) {
				return;
			} elseif ( $header && !@unlink(WP_CONTENT_DIR . $header) ) {
				return;
			}
			
			$entropy = intval(get_site_option('sem_entropy')) + 1;
			update_site_option('sem_entropy', $entropy);
			
			$name = WP_CONTENT_DIR . header::get_basedir() . '/' . $post_id . '/header-' . $entropy . '.' . $ext;
			@move_uploaded_file($_FILES['header_file']['tmp_name'], $name);
			
			$stat = stat(dirname($name));
			$perms = $stat['mode'] & 0000666;
			@chmod($name, $perms);
			
			delete_post_meta($post_id, '_sem_header');
		} elseif ( $header && isset($_POST['delete_header']) ) {
			if ( !@unlink(WP_CONTENT_DIR . $header) ) {
				return;
			}
			if ( !@rmdir(WP_CONTENT_DIR . header::get_basedir() . '/' .  $post_id) ) {
				return;
			}
			delete_post_meta($post_id, '_sem_header');
		}
	} # save_entry()
} # sem_header

//$sem_header = new sem_header();
sem_header::get_instance();