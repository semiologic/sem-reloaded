=== Semiologic theme ===
Contributors: Denis-de-Bernardy, Mike_Koepke
Donate link: http://www.semiologic.com/partners/
Tags: semiologic, wordpress theme
Requires at least: 3.2
Tested up to: 3.8
Stable tag: trunk

The Semiologic theme, Reloaded for WordPress.


== Description ==

The Semiologic theme, Reloaded for WordPress.


= Help Me! =

The [Semiologic forum](http://forum.semiologic.com) is the best place to report issues.


== Installation ==

1. Upload the plugin folder to the `/wp-content/themes/` directory
2. Activate the theme through the 'Themes' menu in WordPress


== Change Log ==

= 2.0 =

- Fix: Sidebar widget lists with long post/page titles don't overflow widget boundaries
- 404, Search and Archive pages now set with .entry class for consistency with blog, page, home and single post pages

= 1.3 =

- Page-specific header functionality was creating a bogus empty extra folder (headerNNNN) in the wp-content directory.  These folders can simply be removed.
- Deleting a page-specific now removes the subfolder under /wp-content/header/NNNN.
- WP 3.8 compat

= 1.2.1 =

- Disabled editor style for now as still too quirky in the presentation

= 1.2 =

- Initial implementation of editor style to match select skin and fonts
- Compressed presentation of skin and layout pages
- Added missing required WP css class rules
- New themes added in version 1.0 and 1.1 are now available as choices in the custom css editor.
- Added new .main_content and .sidebar_content css class for additional skinning options
- Use size_format in place of deprecated wp_convert_bytes_to_hr
- WP 3.6 compat
- PHP 5.4 compat

= 1.1 =

- Alter css encoding for header image shadow for W3C compliance
- Fix hardcoded font-family for sitename, h1, h2 and calendar caption
- Embellished Helvetica font stack
- Fixed incorrect url being generated for hierarchies with children of children in Pages, Nav-Menus and Silo widgets.  url was being generated as parent/grandparent/child
- Don't show featured image post thumbnail on a single post page
- Setting a custom background color now fully extends to the top and bottom of the screen.  (Top and bottom white stripes are now gone)

= 1.0.3 =

- Fixed shadow bug in header image on some skins I broke in 1.0

= 1.0.2 =

- Added template files aren't being preserved upon updating

= 1.0.1 =

- Fix .entry link overflow wrapping side effect with Firefox

= 1.0 =

- WP 3.5 compatibility
- Add WP 3.4 theme support
- Microformat support in the theme
- Author byline under post/page title
- Author name now links to author page
- Author page now support author image and additional css skinning options
- Post feature image can now be disabled for post entry.
- PHP code now natively supported in text widgets.
- Text widgets now allow shortcodes including video embeds
- Convert to HTML5 HTML Headers
- Added Helvetica and Lucinda font stacks
- Added post/page-post_name css class for more specific post/page skinning
- Theme upgrades now preserve custom skins and added template files
- W3C HTML and CSS Validation improvements
- Replace deprecated WP functions
- Fix .entry link overflow wrapping
- Fix php lint warnings and errors
- Fix unknown index warnings


= 0.9.7 =

- Add a unique class to widgets
- jQuery compat / Nav Menus

= 0.9.6 =

- Header optimizations

= 0.9.5 =

- Fix a bug when dealing with headers

= 0.9.4 =

- WP 3.0.1 compat

= 0.9.3 =

- WP 3.0 compat
- Integrate WP 3.0 custom background handler

= 0.9.2 =

- Autofix panels when moving in and out of themes
- Change default menu items: sections in navbar, non-sections in footer
- Fix the check for a custom letter.css
- Fix WP's built-in post thumbnail processing (i.e. downsize them automatically)
- Fix a potential infinite loop

= 0.9.1 =

- Sem Cache 2.0 related tweaks
- Apply filters to permalinks
- Fix blog link on search/404 pages
- Fix thumbnail support (broken by WP API change before releasing)

= 0.9 =

- Switch to 3 inline boxes instead of 4 in wide layouts
- Skin revamp (30 new skins)
- WP 2.9 compat
- WP 2.9 post thumbnails
- Improved local url identification

= 0.8 =

- Enhanced navigation for archives
- Enhanced comments: wider text, larger gravatar
- Enhanced comment form: narrower field captions, place WP-Review Site stuff and Subscribe to Comments above the form rather than below
- Miscellaneous optimizations and fixes