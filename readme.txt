=== APG Google Video Sitemap Feed ===
Contributors: artprojectgroup 
Donate link: https://artprojectgroup.es/tienda/donacion
Tags: video sitemap, sitemap, youtube, vimeo, dailymotion
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Dynamically generates sitemap-video.xml, a video sitemap for Google. It needs no configuration at all.

== Description ==
**APG Google Video Sitemap Feed** dynamically generates a video sitemap for Google by creating a virtual `sitemap-video.xml` file.

= Features =
* It needs virtually no configuration, so it works on its own from the moment you activate it.
* Automatically adds every YouTube, Vimeo and Dailymotion video.
* Supports every custom post type.
* Also detects videos hosted on your own website: the video block, the `[video]` shortcode and the media library.
* Finds videos even when they live in custom fields belonging to your theme or to a page builder.
* Publishes the duration, the publication date and the uploader of each video.
* Groups every video of the same page into a single sitemap entry, the way Google's specification expects.
* Automatically caches the external data of each video to speed up building `sitemap-video.xml`.
* Emails the site administrator when a video has been deleted or made private, so you can edit the post and remove the URL that is no longer valid.
* Fully compatible with WordPress Multisite installations.
* Automatically generates several sitemaps, with a maximum of 50,000 pages in each one.
* Refreshes the sitemap as soon as a post is published, edited or deleted.
* Announces itself in `robots.txt` and in the WordPress sitemap index, so search engines can find it.
* Warns you if Yoast, Rank Math, All in One SEO or SEOPress is already publishing a video sitemap, so you do not end up with two.

