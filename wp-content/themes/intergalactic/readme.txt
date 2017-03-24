=== Intergalactic ===
Contributors: automattic
Donate link:
Tags: photoblogging, dark, black, white, one-column, right-sidebar, custom-background, custom-colors, custom-header, custom-menu, featured-image-header, featured-images, flexible-header, post-formats, rtl-language-support, translation-ready, fixed-layout, responsive-layout
Tested up to: 4.2
Stable tag: 3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Intergalactic is based on Underscores http://underscores.me/, (C) 2012-2015 Automattic, Inc.

== Description ==

Intergalactic is a stunning specimen for your personal blog. Bold featured images act as the backdrop to your text, giving you a high-contrast, readable theme that's perfect for making your content pop. The one-column layout provides a distraction-free environment for reading, while the slide-out menu keeps your navigation and secondary content readily accessible.

== Bundled Licenses ==

* Photographs depicted in screenshot.png are from pexels.com and licensed CC0
* Google Font "Lato" is licensed under the SIL Open Font License, Version 1.1; Source: https://www.google.com/fonts/specimen/Lato
* Genericons icon font, Copyright 2013 Automattic; Genericons are licensed under the terms of the GNU GPL, Version 2 (or later); Source: http://www.genericons.com

== Installation ==

1. In your admin panel, go to Appearance -> Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

== Where is my Custom Menu? ==

Intergalactic includes a navigation menu in the slide-out menu, accessed by clicking the hamburger menu (three horizontal lines) in the upper right corner of the header. Custom Menus can be configured by going to Appearance -> Menus in your Dashboard.

== Where can I add widgets? ==

Intergalactic includes a widget area in the slide-out menu, accessed by clicking the hamburger menu (three horizontal lines) in the upper right corner of the header. Widgets can be configured by going to Appearance -> Widgets in your Dashboard.

== Does Intergalactic use Featured Images? ==

Featured Images display best at least 1440px by 900px as a background to the post excerpt and title.

== How can I add a site logo? == 

Brand your site and make it yours by including your business' logo with Jetpack (http://jetpack.me); navigate to Customize → Site Title and upload a logo image in the space provided. The logo will appear next to your site title in the header; it can be any size, but will display at a maximum height of 100 px.

== How can I add links to my social networks? == 

You can add links to a multitude of social services in the slide-out menu using the following steps:

1. Create a new Custom Menu, and assign it to the Social Links menu location.
2. Add links to each of your social services using the Links panel.
3. Icons for your social links will appear in the slide-out menu.

= Quick Specs (all measurements in pixels) =

* The main column width is 660.
* The sidebar width is 600.
* Featured Images are 1440px by 900px

== Changelog ==

= 3 February 2016 =
* Adding word-wrap to post and page titles, to prevent layout braking when long words are used.

= 8 January 2016 =
* Tweak to media query to catch 1px area where images wouldn't appear correctly on single posts on portrait orientation tablets.

= 4 December 2015 =
* Adding a max-width to input elements to prevent them from overflowing the main container; Fixes #3566;

= 3 December 2015 =
* Adding word-wrap and hyphenation to post, page and comment content; Fixes #3565;

= 2 December 2015 =
* Wrapping 'Read more' link after posts with post format set in a conditional statement, to prevent duplicate 'More' buttons; Fixes #2731;

= 6 November 2015 =
* Add support for missing Genericons and update to 3.4.1.

= 20 August 2015 =
* Add text domain and/or remove domain path. (E-I)

= 31 July 2015 =
* Remove .`screen-reader-text:hover` and `.screen-reader-text:active` style rules.

= 16 July 2015 =
* Always use https when loading Google Fonts. See #3221;

= 8 July 2015 =
* Remove site-logo tag from stylesheet.

= 10 June 2015 =
* Fixed Header text color not being correctly applied; Bumped up version number; Fixes #3183;

= 12 May 2015 =
* Ugh, generated download before I bumped the version number. Re-upping it so it's synched with the download.
* Update version number for resubmission to .org

= 6 May 2015 =
* Fully remove example.html from Genericons folders.
* Remove index.html file from Genericions.

= 24 April 2015 =
* Removed footer top padding. No longer needed due to update in previous revision.
* Updated full-page featured image functionality.

= 9 April 2015 =
* Add really simple editor style to make sure images fit in the editor screen.

= 8 April 2015 =
* Be less specific with the image to add the extra class when >= 1000px

= 7 April 2015 =
* Attempt to fix image alignement/size issue by making the JS function less greedy and simplyfing the CSS
* Revert r25174 -- Wasn't fixing image alignement/size issue
* Attempt to fix image alignement/size issue by making the JS function less greedy and simplyfing the CSS

= 27 March 2015 =
* Make buttons in slideout menu visible.

= 6 March 2015 =
* Apply fix to toggle menu script, loading it when the dom is ready rather than when the window loads; this prevents a bug where users on mobile devices like iPhone can't open the menu when other media is present on the page.

