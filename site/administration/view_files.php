<?php
require 'adminheadertemplate.php';
?>

<h2>Files in directory /files/:</h2>
<table>
<tr><th>Name</th> <th>Size</th></tr>
<?php
try{
/* ---- Pick up all files ------ */
	$files = array_diff(scandir('../files/'), ['.', '..']);
/* ---- Make a table ------ */
	foreach ($files as $file){
		echo '<tr><td><a href="../files/'.$file.'">'.$file.'</a></td><td>'.filesize('../files/'.$file).'</td></tr>';
	}
}
catch (Throwable $t){
	echo 'Error: Not able to retrieve files.';
}
?>
</table>

</br>

<h2>Private files in directory administration/files/:</h2>
<table>
<tr><th>Name</th> <th>Size</th></tr>
<?php
try{
/* ---- Pick up all files ------ */
	$files = array_diff(scandir('files/'), ['.', '..']);
/* ---- Make a table ------ */
	foreach ($files as $file){
		echo '<tr><td><a href="files/'.$file.'">'.$file.'</a></td><td>'.filesize('files/'.$file).'</td></tr>';
	}
}
catch (Throwable $t){
	echo 'Error: Not able to retrieve files.';
}
?>
</table>

</br>

<h2>Images in directory /images/:</h2>
<table>
<tr><th>Name</th> <th>Size</th></tr>
<?php
try{
/* ---- Pick up all files ------ */
	$files = array_diff(scandir('../images/'), ['.', '..']);
/* ---- Make a table ------ */
	foreach ($files as $file){
		echo '<tr><td><a href="../images/'.$file.'">'.$file.'</a></td><td>'.filesize('../images/'.$file).'</td></tr>';
	}
}
catch (Throwable $t){
	echo 'Error: Not able to retrieve files.';
}
?>
</table>

<?php require 'adminfootertemplate.php'?>
