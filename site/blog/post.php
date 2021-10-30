<?php 
/* ---- FUNCTION OF THIS PAGE
 * Get a post labelled by the postnumber parameter ?p=...*
 * There is all sorts of error processing in the case this is not possible.
 * This will create a page with an appropriate Browser Title
 * As Well as an <h2> level title, telling you where we are located. */

/* ---- OUTPUT ALL ERRORS FOR DEBUGGING PURPOSES: ---- */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

/* ---- GET THE $post OR SET $msg DEPENDING ON PARAMETER $_GET['p']: ---- */
require 'blogfunctions.php';
require 'comments_user.php';
/* The following set of if clauses does the following:
 * If there is no parameter p:
 * 	Set  $msg 'No Post Selected'
 * If there is, cast p into an int $postnum
 *	If $postnum is invalid (<=0)
 *		throw an Exception "Postnumber $postnum invalid"
 * 	If $postnum is valid
 * 		Get the post $postnum and save in $post.
 * 		If $postnum post does not exist set message "Post $postnum is not available"
 *  Output any exceptions by setting them into $msg.
 *
 * Therefore this means that whenever $msg is not set, a $post has been successfully retrieved!
 * The full output either $post or $msg has therefore been given.
 */
try{
	if( isset($_GET['n']) ) {
			$PostTag = $_GET['n'];
			if (preg_match('/[^A-Za-z0-9-]/', $PostTag)) { // '/[^a-z\d]/i' should also work.
				throw new Exception('PostTag contains characters other than alphanumeric and hyphen.'); // string contains only english letters & digits
			}
			$post = get_post_str( $PostTag );
			/* Get Next Prev Post information */
			$NextPrev = get_next_prev($post['PostNumber']);
	}
	elseif( isset($_GET['p']) ) {
			$postnum = (int) $_GET['p'];
			if($postnum <= 0){
				throw new Exception("Postnumber $postnum invalid.");
			}
			$post = get_post_int( $postnum );
			/* Get Next and Prev Post information */
			$NextPrev = get_next_prev($post['PostNumber']);
	}
	else{
		$msg = 'No Post Selected. View all posts <a href="posts.php">HERE</a>.';
	}
}
catch(Throwable $t){
	# Only for Developer use:
	# $msg = $t->getMessage();  #$msg becomes the exception message.
	# For Public use:
	$msg = 'Post not found. View all posts <a href="posts.php">HERE</a>.';
}

/* ---- IF SUCCESSFULL SET TITLE TO 'Blog-'$post['Title']+' EthanvanWoerkom.com' ELSE TO 'Blog' ---- */

if( isset($msg) ) {
	$title = 'Blog';
}
else {
	$title = 'Blog-'.$post['Title'];
	/* ---- SPLIT DATE & TIME -----*/
	$SplitDate = substr($post['DateTime'], 0, 10); 
	$SplitTime = substr($post['DateTime'], 11, 8);
}

/* ---- GENERATE THE PAGE HEADER USING $title ---- */

require 'headertemplate.php';

print_blog_sidebar();
echo '<h1>Blog Post:</h1>';

/* ---- PRINT EITHER $post (SUCCESSFULL) OR $msg (UNSUCCESSFULL)---- */
if( !isset($msg) ) { /* SUCCESFULL CASE */ ?>
<article>
<div class="PostHeader">

<h1><?php echo $post['Title'] ?></h1>

<h2><?php echo $post['SubTitle'] ?></h2>

<div class="ArticleInfo">
	<time datetime="<?php echo $post['DateTime'] ?>">Posted on <?php echo $SplitDate ?> at <?php echo $SplitTime ?></time>.
</div>

</div>
<?php echo $post['Body']?>

<div class="ArticleInfo">
	Views: <?php echo $post['Views'] ?>, Tags: <?php echo $post['Tags'] ?>.
</div>
</article>

<div class="NextPrevPost">
<?php if(isset($NextPrev['Prev'])) { ?>
<div class="PrevPost"><a href="post.php?n=<?php echo $NextPrev['Prev']['PostTag'] ?>">&larr; "<?php echo $NextPrev['Prev']['Title'] ?>"</a></div>
<?php } ?>
<?php if(isset($NextPrev['Next'])) { ?>
<div class="NextPost"><a href="post.php?n=<?php echo $NextPrev['Next']['PostTag'] ?>">"<?php echo $NextPrev['Next']['Title'] ?>" &rarr;</a></div>
<?php } ?>
</div>

<div class="Comments">
<h2>Comments</h2>
<?php if($post['Comments']) {
/* Here resides the comment section. */
$Comments = list_comments(1, 1, 0, $post['PostNumber']);
foreach ($Comments as $comment) {
	echo generate_comment_html($comment);
}
?>
</br>
<div class="CommentForm">
<?php /* Here resides the comment form. Check whether a form has been posted, and if so, call the write_comment() function */
if(isset($_POST['FormID'])) {
	try {
		$FormMsg = write_comment($_POST['E-mail'], $_POST['Name'], $_POST['CommentText'],
								 $_POST['FormID'], $_POST['HumVer']);
	}
	catch(Throwable $t){
	/* For Public use: */
	$FormMsg = 'Form Submission Failed.';
	/* Only for Developer use:
	$FormMsg .= $t->getMessage();*/
	}
	echo '<span class="CommentFormMessage">'.$FormMsg.'</span>';
}
if(isset($_POST['E-mail']) and isset($_POST['Name']) and isset($_POST['CommentText'])) {
	// If the form has been posted, refill it.
	echo make_comment_form($post['PostNumber'], htmlspecialchars($_POST['E-mail']), htmlspecialchars($_POST['Name']), htmlspecialchars($_POST['CommentText']));
} else {
	// Case that the form was not previously posted.
	echo make_comment_form($post['PostNumber']);
} ?>
</div>
<?php } else { ?>
<em>Comments are disabled on this post.</em>
<?php } ?>
</div>

<?php }
else{ # UNSUCCESSFULL CASE
	echo $msg;
}

/* ---- GENERATE THE PAGE FOOTER: ---- */
require 'footertemplate.php';
