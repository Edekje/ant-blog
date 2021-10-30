<?php
require 'adminheadertemplate.php';
require 'comments_user.php';
require 'blogfunctions.php';
require 'adminfunctions.php';


// Process any comment (de)publications or deletions:
try{
	if(isset($_GET['approve'])) {
		$commentid = (int) $_GET['approve'];
		make_comment_public($commentid, 1);
		echo "Set comment $commentid to public.";
	} else if(isset($_GET['hide'])) {
		$commentid = (int) $_GET['hide'];
		make_comment_public($commentid, 0);
		echo "Set comment $commentid to not public.";
	} else if(isset($_GET['delete'])) {
		$commentid = (int) $_GET['delete'];
		delete_comment($commentid);
		echo "Deleted comment $commentid.";
	}
} catch (Throwable $t) {
	echo 'Error approving/hiding/deleting comments. '.$t->getMessage();
}

// Determine which type of comments we are to show:
try{
	if(isset($_GET['Confirmed']) and isset($_GET['Public'])) { 
		$Confirmed = $_GET['Confirmed']=='1';
		$Public = $_GET['Public']=='1';
	} else { # Default to unapproved, confirmed comments.
		$Confirmed = 1;
		$Public = 0;
	}
	$Comments = list_comments($Confirmed, $Public);
} catch (Throwable $t) {
	echo 'Error Getting Comments.';
	$Comments = [];
}
?>

<h2>View Comments</h2>
<div class="AdminCommentViews">
<a href="view_comments.php?Confirmed=0&Public=0">Unconfirmed, Not Public (<?php echo list_comments(0, 0, 1)?>) </a> : 
<a href="view_comments.php?Confirmed=0&Public=1">Unconfirmed, Public (<?php echo list_comments(0, 1, 1)?>) </a> <br>
<a href="view_comments.php?Confirmed=1&Public=0">Confirmed, Not Public (<?php echo list_comments(1, 0, 1)?>) </a> :
<a href="view_comments.php?Confirmed=1&Public=1">Confirmed, Public (<?php echo list_comments(1, 1, 1)?>) </a> <br>
</div>

<p>Showing comments with Confirmed = <?php echo (int) $Confirmed ?> and Public = <?php echo (int) $Public ?>.</p>

<div class="AdminComments">
<?php
foreach ($Comments as $comment) {
	$post = get_post_int($comment['PostNumber']); // Get the post title to print.
	$posttitle = $post['Title'];
	$postname = $post['PostTag'];
	$commentemail = htmlspecialchars($comment['Email']);
	echo generate_comment_html($comment);
	echo "Comment {$comment['Comment_ID']} in <a href=\"../blog/post.php?n=$postname\">$posttitle</a> by: {$commentemail}.</br>";
	echo ($comment['Public']) ? "<a href=\"?hide={$comment['Comment_ID']}\">Hide</a> " : "<a href=\"?approve={$comment['Comment_ID']}\">Approve</a> ";
	echo "or <a href=\"?delete={$comment['Comment_ID']}\">Delete</a>? </br></br>";
}
?>
</div>

<?php require 'adminfootertemplate.php' ?>

