<?php
#
# DO NOT EDIT THIS FILE
# ---------------------
# You would lose your changes when you upgrade your site. Use php widgets instead.
#


/*
Template Name: Sales Letter
*/

add_filter('active_layout', array('sem_template', 'force_letter'));
remove_action('wp_footer', array('sem_template', 'display_credits'));

# show header
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>><head><title><?php
if ( $title = trim(wp_title('&#8211;', false)) ) {
	if ( strpos($title, '&#8211;') === 0 )
		$title = trim(substr($title, strlen('&#8211;')));
	echo $title;
} else {
	bloginfo('description');
}
?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php _e('RSS feed'); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php do_action('wp_head'); ?>
</head>
<body class="<?php echo implode(' ', get_body_class(array('skin', 'custom'))); ?>">

<div id="wrapper">
<div id="wrapper_top"><div class="hidden"></div></div>
<div id="wrapper_bg">
<?php
# show header
header::letter();
?>
<div class="pad">
<?php
sem_panels::display('before_the_entries');

# show posts
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

?>
<div class="entry" id="entry-<?php the_ID(); ?>">
<?php
		sem_panels::display('the_entry');
?>
</div>
<?php
	endwhile;
# or fallback
elseif ( is_404() ) :
	sem_panels::display('the_404');
endif;

sem_panels::display('after_the_entries');
?>
</div>
</div>
<div id="wrapper_bottom"><div class="hidden"></div></div>
</div><!-- wrapper -->
<?php

# show footer
do_action('wp_footer');
?>
</body>
</html>