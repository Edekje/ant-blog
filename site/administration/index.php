<?php

require 'adminheadertemplate.php';
require 'comments_user.php';
try {
	$WaitingCom = list_comments(1, 0, 1);
	$TotalCom = list_comments(1, 1, 1);
} catch (Throwable $t) {
	echo $t->getMessage();
}
?>

<h2>Blog Administration</h2>

<ul class="AdminPageLinks">
 <li><a href="view_posts.php">View Posts</a></li>
 <li><a href="make_post.php">Make Post</a></li>
 <li><a href="edit_post.php">Edit Post</a></li>
 <li><a href="delete_post.php">Delete Post</a></li>
 <li><a href="view_comments.php">View Comments (<?php echo $WaitingCom?> Waiting, <?php echo $TotalCom?> Public)</a></li>
 <li><a href="upload.php">Upload</a></li>
 <li><a href="view_files.php">File Overview</a></li>
</ul>

<?php $disableadminreturnlink = true; require 'adminfootertemplate.php'?>
