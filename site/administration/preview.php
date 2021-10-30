<?php
/* ---- FUNCTION OF THIS PAGE
 * Copy of /post.php but allows viewing non-public posts.
 * Get a post labelled by the postnumber parameter ?p=...*
 * There is all sorts of error processing in the case this is not possible.
 * This will create a page with an appropriate Browser Title
 * As Well as an <h2> level title, telling you where we are located. 
 */

/* ---- OUTPUT ALL ERRORS FOR DEBUGGING PURPOSES: ---- */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

/* ---- GET THE $post OR SET $msg DEPENDING ON PARAMETER $_GET['p']: ---- */
require 'blogfunctions.php';
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
			$post = get_post_str( $PostTag, False);
	}
	elseif( isset($_GET['p']) ) {
			$postnum = (int) $_GET['p'];
			if($postnum <= 0){
				throw new Exception("Postnumber $postnum invalid.");
			}
			$post = get_post_int( $postnum, False);
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
}

/* ---- GENERATE THE PAGE HEADER USING $title ---- */

require 'headertemplate.php';

/* ---- PRINT EITHER $post (SUCCESSFULL) OR $msg (UNSUCCESSFULL)---- */
if( !isset($msg) ) { # SUCCESFULL CASE?>
<h2> (Preview) </h2>
<h2><?php echo $post['Title'] ?></h2>

<h3><?php echo $post['SubTitle'] ?></h3>

Date/Time: <?php echo $post['DateTime'] ?> Views: <?php echo $post['Views'] ?> </br>

Tags: <?php echo $post['Tags'] ?>

<?php echo $post['Body']?>

<?php }
else{ # UNSUCCESSFULL CASE
	echo $msg;
}

/* ---- GENERATE THE PAGE FOOTER: ---- */
require 'footertemplate.php';
