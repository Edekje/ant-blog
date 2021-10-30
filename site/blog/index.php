<?php
require 'blogfunctions.php';
$last_post = get_posts(1);

if(count($last_post)!=0) {
	// If there is a recent post, direct user to it.
	header('Location: post.php?p='.$last_post[0]['PostNumber']);
	exit();
} else {
// If there are no publics posts, just display a message.
	$title = 'Blog'; 
	require 'headertemplate.php';
	print_blog_sidebar();
?>
<h1>Blog</h1>
<p>There are currently no posts to display.</p>
<?php require 'footertemplate.php'; } ?>
