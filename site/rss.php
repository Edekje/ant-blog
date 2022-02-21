<?php
require 'blogfunctions.php';
require 'website_configuration.php';
$RecentPosts = get_posts(5);
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>";
?>
<rss version='2.0' xmlns:atom="http://www.w3.org/2005/Atom">
<channel> 

<title><?php echo $wc_rss_title ?></title>
<link><?php echo $wc_website_url ?>blog.php</link>
<description><?php echo $wc_rss_description ?></description>

<atom:link href="<?php echo $wc_website_url ?>rss.php" rel="self" type="application/rss+xml" />

<?php foreach ($RecentPosts as $Post) { ?>
<item>
  <title><?php echo $Post['Title'] ?></title>
  <link><?php echo $wc_website_url ?>blog/post.php?n=<?php echo $Post['PostTag'] ?></link>
  <description><?php echo $Post['SubTitle'] ?></description>
  <guid>evw-blog-tag:<?php echo $Post['PostTag'] ?></guid>
</item>
<?php } ?>
</channel>
</rss>
