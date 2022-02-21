<?php require 'website_configuration.php';
// Enable Debug:
/* ini_set('display_startup_errors', 1);
   ini_set('display_errors', 1);
   error_reporting(-1);*/
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title.$wc_website_titlebar ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo $wc_website_mainstylesheet ?>">
<link href="<?php echo $wc_headertemplate_fontsstylesheet ?>" rel="stylesheet">
<link rel="alternate" type="application/rss+xml" href="<?php echo $wc_website_url ?>rss.php">
</head>
	
<body>

<header>

<div class="BannerLogo">
  <div class="left">
    <h1><em><?php echo $wc_headertemplate_bannertitle ?></em></h1>
    <h2><?php echo $wc_headertemplate_bannersubtitle ?></h2>
  </div>
  
  <!--
  <div class="right">
    <img src="<?php echo $wc_headertemplate_bannerimage ?>">
  </div>
  -->
</div>

<nav><?php
/*
 * Website Header Bar includes:
 * -Links to other pages
 * -Non-linked accentuated type for own pages
 * -(Sub)title
*/

echo '<b id="menubar">';
foreach($wc_headertemplate_navigationpages as $key => $pagename){
	if( ! str_starts_with($title, $pagename) ){
		echo '<a id="pagelink" href="/sandbox/site/'.$wc_headertemplate_navigationlinks[$key].'">'.$pagename.'</a>';
	}
	else {
		echo $pagename;
	}
	if($key+1 != count($wc_headertemplate_navigationpages) ) echo ' : ';
}
echo '</b></br>';
?></nav>
<!--<hr>-->
</header>
<div class="PageCenter">
<div class="PageBody">
