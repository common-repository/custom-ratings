=== Plugin Name ===
Contributors: stevepuddick
Tags: rating, stars, post, user rating, woocommerce
Requires at least: 3.5
Tested up to: 6.1.1
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A fun and creative way to let your site visitors rate your posts, pages, and more! 

== Description ==

Custom Ratings is a fun and creative tool that allows your visitors to interact and rate your site content.

* Choose from a selection of 'built in' rating images or upload your own single image. Custom ratings automatically converts the image to grayscale for disabled ratings, and splits images in half for half values.
* All text is fully customizable and translateable.
* WPML compatible with a cumulative tallying system between languages.
* Use the built in CSS or your own.
* Fully compatible with caching plugins such as W3C Total Cache and Super Cache.
* Full control over caching time for AJAX based voting data. 
* Option for manual placement of Custom Ratings components in theme templates.   
* Full support for custom post types.
* Compatible with WooCommerce products.
* Control over which templates Custom Ratings appears on.

Add some personality to your website and install Custom Ratings today!

Thanks to the following open source projects which Custom Ratings has utilized:

* [Ractive](http://www.ractivejs.org/ "Ractive")
* [Spectrum](https://bgrins.github.io/spectrum/ "Spectrum")


== Installation ==

1. From the plugin managment page in the WordPress admin, search for "Custom Ratings" 
2. Select "Custom Ratings" from the list of results and click on "install"
3. "Custom Ratings" has now been installed. In the Wordpress admin, go to "Settings" > "Custom Ratings"
4. Choose your star type from the included options, or upload your own image (will be uploaded to media library)
5. Select which post types "Custom Ratings" should be applied to. Posts are selected by default
6. Select where the rating tally and vote display positions should be in relation to the content 
7. Select the background color, if desired
8. Edit the various text snippets, if desired
9. Configure other Custom Ratings settings to your preference
10. Take a look at a "single" or "archive" page of a selected post type (or any other page which call the_excerpt or the_content) and you will see the custom ratings components appended


== Frequently Asked Questions ==

= What is a "star" =

In Custom Ratings, a "star" is a general term used to describe the rating object. A "star" can be a cupcake, heart, star, smiley face, shoe, etc.

= What is a rating tally? =

The rating tally is a non-interactive display of the current ratings of a post. This is displayed visually as the number of stars out of a total of 5 stars. This hooks into the "the_excerpt" filter. Where ever that filter is used, the Custom Ratings rating tally will be displayed (unless disabled in the settings). 

= What is a vote display? =

The vote display is an interactive component which allows users to rate a post. This rating can be 1, 2, 3, 4, or 5 stars out of 5. The vote display hooks into "the_content" filter.  Where ever that filter is used, the Custom Ratings vote display will be displated (unless disabled in the settings). 


= Will this work with WPML? How does the cumulative tallying system work? =

Custom Ratings has been designed with WPML compatiblity in mind. All of the votes of a specific post are shared amongst the different language versions. One caveat is that a post in the default language must be created in order for Custom Ratings to function. The default language post holds all of the rating information. 

= Does this work with custom post types? =

Yes. You can choose which (if any) custom post types Custom Ratings gets applied to.

= How does the caching of AJAX vote data work? =

Each time a page loads with Custom Ratings components, an AJAX request is sent to the web server to get the vote information for the posts on that page. The "Expires" and "Cache-Control" values can be specified for this request if desired. CDNs and other caching tools can read these HTTP headers and handle the caching from there. 

= How can I use Custom Ratings in my custom development? =

Custom Ratings has been designed to offer lots of flexibility for custom theme development: 

* `Custom_Ratings::get_rating_object_image_src()` can be used in your theme to get the 'star' (rating object) image
* `echo Custom_Ratings_Public::display()` can be used to manually output the rating tally
* `echo Custom_Ratings_Public::vote()` can be used to manually output the rating vote interface

If you are [creating your own queries](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters "creating your own queries"), you can order the results in different ways:

* The post meta key `_wpcr_rating_stars_avg` can be used to order posts by rating average
* The post meta key `_wpcr_rating_stars_count` can be used to order posts by total rating count

== Screenshots ==

1. Front end display of rating vote interface (voted) 
2. Front end display of rating vote interface (un voted)
3. Front end display of rating tally 
4. Admin settings tab 1
5. Admin settings tab 2
6. Admin settings tab 3
7. Admin post listing
8. Admin post edit page

== Changelog ==

= 1.5.0 =
* Fixed issue that prevents featured image selection from working on posts, pages, and other post types
* Added rating tally and rating vote image width fields
* Fixed issue with proper caching time output
* Fixed issue with proper alignment of rating display
* Added rating column to admin post listing
* Added controls to hide rating tally on front end
* Added conditional checks for 'in the loop'
* Added conditional checks for 'front page', 'home', 'archive', and 'search' templates
* Code clean up

= 1.0.1 =
* Option to hide Custom Ratings components on front page
* Fix to 'money' image file path
* Option to disable Custom Ratings CSS
* Full WPML compatibility
* Removed 'post' as the default post type Custom Ratings is applied to
* Set default excerpt attachment to none

= 1.0.0 =
* Initial version

