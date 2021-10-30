<?php
$title = 'Blog - All Posts';
require 'headertemplate.php';
require 'blogfunctions.php';
print_blog_sidebar();
?>

<h2>Post History</h2>

<table>
<tr><th>Title</th> <th style="width : 110px;">Date</th></tr>
<?php
try{
/* ---- Pick up all recent blogposts ------ */
	$postlist = get_posts();
/* ---- Make a table of these posts ------ */
	foreach ($postlist as $post){
		echo '<tr><td><a href=post.php?n='.$post['PostTag'].'>'.$post['Title'].'</a></td><td>'.substr($post['DateTime'], 0, 10).'</td></tr>';
	}
}
catch (Throwable $t){
	echo 'Error: Not able to retrieve post.';
}
?>
</table>

<?php require 'footertemplate.php'?>
