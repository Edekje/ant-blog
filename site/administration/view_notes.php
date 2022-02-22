<?php
require 'adminheadertemplate.php';
require 'notesfunctions.php';


// Make a new note
try{
	if(isset($_POST['SubmitName'])) {
		if(! empty($_POST['CreateName'])) {
			create_note($_POST['CreateName']);
			echo 'Note created.';
		} else {
			echo 'Name may not be empty.';
		} 
	}
} catch (Throwable $t) {
	echo 'Error creating note.';
}

//Get list of notes
try{
	if(isset($_GET['sort']) and isset($_GET['order'])) { 
		$SortBy = $_GET['sort']=='1';
		$Order = $_GET['order']=='1';
	} else { # Default to sort by name, descending.
		$SortBy = False; # Name=0 Date=1
		$Order = False; # Asc=0 Desc=1
	}
	$Notes = list_notes($SortBy, $Order);
} catch (Throwable $t) {
	echo 'Error Getting Notes.';
	$Notes = [];
}
?>

<div>
<h2 style="display: inline; ">Notes</h2>
<form method="Post" style="display: inline; float:right;">
	<label for="CreateName">New Note: </label>
	<input type="text" name="CreateName"">
	<input type="submit" value="Submit" name="SubmitName">
</form>
<div/>
<br>

<table class="NotesTable">
<tr><th><a href="?sort=0&order=<?php echo ((!$SortBy) ? intval(!$Order) : 0) ?>">Name</a></th><th><a href="?sort=1&order=<?php echo (($SortBy) ? intval(!$Order) : 0) ?>">Last Modified</a></th></tr>
<?php foreach ($Notes as $Note) { ?>
<tr><td><a href="note.php?ID=<?php echo $Note['ID'] ?>"><?php echo htmlspecialchars($Note['Name']) ?></a></td><td><?php echo (new DateTime($Note['LastModified']))->format('H:i d-m-Y') ?></td></tr>
<?php } ?>
</table>

<?php
require 'adminfootertemplate.php';

