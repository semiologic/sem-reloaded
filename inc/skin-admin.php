<?php
/**
 * sem_skin_admin
 *
 * @package Semiologic Reloaded
 **/

add_action('appearance_page_skin', array('sem_skin_admin', 'save_options'), 0);
add_action('admin_head', array('sem_skin_admin', 'admin_head'));
wp_enqueue_script('jquery');

class sem_skin_admin {
	/**
	 * admin_head()
	 *
	 * @return void
	 **/
	
	function admin_head() {
		echo <<<EOS

<style type="text/css">
#current_option img {
	border: solid 1px #999;
	float: left;
	clear: right;
	margin-right: 10px;
}

.current_option_details th {
	text-align: left;
	padding-right: 5px;
}

.available_option {
	text-align: center;
	width: 275px;
}

.available_option img {
	border: solid 1px #ccc;
}

.available_option label {
	cursor: pointer !important;
}

#available_options {
	border-collapse: collapse;
}

#available_options td {
	padding: 10px;
	border: solid 1px #ccc;
}

#available_options td.top {
	border-top: none;
}

#available_options td.bottom {
	border-bottom: none;
}

#available_options td.left {
	border-left: none;
}

#available_options td.right {
	border-right: none;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#available_options label').click(function() {
		jQuery(this).closest('td').find('input').attr('checked', 'checked');
		jQuery('#option_picker').trigger('submit');
	});
});
</script>

<style type="text/css">
.skin {
	font-family: "Trebuchet MS", Tahoma, Helvetica, Sans-Serif;
	font-size: .9em;
}

.antica {
	font-family: "Book Antica", Times, Serif;
	font-size: 1em;
}

.arial {
	font-family: Arial, Helvetica, Sans-Serif;
	font-size: .9em;
}

.bookman {
	font-family: "Bookman Old Style", Times, Serif;
	font-size: .9em;
}

.comic {
	font-family: "Comic Sans MS", Helvetica, Sans-Serif;
	font-size: .9em;
}

.corsiva {
	font-family: "Monotype Corsiva", Courier, Monospace;
	font-size: 1.1em;
}

.courier {
	font-family: "Courier New", Courier, Monospace;
	font-size: .9em;
}

.garamond {
	font-family: Garamond, Times, Serif;
	font-size: 1em;
}

.georgia {
	font-family: Georgia, Times, Serif;
	font-size: .9em;
}

.tahoma {
	font-family: Tahoma, Helvetica, Sans-Serif;
	font-size: .9em;
}

.times {
	font-family: "Times New Roman", Times, Serif;
	font-size: 1em;
}

.trebuchet {
	font-family: "Trebuchet MS", Tahoma, Helvetica, Sans-Serif;
	font-size: .9em;
}

