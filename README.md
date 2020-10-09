# Jeremy #
**Contributors:** prestobunny
**Requires at least:** 5.2
**Tested up to:** 5.5
**Version:** 2.0.0
**Requires PHP:** 7.0.0

**License URI:** https://www.gnu.org/licenses/gpl-3.0.en.html
**License:** GNU GPL-3.0 or later

**Tags:** business, directory, geodirectory, members, buddypress, custom background, one sidebar

A playful, modern theme designed for business geodirectories and chambers of commerce.

## Description ##
Jeremy is designed to integrate with [Buddypress](https://buddypress.org/) and [Event Organiser](https://wordpress.org/plugins/event-organiser/) to create a robust business directory and event system.

## Installation ##
1. In the admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme.

The member geodirectory feature and Event Organiser's venue maps make use of the Google Maps API. You'll need a Google Maps [JavaScript API key](https://developers.google.com/maps/documentation/javascript/get-api-key) for them. If you plan on using the profile maps as well, you will need to request an [Embed API key](https://developers.google.com/maps/documentation/embed/get-api-key) for the same project.

- [BuddyPress WP-CLI tools](https://github.com/buddypress/wp-cli-buddypress)
- [BuddyPress GDPR data export extension](https://github.com/buddypress/buddypress-data-exporters)

## Changelog ##

### 2.0.0 ###
* Minified stylesheet
* Enhanced print styles!
* Events archive now shows a calendar.
* Content area now only has one sidebar, rather than multiple to separate between page/post widgets. This is to avoid having to duplicate the same widget if you use one for both.
* Consolidated redundant "page" and "single" templates to "singular"
* Added wp_body_open() to header.php
* Removed Event Schema markup generator
* Member directory can now be searched using the "category" url parameter, which will turn up only members in that category. The old implementation using "search_terms" included members that had the category name (e.g. "restaurants") anywhere in their profile (as in "we provide services for restaurants") regardless of whether they belonged in the category.

### 1.0.0 ###
* Initial release

# Wishlist #
* It'd be nice to use the modern version of Fullcalendar.js, as the one that ships with Event Organiser has nested table-based markup that's super hard to style.