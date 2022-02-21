<?php
require 'adminheadertemplate.php';
require 'adminfunctions.php';

/* If The following vars have been posted, process form input and delete post. */
if( isset($_POST['PostTag'], $_POST['PostNumber']) ) {
	try {
		$PostTag=$_POST['PostTag']; $PostNumber=$_POST['PostNumber'];

		delete_post($PostTag, (int) $PostNumber);
		echo "Successfully deleted post with tag '$PostTag' and number $PostNumber";
		}
	catch(Throwable $t) {
		echo 'Error whilst deleting post: '.$t->getMessage();
	}
}
?>

<h2>Delete Post</h2>

<p>Please enter both post tag and number to confirm your decision:

<form method="Post">
<label for="PostTag">Post Tag: </label> <input type="text" name="PostTag"> <br>
<label for="PostNumber">Post Number: </label> <input type="text" name="PostNumber"> <br>
<input type="submit">
</form>

<?php require 'adminfootertemplate.php'?>