.verdana {
	font-family: Verdana, Helvetica, Sans-Serif;
	font-size: .85em;
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
		if ( !$_POST )
			return;
		
		check_admin_referer('sem_skin');
		
		global $sem_options;
		
		$sem_options['active_skin'] = preg_replace("/[^a-z0-9_-]/i", "", $_POST['skin']);
		$sem_options['skin_data'] = sem_template::get_skin_data($sem_options['active_skin']);
		$sem_options['active_font'] = preg_replace("/[^a-z0-9_-]/i", "", $_POST['font']);
		
		update_option('sem6_options', $sem_options);
	} # save_options()
	
	
	/**
	 * edit_options()
	 *
	 * @return void
	 **/
	
	function edit_options() {
		echo '<div class="wrap">' . "\n";
		echo '<form method="post" action="" id="option_picker">' . "\n";
		
		wp_nonce_field('sem_skin');
		
		global $sem_options;
		$skins = sem_skin_admin::get_skins();
		$fonts = sem_skin_admin::get_fonts();
		
		screen_icon();
		
		echo '<h2>' . __('Manage Skin &amp; Font', 'sem-reloaded') . '</h2>' . "\n";
		
		echo '<h3>' . __('Current Skin &amp; Font', 'sem-reloaded') . '</h3>' . "\n";
		
		$details = $skins[$sem_options['active_skin']];
		$screenshot = sem_url . '/skins/' . $sem_options['active_skin'] . '/screenshot.png';
		$title = __('%1$s v.%2$s by %3$s', 'sem-reloaded');
		$name = $details['uri']
			? ( '<a href="' . esc_url($details['uri']) . '"'
				. ' title="' . esc_attr(__('Visit the skin\' page', 'sem-reloaded')) . '">'
				. $details['name']
				. '</a>' )
			: $details['name'];
		$author = $details['author_uri']
			? ( '<a href="' . esc_url($details['author_uri']) . '"'
				. ' title="' . esc_attr(__('Visit the skin authors\' site', 'sem-reloaded')) . '">'
				. $details['author_name']
				. '</a>' )
			: $details['author_name'];
		
		echo '<div id="current_option">' . "\n";
		
		echo '<img src="' . esc_url($screenshot) . '" alt="' . esc_attr(sprintf($title, $details['name'], $details['version'], $details['author'])) . '" />' . "\n";
		
		echo '<h4>' . sprintf($title, $name, $details['version'], $author) . '</h4>';
		
		$font = '<span class="' . $sem_options['active_font'] . '">'
			. $fonts[$sem_options['active_font']]
			. '</span>';
		
		echo '<p>'
			. sprintf(__('Font: %s', 'sem-reloaded'), $font)
			. '</p>' . "\n";
		
		if ( $details['description'] ) {
			echo wpautop($details['description']);
		}
		
		if ( $details['tags'] ) {
			echo '<p>'
				. sprintf(__('Tags: %s', 'sem-reloaded'), implode(',', $details['tags']))
				. '</p>' . "\n";
		}
		
		echo '<div style="clear: both;"></div>' . "\n";
		
		echo '</div>' . "\n";
		
		echo '<h3>' . __('Available Skins', 'sem-reloaded') . '</h3>' . "\n";
		
		echo '<p class="hide-if-no-js">'
			. __('Click on a skin below to activate it.', 'sem-reloaded')
			. '</p>' . "\n";
		
		echo '<table id="available_options" cellspacing="0" cellpadding="0">' . "\n";
		
		$row_size = 3;
		$num_rows = ceil(count($skins) / $row_size);
		
		$i = 0;
		
		foreach ( $skins as $skin => $details ) {
			if ( $i && !( $i % $row_size ) )
				echo '</tr>' . "\n";
			
			if ( !( $i % $row_size ) )
				echo '<tr>' . "\n";
			
			$classes = array('available_option');
			if ( ceil(( $i + 1 ) / $row_size) == 1 )
				$classes[] = 'top';
			if ( ceil(( $i + 1 ) / $row_size) == $num_rows )
				$classes[] = 'bottom';
			if ( !( $i % $row_size ) )
				$classes[] = 'left';
			elseif ( !( ( $i + 1 ) % $row_size ) )
				$classes[] = 'right';
			
			$i++;
			
			echo '<td class="' . implode(' ', $classes) . '">' . "\n";
			
			$screenshot = sem_url . '/skins/' . $skin . '/screenshot.png';
			
			echo '<p>'
				. '<label for="skin-' . $skin . '">'
				. '<img src="' . esc_url($screenshot) . '" alt="' . esc_attr($details['name']) . '"/>'
				. '</label>'
				. '</p>' . "\n"
				. '<h4>'
				. '<label for="skin-' . $skin . '">'
				. '<span class="hide-if-js">'
				. '<input type="radio" name="skin" value="' . $skin . '" id="skin-' . $skin . '"'
					. checked($sem_options['active_skin'], $skin, false)
					. ' />' . '&nbsp;' . "\n"
				. '</span>'
				. $details['name']
				. '</label>'
				. '</h4>' . "\n";
			
			echo '</td>' . "\n";
		}
		
		while ( $i % $row_size ) {
			$classes = array('available_option');
			if ( ceil(( $i + 1 ) / $row_size) == 1 )
				$classes[] = 'top';
			if ( ceil(( $i + 1 ) / $row_size) == $num_rows )
				$classes[] = 'bottom';
			if ( !( $i % $row_size ) )
				$classes[] = 'left';
			elseif ( !( ( $i + 1 ) % $row_size ) )
				$classes[] = 'right';
			
			$i++;
			
			echo '<td class="' . implode(' ', $classes) . '">&nbsp;</td>' . "\n";
		}
		
		echo '</tr>' . "\n";
			
		
		echo '</table>' . "\n";
		
		echo '<p class="submit hide-if-js">'
			. '<input type="submit" value="' . esc_attr(__('Save Changes', 'sem-reloaded')) . '" />'
			. '</p>' . "\n";
		
		echo '<h3>' . __('Font Settings') . '</h3>' . "\n";
		
		echo '<p>' . __('This will let you set the default font on your site.') . '</p>' . "\n";
		
		echo '<ul>' . "\n";
		
		foreach ( $fonts as $k => $v ) {
			echo '<li class="' . ( $k ? $k : 'skin' ) . '">'
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
	 * get_skins()
	 *
	 * @return array $skins
	 **/

	function get_skins() {
		$skins = array();
		$handle = @opendir(sem_path . '/skins');

		if ( !$handle )
			return array();

		while ( ($skin = readdir($handle) ) !== false ) {
			if ( in_array($skin, array('.', '..')) )
				continue;
			
			$file = sem_path . "/skins/$skin/skin.css";
			if ( !is_file($file) || !is_readable($file) )
				continue;
			
			$skins[$skin] = sem_template::get_skin_data($skin);
		}

		return $skins;		
	} # get_skins()
	
	
	/**
	 * get_fonts()
	 *
	 * @return array $fonts
	 **/

	function get_fonts() {
		return array(
			'' =>  __('The skin\'s default', 'sem-reloaded'),
			'arial' => __('Arial, Helvetica, Sans-Serif', 'sem-reloaded'),
			'antica' => __('Book Antica, Times, Serif', 'sem-reloaded'),
			'bookman' => __('Bookman Old Style, Times, Serif', 'sem-reloaded'),
			'comic' => __('Comic Sans MS, Helvetica, Sans-Serif', 'sem-reloaded'),
			'courier' => __('Courier New, Courier, Monospace', 'sem-reloaded'),
			'garamond' => __('Garamond, Times, Serif', 'sem-reloaded'),
			'georgia' => __('Georgia, Times, Serif', 'sem-reloaded'),
			'corsiva' => __('Monotype Corsiva, Courier, Monospace', 'sem-reloaded'),
			'tahoma' => __('Tahoma, Helvetica, Sans-Serif', 'sem-reloaded'),
			'times' => __('Times New Roman, Times, Serif', 'sem-reloaded'),
			'trebuchet' => __('Trebuchet MS, Tahoma, Helvetica, Sans-Serif', 'sem-reloaded'),
			'verdana' => __('Verdana, Helvetica, Sans-Serif', 'sem-reloaded'),
			);
	} # get_fonts()
} # sem_skin_admin
?>