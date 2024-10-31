=== Secret Content ===
Contributors: maxemil
Donate link: http://oneconsult.dk/wordpress
Tags: hide content, hidden posts, members only, restricted content
Requires at least: 2.8
Tested up to: 3.3
Stable tag: trunk

Easily mark any post or a page as "for logged in members only", hiding it from public view! (not for custom post types).


== Description ==

Simply tick the checkbox "Show this to logged in visitors only"  This works on a per post / per page basis.  Not yet enabeled for custom post types.

You do not need to insert custom functions or shortcodes. You do not need to change theme template files.
This plugin works by filtering WordPress core functions.

If you "hide" a parent page, the children will be hidden in the menu as well.

Technically the filters are modifying the query or the result of databse calls on standard WP features:  wp_get_nav, wp_list_pages, $posts, previous_post, next_post.
When ticking the checkbox, the plugin saves a key/value pair in the postmeta database table.
When uninstalling the plugin, the entrys to the postmeta table are deleted.

Suggestions, Questions and feedback are welcome.


== Installation ==

1. Download the plugin.
2. Upload it to the plugins folder of your blog.
3. Goto the Plugins section of the WordPress admin and activate the plugin.


== Frequently Asked Questions ==

= How do I hide a post or a page? =
Simply tick the checkbox shown at the post/page edit screen.

= How do I make the post or page public viewable again? =
UN-tick the checkbox shown at the post/page edit screen.

= Do I need to modify my theme, insert functions or write code? =
No, You simply activate the plugin, and let the plugin work for you.

= My visitors dont see the post or a page, and they ARE logged in! =
Make sure you have PUBLISHED the post/page and that it is not "draft" or "private".

= I dont see the metabox or the option to hide a post/page? =
Check your screen settings. Make sure the "Show this to logged in visitors only?" is ticked. (screen settings is found in the tab in the top right, below your "hello username").

= I still dont see the metabox... =
Did someone deactivate the plugin? Try activating it again!


== Screenshots ==

1. When you edit a post or page, simply use the checkbox "Show this to logged in visitors only"


== Changelog ==

0.9
* First release in English only.

1.0
* In english, with Danish translation


== Upgrade Notice ==

= 1.0 =
From initial 0.9 to 1.0 - Only added translation to da_DK - this is a worry free upgrade!