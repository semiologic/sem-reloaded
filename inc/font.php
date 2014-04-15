<?php
/**
 * sem_font
 *
 * @package Semiologic Reloaded
 **/

class sem_font {
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
        add_action('semiologic_page_font', array($this, 'save_options'), 0);
        add_action('admin_head', array($this, 'admin_head'));
    }

    /**
	 * admin_head()
	 *
	 * @return void
	 **/

	function admin_head() {
		echo <<<EOS

<link href='http://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Roboto:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=PT+Sans:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Lato:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Merriweather:400' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Lora:400' rel='stylesheet' type='text/css'>

<style type="text/css">

.antica,
.bookman {
	font-family: Palatino, "Book Antica", "Palatino Linotype", "URW Palladio L", Palladio, Georgia, "DejaVu Serif", Serif;
	font-size: 14px;
}

.arial {
	font-family: Arial, "Liberation Sans", "Nimbus Sans L", "DejaVu Sans", Sans-Serif;
	font-size: 14px;
}

.courier {
	font-family: "Courier New", "Liberation Mono", "Nimbus Mono L", Monospace;
	font-size: 14px;
}

.georgia {
	font-family: Georgia, "New Century Schoolbook", "Century Schoolbook L", "DejaVu Serif", Serif;
	font-size: 14px;
}

.helvetica {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 14px;
}

.lucida {
    font-family: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Geneva, Verdana, sans-serif;
    font-size: 14px;
}

.tahoma {
	font-family: Tahoma, "Nimbus Sans L", "DejaVu Sans", Sans-Serif;
	font-size: 14px;
}

.times {
	font-family: "Times New Roman", Times, "Liberation Serif", "DejaVu Serif Condensed", Serif;
	font-size: 14px;
}

.trebuchet {
	font-family: "Trebuchet MS", "Nimbus Sans L", "DejaVu Sans", Sans-Serif;
	font-size: 14px;
}

.verdana {
	font-family: Verdana, "Nimbus Sans L", "DejaVu Sans", Sans-Serif;
	font-size: 14px;
}

.lato {
	font-family: 'Lato', sans-serif;
	font-size: 14px;
}

.lora {
	font-family: 'Lora', serif;
	font-size: 14px;
}

.merriweather {
	font-family: 'Merriweather', serif;
	font-size: 14px;
}

.open_sans {
	font-family: 'Open Sans', sans-serif;
	font-size: 14px;
}

.pt_sans {
	font-family: 'PT Sans', sans-serif;
	font-size: 14px;
}

.roboto {
	font-family: 'Roboto', sans-serif;
	font-size: 14px;
}

.source_sans_pro {
	font-family: 'Source Sans Pro', sans-serif;
	font-size: 14px;
}

.ubuntu {
	font-family: 'Ubuntu', sans-serif;
	font-size: 14px;
}

</style>
EOS;
	} # admin_head()

	/**
	 * save_options()
	 *
	 * @return void
	 **/
	
	function save_options() {
		if ( !$_POST || !current_user_can('switch_themes') )
			return;
		
		check_admin_referer('sem_font');
		
		global $sem_options;

		$sem_options['active_font'] = preg_replace("/[^a-z0-9_-]/i", "", $_POST['font']);
		
		update_option('sem6_options', $sem_options);
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
		echo '<div class="wrap">' . "\n";
		echo '<form method="post" action="" id="option_picker">' . "\n";
		
		wp_nonce_field('sem_font');
		
		global $sem_options;
		$standard_fonts = sem_font::get_fonts('standard');
		$google_fonts = sem_font::get_fonts('google');
		$fonts = sem_font::get_fonts();
		
		echo '<h2>' . __('Manage Font', 'sem-reloaded') . '</h2>' . "\n";
		
		echo '<h3>' . __('Current Font', 'sem-reloaded') . '</h3>' . "\n";

		$font = '<span class="' . esc_attr($sem_options['active_font']) . '">'
			. $fonts[$sem_options['active_font']]
			. '</span>';

		echo '<p>'
			. sprintf(__('Font Family: %s.', 'sem-reloaded'), $font)
			. '</p>' . "\n";
		
		echo '<p style="font-size: larger;"><i>' . __('Select a new font below to set as the default font on your site.', 'sem-reloaded') . '</i></p>' . "\n";
		echo '<p>&nbsp;</p>' . "\n";

		echo '<h3>' . __('Standard Web Safe Fonts', 'sem-reloaded') . '</h3>' . "\n";

		echo '<ul>' . "\n";
		
		foreach ( $standard_fonts as $k => $v ) {
			echo '<li class="' . esc_attr($k) . '">'
				. '<label>'
				. '<input type="radio" name="font" value="' . $k . '"'
					. checked($sem_options['active_font'], $k, false)
					. '/>'
				. '&nbsp;'
				. $v
				. '</label>'
				. '</li>' . "\n";
		}
		
		echo '</ul>' . "\n";

		echo '<h3>' . __('Google Fonts', 'sem-reloaded') . '</h3>' . "\n";

		echo '<ul>' . "\n";

		foreach ( $google_fonts as $k => $v ) {
			echo '<li class="' . esc_attr($k) . '">'
				. '<label>'
				. '<input type="radio" name="font" value="' . $k . '"'
					. checked($sem_options['active_font'], $k, false)
					. '/>'
				. '&nbsp;'
				. $v
				. '</label>'
				. '</li>' . "\n";
		}

		echo '</ul>' . "\n";

		echo '<div class="submit">'
			. '<input type="submit" value="' . esc_attr(__('Save Changes', 'sem-reloaded')) . '" />'
			. '</div>' . "\n";

		echo '</form>' . "\n";
		echo '</div>' . "\n";
	} # edit_options()

	
	/**
	 * get_fonts()
	 *
	 * @return array $fonts
	 **/

	static function get_fonts($type = 'all') {
		$standard = array(
			'' =>  __('The default font stack as defined by your skin', 'sem-reloaded'),
			'antica' => __('Antica stack: Palatino, "Book Antica", "Palatino Linotype", "URW Palladio L", Palladio, Georgia, "DejaVu Serif", Serif', 'sem-reloaded'),
			'arial' => __('Arial stack: Arial, "Liberation Sans", "Nimbus Sans L", "DejaVu Sans", Sans-Serif', 'sem-reloaded'),
			'courier' => __('Courier stack: "Courier New", "Liberation Mono", "Nimbus Mono L", Monospace', 'sem-reloaded'),
			'georgia' => __('Georgia stack: Georgia, "New Century Schoolbook", "Century Schoolbook L", "DejaVu Serif", Serif', 'sem-reloaded'),
			'helvetica' => __('Helvetica stack: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif', 'sem-reloaded'),
            'lucida' => __('Lucida stack: "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Geneva, Verdana, Sans-Serif', 'sem-reloaded'),
			'tahoma' => __('Tahoma stack: Tahoma, "Nimbus Sans L", "DejaVu Sans", Sans-Serif', 'sem-reloaded'),
			'times' => __('Times stack: "Times New Roman", Times, "Liberation Serif", "DejaVu Serif Condensed", Serif', 'sem-reloaded'),
			'trebuchet' =>  __('Trebuchet stack: "Trebuchet MS", "Nimbus Sans L", "DejaVu Sans", Sans-Serif', 'sem-reloaded'),
			'verdana' =>  __('Verdana stack: Verdana, "Nimbus Sans L", "DejaVu Sans", Sans-Serif', 'sem-reloaded'),
		);

		$google = array(
			'lato' => __('Lato (Google Fonts) stack: "Lato", sans-serif', 'sem-reloaded'),
			'lora' => __('Lora (Google Fonts) stack: "Lora", serif', 'sem-reloaded'),
			'merriweather' => __('Merriweather (Google Fonts) stack: "Merriweather", Serif', 'sem-reloaded'),
			'open_sans' => __('Open Sans (Google Fonts) stack: "Open Sans", sans-serif', 'sem-reloaded'),
			'pt_sans' => __('PT Sans (Google Fonts) stack: "PT Sans", sans-serif', 'sem-reloaded'),
			'roboto' => __('Roboto (Google Fonts) stack: "Roboto", sans-serif', 'sem-reloaded'),
			'source_sans_pro' => __('Source Sans Pro (Google Fonts) stack: "Source Sans Pro", sans-serif', 'sem-reloaded'),
			'ubuntu' => __('Ubuntu (Google Fonts) stack: "Ubuntu", sans-serif', 'sem-reloaded'),
		);

		switch ( $type ) {
			case 'standard':
				$fonts = $standard;
				break;
			case 'google':
				$fonts = $google;
				break;
			default:
				$fonts =  array_merge( $standard, $google);
				break;
		}

		return $fonts;
	} # get_fonts()
} # sem_font

//$sem_font = new sem_font();
sem_font::get_instance();