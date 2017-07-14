=== DispletReader Legacy ===
Requires at least: 3.2
Author: Displet
Author URI: http://displet.com/
Contributors: displetdev
Plugin URI: http://displet.com/wordpress-plugins/displetreader-legacy/
Tested up to: 3.5.1
Stable tag: 1.5.2.1
Tags: real estate, rets, idx, listings, realty, mls, free, agent, realtor

Uses the Displet 1.0 API (newer version available in Displet RETS/IDX Plugin) to insert real estate listings, statistics, maps, and quick searches into Wordpress pages & widget ready sidebars.

== Description ==
<strong><a href="http://displet.com/idxrets-search/">Click here to read about Displet's RETS/IDX Solutions</a></strong><br>

Easily insert real estate listings, statistics, maps, and quick searches into Wordpress pages &amp; widget ready sidebars. This plugin leverages Displet's powerful RETS/IDX system &amp; lead capture tools. The plugin offers both free &amp; paid versions.<br><br>

<strong>Free Version vs. Paid Version</strong><br>
Our free version is very powerful and offers most features available in other plugins' paid versions. The data source for our free version is not from any MLS or RETS feed, so is not as complete or reliable as a RETS feed. However, it does offer thousands of listings &amp; many features. For the most complete &amp; reliable data &amp; feature set, however, you would upgrade to our paid version.<br><br>

<strong>Easily Insert Real Estate Listings</strong><br>
Using multiple query options, including price, beds, baths, square footage, stories, pools, foreclosure, short sale, keyword, gated community, year built, new construction &amp; more. Our keyword query is very powerful &amp; searches across multiple fields. Our plugin allows your visitors to search using a quick search or map search, and allows you to create high conversion niche landing pages. You can choose whether or not to include a map and/or statistics in your search results &amp; landing pages, and whether to expand or hide the map by default.<br><br>

<strong>Powerful Lead Capture Tools</strong><br>
This plugin includes very powerful lead capture tools, including forced vs soft registration, teaser views, phone number nag, light window registration/login, and social registration/login. Our Pro version allows you to view your visitors searching history, as well as overview statistics. Our proprietary Property Suggestion Tool ensures your visitors will return by suggesting new properties to them via email.
== Installation ==

1. Upload and activate the plugin, using either the Plugin Administration or ftp.
1. Configure the plugin in Settings -> DispletReader. A Displet server url is required.

== Frequently Asked Questions ==

= Are there FAQs? =

Please watch the video tutorials available here: http://displet.com/wordpress-plugins/displet-rets-idx-plugin/

== Screenshots ==

1. Choose from a wide range of criteria to easily insert real estate listings on any page or widget ready sidebar.
2. Easily insert basic statistics, a map of the listings, and a tile or list view of real estate listings into any page or sidebar.
3. Leverage Displet's powerful lead capture tools, including regist++ration light window, facebook login, saved searches, favorites, and property suggestions.

== Changelog ==

= 1.0 =
* Initial release.

= 1.0.1 =
* Updated php-displet, area_mls_defined now working.

= 1.0.2 =
* Fix for layout bug in Tile template.

= 1.0.3 =
* Graceful handling of unconfigured widgets.
* Clean out caches on uninstall.

= 1.1 =
* Street address support.
* New Listings widget template.
* Sort option in widgets.
* Tile template hard heights, to work around sub-pixel height differences in some themes/browsers (this caused gaps in tile layout).

= 1.1.1 =
* Bugfix for domain mapping multisite.

= 1.2 =
* Improved domain mapping support.
* Bugfix for DispletStats: Result sets with no interior space (0 to 0 "Size Range") don't break things.
* onDisplet functionality now included.

= 1.2.1 =
* Made Displet Pro mode default.

= 1.2.2 =
* Bugfix: Activation hook no longer fires on updates, must do version check with every request.

= 1.2.3 =
* DispletFrame refinement.

