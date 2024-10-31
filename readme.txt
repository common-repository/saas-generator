=== Saas Generator ===
Contributors: eggemplo
Donate link: https://saas-generator.com/
Tags: saas, front edit, cpt
Requires at least: 5.7
Tested up to: 5.8.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create your own Saas in WordPress.

== Description ==

No-code solution, you can create your own Saas in Wordpress, editing custom post types from the frontend, listing and executing custom actions on them.

= Features: =
  * Display custom post types lists on frontend.
  * Edit custom post types on frontend.
  * Create custom action for the items.

Integration with Advanced Custom Field (ACF) that you can use to create attributes to your objects.
= ACF fields available: =
  * Text field.
  * Select field.
  * Post object field.
  * True-False field. 

= Basic usage =
Create one page for each custom post type that you want to be available in your saas. One page per section.

![One page, one section](/assets/03-pages.png)

Simply create the page and use the <strong>[saas] shortcode</strong> with the "cpt" attribute. For example, if your custom post type is "task", your shortcode is: [saas cpt="task" /]


Official site: [saas-generator](https://saas-generator.com)
Documentation page: [Documentation](https://saas-generator.com/documentation/)

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip the archive and put the `saas` folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins menu.
3. Create as many pages as you need using the new shortcodes availables.

== Screenshots ==

1. Listing: Method - Method dashboard section.
1. Editing: Roles - Roles dashboard section.

== Changelog ==

= 1.0.1 =
* Added Bootstrap css/js

= 1.0.0 =
* Initial version.
