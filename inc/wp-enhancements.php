<?php

/**
 * enable_php_code()
 *
 * @param $text string
 * @return string
 */
function enable_php_code ($text) {
    if (strpos($text, '<' . '?') !== false) {
        ob_start();
        @eval('?' . '>' . $text);
        $text = ob_get_contents();
        ob_end_clean();
    }
    return $text;
}

add_filter('widget_text', 'enable_php_code', 99);

/**
 * Filter to allow shortcodes in text widgets
 *
 */
global $wp_embed;

add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode', 11);

// embed trick props http://daisyolsen.com/
add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'widget_text', array( $wp_embed, 'autoembed'), 8 );

?>