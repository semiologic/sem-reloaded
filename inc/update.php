<?php
/**
 * sem_update
 *
 * @package Semiologic Reloaded
 **/

class sem_update {
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
	 * Plugin setup.
	 *
	 * @since  0.5.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
        //if ( !empty($_REQUEST['action']) && ($_REQUEST['action'] == 'upgrade-theme' || $_REQUEST['action'] == 'update-selected-themes') ) {
            add_filter('upgrader_source_selection', array($this, 'upgrader_source_selection'), 10, 3);
        //}
    }

    /**
     * upgrader_source_selection()
     *
     * @param $source
     * @param $remote_source
     * @param $wp_upgrader
     * @internal param mixed $in
     * @return $in
     */

	function upgrader_source_selection($source, $remote_source, $wp_upgrader) {
		global $wp_filesystem;

        // check this is our theme
//        if ( 'sem-reloaded' != $wp_upgrader->skin->theme_info->stylesheet )
//            return $source;

		$old = sem_path;
		$new = untrailingslashit($source);

		show_message(__('Importing Semiologic Reloaded Customizations', 'sem-reloaded'));

		# copy user customizations
		foreach ( array('custom.css', 'custom.php') as $file ) {
			if ( file_exists("$old/$file") )
				$wp_filesystem->copy("$old/$file", "$new/$file");
		}
		
		if ( is_dir("$old/custom") ) {
			$wp_filesystem->mkdir("$new/custom");
			copy_dir("$old/custom", "$new/custom");
		}

        // copy any user templates
       $templates = get_page_templates();
       $semio_templates = array('letter.php', 'monocolumn.php', 'special.php');
       foreach ( $templates as $template_name => $template_filename ) {
            if (in_array($template_filename, $semio_templates))
                continue;
            $wp_filesystem->copy("$old/$template_filename", "$new/$template_filename");
       }


		$handle = @opendir("$old/skins");
		
		if ( !$handle )
			return $source;
		
		while ( ( $skin = readdir($handle) ) !== false ) {
			if ( in_array($skin, array('.', '..')) )
				continue;

            // skip any files directly in the skin directory
            if ( is_file($skin) )
                continue;

            if ( !is_dir("$new/skins") )
            	$wp_filesystem->mkdir("$new/skins");

			if ( !is_dir("$new/skins/$skin") ) {
				$wp_filesystem->mkdir("$new/skins/$skin");
				copy_dir("$old/skins/$skin", "$new/skins/$skin");
			} else {
				foreach ( array('custom.css', 'letter.css', 'print.css') as $file ) {
					if ( file_exists("$old/skins/$skin/$file") )
						$wp_filesystem->copy("$old/skins/$skin/$file", "$new/skins/$skin/$file");
				}
				
				if ( is_dir("$old/skins/$skin/custom") ) {
					$wp_filesystem->mkdir("$new/skins/$skin/custom");
					copy_dir("$old/skins/$skin/custom", "$new/skins/$skin/custom");
				}
			}
		}
		
		closedir($handle);
		
		return $source;
	} # upgrader_source_selection()
} # sem_update


//$sem_update = new sem_update();
sem_update::get_instance();