= 5 March 2015 =
* I don't think we need to set a min-height on the site-branding area unless there is no site title displayed; this causes weird spacing issues in the header when a site title is present.
* Tweak for height of site branding area when a custom header is assigned;
* Improvements to custom headers on mobile devices.

= 12 February 2015 =
* Updated alignment styles for full-sized images smaller than 1000px in width.

= 7 January 2015 =
* Add margin to bottom of large screen author avatar

= 17 December 2014 =
* update credits.

= 15 December 2014 =
* Add function_exists checks around two scripts; update readme and version number in preparation for resubmission to .org
* Remove unused content-search.php file; content-home.php should be used for search.php instead.

= 2 December 2014 =
* Improve accessibility of links

= 27 November 2014 =
* Multiple changes:

= 11 November 2014 =
* Fix text domain without quotes issue in template-tags.php

= 7 November 2014 =
* Custom header text fix

= 27 October 2014 =
* Ensure background images stay dark no matter the screen size;
* Fix for custom header and site title site header height on single posts
* Ensure site controls are visible even if no site title is present.
* Ensure if user does not have a site title displayed or a header image that a large blank area is not displayed.
* Set a minimum height so custom headers will display even if site title and tagline are removed.

= 2 October 2014 =
* Fix for taxonomy description on mobile
* Ensure taxonomy description shows up properly

= 30 September 2014 =
* Add missing languages file
* Update photo credit for readme.txt
* Update readme in preparation for submission to .org

= 26 September 2014 =
* Make certain areas (entry meta, blockquotes, site description) higher contrast for better accessibility; add accessibility-ready tag
* Add hidden post title to read more links for accessibility
* Add comment to note infinite scroll load
* Ensure galleries and images are outdented properly when Infinite Scrolling; add support for Gallery post format
* Ensure small videos stay centered in the content area
* Ensure large images that are large enough will display outdented
* Center background images
* Make caption/image outdent styles more specific so they work properly for all big images regardless of alignment. Partial fix.
* Partial fix for image caption outdenting; reduce h1 size
* Clean up style.css, clean up gallery styles
* Ensure nested blockquotes do not increase font size on large screens
* Ensure commas between tags and categories don't stand out so much; make reply-title h3 match font families.
* Fix for Rate This text size; allow site info to display on one line on larger screens
* Add spacing between page numbers on paginated posts

= 25 September 2014 =
* Transform arrows on post navigation to rotate for RTL
* Add left arrow for more link to RTL styles
* Initial pass at RTL styles, clean up spacing in style.css
* Better method for outdenting large, centered images
* Ensure quotes are displayed with white text on home, archives, and search for better readability
* Increase font size for links, asides, and blockquotes at a tablet screen size
* Adjustments to font sizes for small and large screens
* Fix margins on site title in custom header admin
* Ensure custom header admin area matches front end
* Minor fix for screenshot
* Reduce font size of image captions; apply outdented centering to large images as well as full
* Tweak display of toggle button for small screens

= 24 September 2014 =
* Fix positioning of background featured image on single posts
* More fixes for toggle button during different states (mobile/not mobile, single/archives views) and tweak to Custom Header image such that it does not wrap the page title
* More fixes, add a white border around menu toggle button on single posts view when toggle menu is open
* Begin integrating custom headers and fix single post toggle icon
* Add screenshot
* Minor adjustments to styles
* First crack at animated toggle icon
* Capitalize page titles
* Display full content for formatted posts on blog index and archives; add link to post format archives on single posts; remove line numbers and clean up CSS

= 12 September 2014 =
* Align edit link to the right on single posts
* Adjustments to alignment and font size for small screens
* Improvements for tablet-sized mobile devices
* Add hover styles; remove blur on background images; adjust border colors

= 11 September 2014 =
* Begin readme.txt file
* Allow video embeds to overflow the content container on large screens

= 10 September 2014 =
* Wrap tiled gallerys in a DIV, allowing them to resize to overflow the content column on large screens. Remove minified JS directory. Styles to work with tiled galleries.
* Pull galleries into the margins; update custom header crop size to reflect same size as featured image
* Standardize pulled images and blockquotes so they align to the grid
* Move social links menu to menus section; update font size to align to pixel grid for Genericons
* Fix for menu toggle on small screens
* Increase bottom margin on widgets
* More improvements to widget areas to better match mock-ups.
* Style list items in widgets
* Add theme description
* Darken single post featured images slightly so white titles can be read on light images
* Adjustments to styles for 404 and search results not found pages
* Add icon to search form label
* Add bottom border to page header; adjust site title color for posts with featured images so it will show up on a light image
* Minor style changes for large screens and mobile devices
* Begin styling custom header admin
* Minor style adjustments to menu toggle icon and pagetitles
* Update content width to 1000px to accomodate large centered images
* Adjust padding on single posts
* Minor style tweaks
* Improvements for mobile menu and site title/description on mobile
* Add tags to style.css, consolidate jetpack setup functions, add support for jetpack responsive videos, minor style changes to social links to add bottom margin
* Style updates for WP.com; add social links menu to toggle area
* Initial commit