= Translations =
* English ([**Art Project Group**](https://artprojectgroup.es/)).
* Spanish ([**Art Project Group**](https://artprojectgroup.es/)).

= Support =
**Art Project Group** offers paid [**technical support**](https://artprojectgroup.es/tienda/ticket-de-soporte) to help you install or configure **APG Google Video Sitemap Feed**.

= Origin =
**APG Google Video Sitemap Feed** was built on top of [*Google News Sitemap Feed With Multisite Support*](https://wordpress.org/plugins/google-news-sitemap-feed-with-multisite-support/) by [Tim Brandon](https://profiles.wordpress.org/timbrd/) and [*Google XML Sitemap for Videos*](https://wordpress.org/plugins/xml-sitemaps-for-videos/) by [Amit Agarwal](https://profiles.wordpress.org/labnol/). Both are excellent plugins that did not offer everything we needed, but their work was essential to build this one.

Since version 1.0 it has also been improved thanks to the code contributed by [Ludo Bonnet](https://github.com/ludobonnet) and his idea of adding Vimeo and Dailymotion support.

= Companion plugins =
We recommend using **APG Google Video Sitemap Feed** together with [**APG Google Image Sitemap Feed**](https://wordpress.org/plugins/google-image-sitemap-feed-with-multisite-support/), which generates `sitemap-image.xml`, and [**Google Mobile Sitemap Feed With Multisite Support**](https://wordpress.org/plugins/google-mobile-sitemap-feed-with-multisite-support/), which generates `sitemap-mobile.xml`.

= Important =
Problems have been reported when using it alongside the latest version of **Google XML Sitemaps** with WordPress Multisite support. They are described in [How to fix the incompatibility between Google XML Sitemaps and our plugins](https://artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins), where you will find everything you need to solve it.

= More information =
You can read more about [**APG Google Video Sitemap Feed**](https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed) on our official website. The Spanish version of this documentation ships with the plugin in `readme.md`.

= Feedback =
Do not forget to leave us your comments on:

* [APG Google Video Sitemap Feed](https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed) at Art Project Group.
* [Art Project Group](https://www.facebook.com/artprojectgroup) on Facebook.
* [@artprojectgroup](https://twitter.com/artprojectgroup) on Twitter.

= More plugins =
Remember that you can find more [WordPress plugins](https://artprojectgroup.es/plugins-para-wordpress) at [Art Project Group](https://artprojectgroup.es) and on our [WordPress profile](https://profiles.wordpress.org/artprojectgroup/).

= GitHub =
You can follow the development of this plugin on [GitHub](https://github.com/artprojectgroup/google-video-sitemap-feed-with-multisite-support).

== Installation ==
1. You can:
 * Upload the `google-video-sitemap-feed-with-multisite-support` folder to the `/wp-content/plugins/` directory over FTP.
 * Upload the whole ZIP file from *Plugins -> Add New -> Upload* in your WordPress admin panel.
 * Search for **APG Google Video Sitemap Feed** in *Plugins -> Add New* and click *Install Now*.
2. Activate the plugin from the *Plugins* menu in your WordPress admin panel.
3. That is all. Enjoy it, and if you find it useful please consider making a [*donation*](https://artprojectgroup.es/tienda/donacion).

== Frequently Asked Questions ==
= Does it need any configuration? =
No, the plugin works entirely on its own.

= Where is my video sitemap? =
At `sitemap-video.xml`, in the root of your website. If your site has more than 50,000 pages with videos, that address becomes an index pointing to `sitemap-video-1.xml`, `sitemap-video-2.xml` and so on.

= Is it compatible with WordPress Multisite installations? =
Yes, it is fully compatible.

= Which videos does it find? =
Videos from YouTube, Vimeo and Dailymotion embedded anywhere in the post content, the excerpt or a custom field, plus videos hosted on your own website through the video block, the `[video]` shortcode or the media library.

= My videos are in the sitemap but Google does not index them. Why? =
Since 2023 Google only indexes a video when it is the main content of the page it lives on. A long article with a video embedded halfway through will show up in Search Console as "Video is not the main content of the page". That is a Google policy, not a problem with the sitemap.

= Are there any known incompatibilities? =
Yes, problems have been reported when using it together with **Google XML Sitemaps**. They are caused by the wrong order of the WordPress rewrite rules, because **Google XML Sitemaps** handles every possible kind of sitemap. You will find the full explanation and the solution in [How to fix the incompatibility between Google XML Sitemaps and our plugins](https://artprojectgroup.es/como-arreglar-la-incompatibilidad-de-google-xml-sitemaps-con-nuestros-plugins).

= The plugin tells me another plugin already publishes a video sitemap. What should I do? =
Whatever you prefer. Having two video sitemaps is not penalized by search engines, but keeping only one is easier to maintain. The notice lets you deactivate this plugin in one click, or hide the message and keep both.

= Technical support =
If you need help installing or configuring **APG Google Video Sitemap Feed**, **Art Project Group** offers its [**technical support**](https://artprojectgroup.es/tienda/ticket-de-soporte) service.

*Art Project Group does not provide free technical support of any kind.*

== Screenshots ==
1. Screenshot of **APG Google Video Sitemap Feed**.
2. Screenshot of the `sitemap-video.xml` file.

== Changelog ==
= 3.0.1 =
* Fixed a loop that re-queued the same videos every minute and fired thousands of daily requests to the video APIs.
* Video checks now run as a daily task and on content changes, instead of on every admin page load.
* A video that fails is retried three times and then abandoned, leaving a note in the Action Scheduler log.
= 3.0.0 =
* Every video on the same page is now grouped into a single sitemap entry.
* Added support for videos hosted on your own website.
* Added video detection inside the custom fields of themes and page builders.
* Added the duration, the publication date and the uploader of each video, and the modification date of each page.
* The sitemap is now announced in robots.txt and in the WordPress sitemap index.
* New notice when Yoast, Rank Math, All in One SEO or SEOPress already publishes a video sitemap, with one click deactivation.
* YouTube data is now requested from its own oEmbed endpoint, with no middleman.
* Fixed a query bug that let videos from unpublished posts into the sitemap.
* Fixed saving the settings, which corrupted the identifiers of the videos already processed.
* Restored the email notice for deleted or private videos, which was never actually sent.
* Fixed the XML to match Google's current specification: namespace, a maximum of 32 tags and a 2048 character description.
* Fixed the numbering of the partial sitemaps, which could serve the wrong one.
* Removed the automatic ping to Google and Bing, as both retired their ping services.
* Hardened security: prepared statements, escaping on every output and capability checks.
* Improved performance: fewer permalink rebuilds and fewer queries per page.
* Updated translations.
* The cache and the permalinks are now also rebuilt when the plugin is updated over FTP.
* Fixed every Plugin Check error and warning.
* Updated compatibility up to WordPress 7.1.
= 2.1 =
* Action Scheduler update.
* Performance improvements.
* Header update.
* Stylesheet update.
* Screenshot update.
= 2.0.1.3 =
* Minor fixes.
= 2.0.1.2 =
* Minor fixes.
= 2.0.1.1 =
* Minor fixes.
= 2.0.1 =
* Minor fixes.
= 2.0 =
* Adapted to the new data structure.
* Generates several sitemaps, one per 50,000 videos.
* Bug fixes.
* Support for websites with a large number of videos.
= 1.8.1.1 =
* Header update.
* Stylesheet update.
* Screenshot update.
= 1.8.1 =
* Removed the extra processing of short descriptions and excerpts.
= 1.8 =
* Added video search inside short descriptions and excerpts.
= 1.7.3.3 =
* SQL query fix.
= 1.7.3.2 =
* SQL query fix.
= 1.7.3.1 =
* Fixed a bug that prevented the settings from being updated.
= 1.7.3 =
* Supports every custom post type.
= 1.7.2.2 =
* Updated support links and minor updates.
= 1.7.2.1 =
* Updated the font package. New Google+ icon.
* Updated translations.
= 1.7.2 =
* Fixed the YouTube player URL.
= 1.7.1 =
* Adjusted the responsive design of the XSL template.
= 1.7 =
* Fixed a bug in the notification and processing of deleted or private videos.
* Responsive redesign of the XSL template.
* Removed unnecessary functions.
* Screenshot update.
= 1.6 =
* Updated the YouTube API.
* Fixed the regular expressions. Both problems reported on [WordPress.org](https://wordpress.org/support/topic/youtube-dailymotion-videos-not-working?replies=3#post-7475198).
* Updated the URLs of the external players.
* Updated translations.
* Improved the XSL file that renders the XML template.
= 1.5 =
* Updated translations.
* New responsive stylesheet.
* Reworked the internal structure of the plugin to follow the WordPress standards.
* Screenshot update.
= 1.4.1 =
* Added cache clearing when new content is published.
= 1.4 =
* Fixed a bug that raised an error message on PHP versions above 5.2.
= 1.3.1 =
* Fixed a bug that wiped the whole configuration when the plugin was deactivated.
* Minor fix that avoided an error code while gathering information about the plugin.
= 1.3 =
* Added a new settings screen to choose whether to receive email notifications.
* Improved the code that sends the email.
* Changed the donation link.
= 1.2 =
* Improved and optimized the code.
* Added a cache for the external data.
* Added a function that clears the cache when the plugin is deleted.
* Changed the donation button and link.
= 1.1.7 =
* Minor bug fixes.
= 1.1.6 =
* Improved and optimized the code.
* Minor bug fixes.
* Used the WordPress Transients API to improve the queries.
* Improved the Vimeo video search.
= 1.1.5 =
* Fixed a variable name bug that left blank data in the email.
= 1.1.4 =
* Fixed several bugs when sending emails.
* Fixed several bugs when storing data in the cache.
= 1.1.3 =
* Simplified duplicated code.
= 1.1.2 =
* Fixed a bug that did not reset the variable controlling the email notifications.
= 1.1.1 =
* Fixed the code that sends the emails.
= 1.1 =
* Cache management for the external data of the videos.
* Sends email notifications when a video no longer exists.
* Code optimization.
* Minor bug fixes.
= 1.0 =
* Added support for the https://youtu.be shortener.
* Added Vimeo support.
* Added Dailymotion support.
= 0.9 =
* Added a new function that cleans the database when the plugin is uninstalled.
= 0.8 =
* Fixed the encoding of the RSS entities.
= 0.7 =
* Minor code fixes.
= 0.6 =
* Improved the code to make sitemap-video.xml validate better.
= 0.5 =
* Updated the stylesheets to match the new WordPress 8.
* Minor code fixes.
= 0.4 =
* Added new buttons and links.
= 0.3 =
* Minor code fixes.
* Minor translation fix.
= 0.2 =
* Minor changes and code fixes.
* Added links.
* Updated the information texts.
= 0.1 =
* Initial release.

== Upgrade Notice ==
= 3.0.1 =
* Fixes a loop that re-queued the same videos every minute. Update as soon as you can.

= 3.0.0 =
* New features (self hosted video, custom fields, duration, uploader and date of each video), several sitemap fixes and hardened security. See the changelog.

== Translations ==
* *English*: by [**Art Project Group**](https://artprojectgroup.es/) (default language).
* *Spanish*: by [**Art Project Group**](https://artprojectgroup.es/).

== Technical support ==
Since **APG Google Video Sitemap Feed** is completely free, **Art Project Group** only offers paid [**technical support**](https://artprojectgroup.es/tienda/ticket-de-soporte). Art Project Group does not provide free technical support of any kind.

== Donation ==
Did you like **APG Google Video Sitemap Feed** and find it useful on your website? We would appreciate a [small donation](https://artprojectgroup.es/tienda/donacion) to help us keep improving this plugin and building more completely free plugins for the whole WordPress community.

== Thanks ==
* To [Tim Brandon](https://profiles.wordpress.org/timbrd/) and [Amit Agarwal](https://profiles.wordpress.org/labnol/) for their great plugins, which inspired **APG Google Video Sitemap Feed**.
* To [Ludo Bonnet](https://github.com/ludobonnet) for his contributions to the code and his idea of adding Vimeo and Dailymotion support.
* To everyone who uses it.
* To everyone who helps to improve it.
* To everyone who donates.
* To everyone who encourages us with their comments.

Thank you all very much!
