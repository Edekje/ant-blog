<?php

require 'adminheadertemplate.php';
require 'blogfunctions.php';
require 'adminfunctions.php';

/* Form input elements: */
$Elements = ['PostTag', 'PostNumber', 'Public', 'Title', 'SubTitle', 'DateTime', 'Tags', 'Comments', 'Body'];

try {
	if(isset($_POST['SubmitEdit']) and isset($_POST['EditPostTag']) and isset($_POST['EditPostNumber'])) {
		/* If an edit has been submitted we will prepare a $NewPost to be sent as edit. */
		$NewPost = [];
		foreach ($Elements as $Element) {
			if (isset($_POST[$Element.'Box']) and isset($_POST[$Element])) {
				$NewPost[$Element] = $_POST[$Element];
			}
		}
		if(empty($NewPost)) {
			echo 'No changes given; nothing done!';
		} else {
		edit_post($_POST['EditPostTag'], $_POST['EditPostNumber'], $NewPost);
		echo 'Successfully edited post!';
		}
	}
} catch (Throwable $t) {
	echo 'Error editing post';
	/* echo $t->getMessage(); Debug */
}

try{
	if( !empty($_POST['EditPostTag'])) {		

		/* Get the post and display it corresponding to the EditPostTag */
		$Post = get_post_str($_POST['EditPostTag'], False);
		$PostTag = $Post['PostTag'];
		$PostNumber = $Post['PostNumber'];
	} elseif( !empty($_POST['EditPostNumber']) ) {
		/* A post can also be requested by number */
		$Post = get_post_int( (int) ($_POST['EditPostNumber']), False);
		$PostTag = $Post['PostTag'];
		$PostNumber = $Post['PostNumber'];
	} else {
		/* If nothing has been supplied, let the user input a Tag or Number */
		$Post = False;
	}
} catch (Throwable $t) {
	echo 'Error getting post';
	/* echo $t->getMessage(); Debug */
	$Post = False;
}
?>

<h2>Edit Post</h2>

<?php if($Post) { /* Case that a PostTag/PostNumber to display has been supplied. */ ?>
<form method="Post" class="EditPostForm">
	<input type="hidden" name="EditPostTag" value="<?php echo htmlspecialchars($PostTag) ?>">
	<input type="hidden" name="EditPostNumber" value="<?php echo htmlspecialchars($PostNumber) ?>">
	<?php
	foreach ($Elements as $Element) { if ($Element !== 'Body') {?>
	<p>
	<label for="<?php echo htmlspecialchars($Element) ?>"><?php echo htmlspecialchars($Element) ?>: </label>
	<input type="text" name="<?php echo htmlspecialchars($Element) ?>" value="<?php echo htmlspecialchars($Post[$Element]) ?>">
	<input type="checkbox" name="<?php echo htmlspecialchars($Element) ?>Box">
	</p>
	<?php } else { ?>
	<p>
	<label for="Body" style="vertical-align: top;">Body: </label>
	<textarea name="Body" style="padding: 5px 5px; width: 660px; height: 300px;"><?php echo htmlspecialchars($Post['Body']) ?></textarea>
	<input type="checkbox" name="BodyBox"> <br>
	<?php } } ?>
	<input type="submit" value="Submit Edit" name="SubmitEdit">
	</p>
</form>
<?php } else { /* Case that we need to request a PostTag or PostNumber */ ?>
<p>Please enter a Post Tag or Post Number to edit:</p>
<form method="Post">
<label for="EditPostTag">Post Tag: </label> <input type="text" name="EditPostTag"> <br>
<input type="submit">
</form>
<p>or</p>
<form method="Post">
<label for="EditPostNumber">Post Number: </label> <input type="number" name="EditPostNumber"> <br>
<input type="submit">
</form>
<?php } ?>

<?php require 'adminfootertemplate.php'?>