= 1.2.4 =
* Bugfix: Updated php-displet, school_district works again.

= 1.2.5 =
* Added map listings option

= 1.2.6 =
* Added compatability for maps with custom Displet templates. Also added option to separately display maps from listings.

= 1.2.7 =
* Added Quick Search shortcode & widget

= 1.2.8 =
* Consolidated DispletStats shortcode into DispletListing shortcode

= 1.2.9 =
* New quick search & stats options, also added cookie to remember listings pagination

= 1.3 =
* Improved settings page

= 1.3.1 =
* Improved horizontal quick search

= 1.3.2 =
* Fixed caption bug

= 1.3.3 =
* Modified backend options to retrieve Woopa field values

= 1.3.5 =
* Fixed map bug when at least 1 listing but no lat-long data

= 1.3.6 =
* Added classes to tile template for increased style control, Displet IDX (dashboard) menu, & cookie to remember tile sort

= 1.3.7 =
* Fixed possible map problem when prices aren't returned numerically

= 1.3.8 =
* Fixed insert/edit Listing popup (MCE) alignment in Windows

= 1.3.9 =
* Fixed overlapping text in tile styles, added classes to tile template, number formatted square feet

= 1.4 =
* Preventing tile images from loading until next page is requested, added variance for map to eliminate outliers

= 1.4.4 =
* New listings styles, option to hide map by default

= 1.4.5 =
* User choice for listing styles

= 1.4.6 =
* Auto price navigation featured added

= 1.4.6.9 =
* WordPress 3.5 compatibile

= 1.4.7.1 =
* Reset quick search form on load, voided href for Gallery/List links

= 1.4.7.2 =
* Added county to search parameters, property types to front-end user sorting

= 1.4.7.3 =
* Improved sidescroller Javascript

= 1.4.7.4 =
* Improved sidescroller Javascript, made listings limit & sort effective for listings widget, sort per DispletListing also

= 1.4.7.5 =
* New features: Property type navigation & Sort by newest

= 1.4.7.6 =
* New features: Property type navgiation & price navigation improvements

= 1.5 =
* New free mode allows listings without a RETS/IDX account

= 1.5.1 =
* New features including Quick Start, Facebook/Google login, Zapier integration, and improved URL structure

== Upgrade Notice ==

== Template Hierarchy ==

Templates can be overriden, in a fashion similar to the Template Hierarchy for WordPress themes. In addition, Javascript and CSS can be overridden.

The plugin looks for templates with a particular name in the root directory of the current Theme.

The plugin also has support for legacy (pre 1.0) templates.

== Template names ==

= Shortcode Templates =
* displet-scripts (js) The javascript
* displet-styles (css) The CSS
* displet-stats (php) Statistics
* displet-map (php) Map
* displet-listings (php) The "vertical" presentation of listings
* displet-tile (php) The "tiled" presentation of listings
* displet-dynamic (php) The combined presentation of listings

= Widget Templates =
* displet-listings-widget (php|js|css)
* displet-sidescroller-widget (php|js|css)
* displet-stats-widget (php|js|css)

== Custom Template Recipe ==

1. Select 'Custom' Default Listings Display.
2. Copy a template from $plugin_root/templates/ to the current theme directory.
3. Edit the template.
4. That's it!

== Legacy Support ==

While Legacy support is turned on (internal setting, default is "on" for 1.0), the plugin will look for a directory called "Views" in theme root and the plugin root, in that order.

CSS and Javascript will be detected in theme root, or the css/ and js/ directories under the plugin root.

== Legacy Templates Recipe ==

1. Copy the Views/ directory from the old plugin to the current theme directory.
1. Copy the contents of the js/ and css/ from the old plugin to the theme root.
1. That's it!

== Complete Template Hierarchy ==
In order of priority:

1. Templates in the theme root.
1. Legacy templates in the theme root.
1. Legacy templates in the plugin root.
1. Default built-in templates.
