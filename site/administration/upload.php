<?php
require 'adminheadertemplate.php';

function do_upload(string $Type, int $MaxSize, bool $Private=True) {
	// MaxSize is max number of bytes, Type is either 'files', or 'images'
	// Which determines the upload folder.
	global $msg;
	// Check whether a file has been uploaded.
	if (! is_string($_FILES['Upload'.$Type]['tmp_name']) ) {
		$msg = 'No valid file has been given.';
		throw new Exception();
	}
	// Check if file is too big
	if ($_FILES['Upload'.$Type]['size'] > $MaxSize) {
		$msg = 'File too large; max 100MB for files, 10MB for images!';
		throw new Exception();
	}
	// Check whether file name is set, and is valid and not too long.
	if (empty($_POST['Name'.$Type])) {
		$msg = 'New File Name has not been set.';
		throw new Exception();
	}
	$NewFileName = $_POST['Name'.$Type];
	if (! is_string ( $NewFileName )) {
		$msg = 'New File Name has not been supplied as string.';
		throw new Exception();
	}
	if ( mb_strlen( $NewFileName ) < 1 or mb_strlen( $NewFileName ) > 30 ) {
		$msg = 'File name must be between 1 and 30 characters.';
		throw new Exception();
	}
	if ( preg_match('/[^A-Za-z0-9.-]/', $NewFileName) ) {
		$msg = 'File name must contain only alphanumeric, hyphen, and full stop characters.';
		throw new Exception();
	}
	if ( preg_match('/^[.].*/', $NewFileName) ) {
		$msg = 'File name may not start with a full stop.';
		throw new Exception();
	}
	// Check whether the name has been taken already
	if( file_exists('../'.$Type.'/'.$NewFileName) ) {
		$msg = 'File name already taken; choose a different one.';
		throw new Exception();
	}
	// Execute upload
	$dir = $Type;
	if($Private and $Type=='files') {
		$dir = 'administration/files';
	}
	if ( move_uploaded_file($_FILES['Upload'.$Type]['tmp_name'], '../'.$dir.'/'.$NewFileName) ) {
		$msg = "File uploaded successfully with name $NewFileName to the directory $dir!";
	} else {
		$msg = 'File upload failed';
	}
}

try {
	$msg = ''; // Deposit error messages here.
	if( isset($_POST['Submitfiles']) ) {
		// Determine if destination is to /administration/files:
		$Private = isset($_POST['Private']) and $_POST['Private'];
		do_upload('files', 100 * 1024 * 1024, $Private); // Max File 100 MB
	} elseif ( isset($_POST['Submitimages'] ) ) {
		do_upload('images' , 10 * 1024 * 1024); // Max Image 10 MB
	}
} catch (Throwable $t) {
	if (empty($msg)) {
		$msg = 'Unspecified Exception Occured';
	}
}

if(isset($msg)) {
	echo $msg;
}
?>

<h2>Upload</h2>

<h3>Upload File</h3>
<form action="" method="post" enctype="multipart/form-data" class="TidyForm">
  <p>
  <label for="Uploadfiles">Select File:&nbsp;</label>
  <input type="file" name="Uploadfiles">
  </p>
  <p>
  <label for="Namefiles">File Name:&nbsp;</label>
  <input type="text" name="Namefiles">
  </p>
  <p>
  <label for="Private">Private:&nbsp;</label>
  <input type="checkbox" name="Private"><label>(administration/files)</label>
  </p>
  <p><input type="submit" value="Upload File" name="Submitfiles"></p>
</form>

<br>

<h3>Upload Image</h3>
<form action="" method="post" enctype="multipart/form-data" class="TidyForm">
  <p>
  <label for="Uploadimages">Select Image:&nbsp;</label>
  <input type="file" name="Uploadimages">
  </p>
  <p>
  <label for="Nameimages">File Name:&nbsp;</label>
  <input type="text" name="Nameimages">
  </p>
  <p><input type="submit" value="Upload Image" name="Submitimages"></p>
</form>

<?php require 'adminfootertemplate.php'?>
