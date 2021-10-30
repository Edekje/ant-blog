<?php
require 'blogfunctions.php';
require 'website_configuration.php'
$RecentPosts = get_posts(5);
echo "<?xml version='1.0' encoding='UTF-8'?>";
?>
<rss version='2.0'>
<channel> 

<title><?php echo $wc_rss_title ?></title>
<link><?php echo $wc_website_url ?>blog/</link>
<description><?php echo $wc_rss_description ?></description>

<?php foreach ($RecentPosts as $Post) { ?>
<item>
  <title><?php echo $Post['Title'] ?></title>
  <link><?php echo $wc_website_url ?>blog/post.php?n=<?php echo $Post['PostTag'] ?></link>
  <description><?php echo $Post['SubTitle'] ?></description>
</item>
<?php } ?>
</channel>
</rss>
