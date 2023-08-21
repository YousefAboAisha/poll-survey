=== SEO Redirection Premium ===
Contributors: wp-buy
Tags: redirection, seo redirection, redirect, redirected, Quick Redirect, post redirect, 301 redirect, 404 redirection, redirection plugin
Requires at least: 3.0.1
Tested up to: 6.2
Stable tag: 4.9
By this plugin you can build 301,302 or 307 redirects easily for your site, you can also monitor 404 Error Pages and redirect them.

== Description ==
By this plugin you can build redirects easily for your site, you can set up all types of redirection including 301,302 or 307 redirect, you can also monitor 404 error pages and redirect them in one mouse click, we concentrated on simplicity in this plugin to meet all user levels.

== Changelog ==

= 5.1 =
* Bug Fixing: Updated table indexes to fix Errors.
  - Modified indexes in the following tables:
    - WP_SEO_Redirection Table:
      - Removed index 'redirect_from'
      - Added composite index 'redirect_from' on columns ('redirect_from'(200), 'cat', 'blog')
    - WP_SEO_404_links Table:
      - Removed index 'link'
      - Added composite index 'link' on columns ('link'(200), 'blog')
  - For best experince : upadate the plugin 

  
= 4.9 =
*  New feature - Support emojis in redirects


= 4.8 =
*  Bug fixing (PHP Warning: Attempt to read property "ID" on null)


= 4.7 =
*  Bug fixing in the plugin settings page


= 4.6 =
*  Adding execlude a redirect feature


= 4.5 =
*  Bug fixing - PHP Warning: Attempt to read property "ID"


= 4.4 =
* Bug fixing in rules page


= 4.3 =
* adding new option in the general options page (never redirect serach results)



= 4.2 =
* XLSX/CSV Export bug fixing



= 4.1 =
* XLSX Import/Export feature


= 3.9 =
* Bug fixing in options page

= 3.8 =
* Bug fixing in redirection log

= 3.7 =
* bug fixing in redirect cache

= 3.6 =
* Bug fixed


= 3.5 =
* Bug fixed

= 3.4 =
* Bug fixed

= 3.3 =
* Bug fixed

= 3.1 =
* support 410 redirect (Permanently Deleted)

= 2.31 =
* bug fixing in redirect cache


= 2.30 =
* bug fixing in add redirects
* bug fixing in updater

= 2.29 =
* add redirect to new page
* fix conflicts with Advanced Custom Fields Plugin
* RTL support


= 2.28 =
* adding $wpdb->prepare to all sql statments to prevent injection

= 2.27 =
* bug fixing (buddy_press_check_locking issue)

= 2.26 =
* bug fixing in multisite INDEXING (the unique constraint)
* Adding the ability to hide/show/mask IP's in the history and 404 pages

= 2.25 =
* fixing buddypress issue (redirects was not working for locked pages)
* Fixing WPML issues (the plugin was not fully compatible with the last WPML version)
* fixing regex (plugin was truncate the "+ sign" after saving the regex)
* Some other enhancements
= 2.24 =
* Bug fixing in uninstalling the plugin
= 2.23 =
* Bug fixing in cf_security (MCRYPT is deprecated since PHP 7.1 and removed since PHP 7.2)
= 2.22 =
* adding the ability to hide/mask IP address (for GDPR compliance)
* User can click display the IP GEO location
* User can control the total number of rows per page
= 2.21 =
* Bug fixing in export redirects with PHP 7.1
= 2.20 =
* adding the ability to hide messages
* Bug fixing - function mcrypt_encrypt() is deprecated since PHP 7.1
= 2.19 =
* Bug fixing in export redirects as csv file
= 2.18 =
* Bug fixing in 404 general rules (to support multisite)
= 2.17 =
* Bug fixing in Top Traffic 404 Errors
= 2.16 =
* Bug fixing in redirection log
= 2.15 =
* New feature - the regex tester
= 2.14 =
* Bug Fixing in the updater plugin
* fixing port issue in the replace rule
= 2.13 =
* GDPR Compatibility
= 2.12 =
* Added feature, the capability to add RegEx in the target URL
= 2.11 =
* Bug Fixing in the redirect history
= 2.10 =
* Bug Fixing in Redirection rules (adding more than one rule)
= 2.9 =
* Added feature, Hits count and last access date.
* Bug Fixing.
= 2.8 =
* Fixed issue appeared in PHP 7.1 versions .
= 2.7 =
* Fixed issue appeared in PHP 7 versions .
= 2.6 =
* Added feature, the capability to disable plugin for admin users
= 2.5 =
* The function urlencode() used instead of base64_encode() which may has security issues.
= 2.4 =
* Some improvements and database optimization.
= 2.3 =
* Import issue fixed.
= 2.2 =
* Fixed issue appeared in PHP < 5.3.0 versions .
= 2.1 =
* Added feature, the capability to redirect HTTP to HTTPS posts.
= 2 =
* Redirect loops protection.
= 1.9 =
* Some Fixes.
= 1.8 =
* Some Fixes to work with woocommerce permalinks.
= 1.7 =
* Some hot fixes.
= 1.6 =
* Some hot fixes.