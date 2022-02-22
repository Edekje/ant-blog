<?php
/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);*/
require 'adminheadertemplate.php';
require 'notesfunctions.php';

try{
	if(isset($_GET['confirmdelete'])) {
		$ID = $_GET['ID'];
		delete_note($ID);
		unset($_GET['ID']);
		echo 'Post deleted. <a href="view_notes.php" class="AdminPageLinks">Return to Notes</a>.';
	}
} catch (Throwable $t) {
	echo 'Delete failed.'.$t->getMessage();
}

try{
	if(isset($_POST['SubmitEdit'])) {
		$ID = $_POST['EditID'];
		$Name = $_POST['EditName'];
		$Body = $_POST['EditBody'];
		edit_note($ID, $Name, $Body);
		echo 'Edit Saved.';
		$_GET['ID'] = $ID;
	}
} catch (Throwable $t) {
	echo 'Edit failed.';
}


try{
	if(isset($_GET['ID'])) {
		$Note = get_note(intval($_GET['ID']));
		
		$dt = new DateTime($Note['LastModified']);
		$SplitDate = $dt->format('d-m-Y'); 
		$SplitTime = $dt->format('H:i');
		
		$ViewLink   = "?ID={$Note['ID']}";
		$DeleteLink = $ViewLink.'&delete=1';
		$ConfirmDeleteLink = $ViewLink.'&confirmdelete=1';
		$EditLink   = $ViewLink.'&edit=1';
	}
	
} catch (Throwable $t) {
	echo 'Note not found.';
}

if(isset($_GET['ID'])) {

if(isset($_GET['delete'])) { ?>
Click <a href="<?php echo $ConfirmDeleteLink ?>" class="AdminPageLinks">HERE</a> to confirm you wish to delete this post.
<?php }
$EditViewHTML = '<a class="AdminPageLinks" href="'.( (isset($_GET['edit'])) ? $ViewLink : $EditLink ).'">'.( (isset($_GET['edit'])) ? 'View' : 'Edit' ).'</a>';
?>
<h3>Note: &ldquo;<?php echo htmlspecialchars($Note['Name']) ?>&ldquo; - <?php echo $EditViewHTML ?>, <a class="AdminPageLinks" href="<?php echo $DeleteLink ?>">Delete</a>, <a href="view_notes.php" class="AdminPageLinks">Return</a>.</h3>
<?php if(isset($_GET['edit'])) { ?>
<form method="Post" class="TidyForm">
	<input type="hidden" name="EditID" value="<?php echo $Note['ID'] ?>">
	<p>
	<label for="EditName">Name: </label>
	<input type="text" name="EditName" value="<?php echo htmlspecialchars($Note['Name']) ?>">
	</p>
	<p>
	<label for="EditBody" style="vertical-align: top;">Content: </label>
	<textarea name="EditBody" style="padding: 5px 5px; width: 660px; height: 300px;"><?php echo htmlspecialchars($Note['Body']) ?></textarea>
	<br>
	<input type="submit" value="Submit Edit" name="SubmitEdit">
	<input type="submit" value="Discard Edit" name="DiscardEdit" style="float:right;">
	</p>
</form>
<?php } else { ?>
<p><i>Last modified on <?php echo $SplitDate ?> at <?php echo $SplitTime ?></i></p> <br>

<div class="NoteBody">
<?php echo $Note['Body']?>
</div>
<?php } } 
require 'adminfootertemplate.php';


