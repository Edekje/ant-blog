<?php
require 'adminheadertemplate.php';
require 'adminfunctions.php';

/* If The following vars have been posted, process form input and make post. */
if( isset($_POST['PostTag'], $_POST['Title'], $_POST['PostNumber']) ) {
	try {
		$PostTag=$_POST['PostTag']; $PostTitle= $_POST['Title']; $PostNumber=$_POST['PostNumber'];
		
		# Check if PostNumber is a proper integer before converting to prevent sending to 0.
		if ( ! preg_match('/^\d+$/', $PostNumber) ) {
			echo 'PostNumber is not a valid integer';
		}
		else {
			make_post($PostTag, $PostTitle, (int) $PostNumber);
			echo "Successfully created post titled: '$PostTitle'\nwith tag '$PostTag'";
		}
	}
	catch(Throwable $t) {
		echo 'Error whilst making post: '.$t->getMessage();
	}
}

?>

<h2>Make Post</h2>

<form method="Post">
<label for="PostTag">Post Tag: </label> <input type="text" name="PostTag"> <br>
<label for="Title">Title: </label> <input type="text" name="Title"> <br>
<label for="PostNumber">Post Number: </label> <input type="text" name="PostNumber" value="0"> <br>
<input type="submit">
</form>

<?php require 'adminfootertemplate.php'?>
