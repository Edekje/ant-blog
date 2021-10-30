<?php
/* This file contains website-specific configuration settings
 * to tailor website content and appearance to users needs. */

/* General */
/* The full http:// url to this website, e.g. http://www.google.com/ MUST END WITH A forward-slash '/'!					*/
$wc_website_url = 'https://www.myantblog.com/';

/* headertemplate.php */
// This text is always appended to the title in the browser title bar:
$wc_website_titlebar = ' - AntBlog.com';
// Website banner title:
$wc_headertemplate_bannertitle = 'Ant Blog';
// Website banner sub-title:
$wc_headertemplate_bannersubtitle = 'A lightweight website and blog CMS';
// Website banner image location:
$wc_headertemplate_bannerimage = $wc_website_url.'images/ant.jpg';
// Main stylesheet location:
$wc_website_mainstylesheet = $wc_website_url.'styles/mainstyle.css';
// Website stylesheet to include fonts:
$wc_headertemplate_fontsstylesheet = 'https://fonts.googleapis.com/css2?family=Merriweather&display=swap';
// Pages in the navigation bar:
$wc_headertemplate_navigationpages = array('Home', 'Blog', 'Shared Work', 'Portfolio', 'Contact');
// Links corresponding to the pages in the navigation bar:
$wc_headertemplate_navigationlinks = array('index.php', 'blog/', 'work.php', 'portfolio.php', 'contact.php');

/* rss.php */
$wc_rss_title = 'Ant Blog';
$wc_rss_description = 'Blog detailing the life of an ant';

/* footertemplate.php */
$wc_footertemplate_text = 'Copyright Ethan van Woerkom (2021)';

