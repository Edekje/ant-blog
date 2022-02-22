<?php
/* Database login with read-write permissions,
 * giving $host, $dbname, $username, $password variables. */
include 'sql_users/blogdb_user.php';

function list_notes($SortBy, $Order) {
	# $SortBy : Name=0 LastModified=1
	# $Order: Asc=0 Desc=1
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$SQL = 'SELECT Name, LastModified, ID FROM notes ORDER BY';
	$SQL .= ($SortBy) ?  ' LastModified' : ' Name';
	$SQL .= ($Order) ? ' DESC' : ' ASC';
	
	# Prepare and execute
	$SQL_Query = $connection->query($SQL);
	
	return $SQL_Query->fetchAll();
}

function get_note($ID) {
	# $SortBy : Name=0 LastModified=1
	# $Order: Asc=0 Desc=1
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$SQL = 'SELECT * FROM notes WHERE ID=:ID';
	
	# Prepare and execute
	$SQL_Query = $connection->prepare($SQL);
	if(!$SQL_Query->execute(['ID' => $ID])) {
		throw new Exception('Database failure.');
	}
	
	$SQL_Query->setFetchMode(PDO::FETCH_ASSOC); # Return an associative array.
	$Note = $SQL_Query->fetch();
	
	# Filter out the case where nothing was found.
	if(! $Note) {
		throw new Exception("Note not found.");
	}
	
	return $Note;
}

function edit_note($ID, $Name, $Body) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	if(empty($Name)) {
		throw new Exception('Edit Error: name may not be empty.');
	}
	
	$SQLUpdateCode = 'UPDATE notes SET Name=:Name, Body=:Body, LastModified=NOW() WHERE ID=:ID';
	$SQL_Query = $connection->prepare($SQLUpdateCode);
	
	$Changes = ['ID' => $ID, 'Name' => $Name, 'Body' => $Body];
	
	if(!$SQL_Query->execute($Changes)){
		throw new Exception('Edit Query Failed');
	}
	/* If nothing changed, didn't go well */
	if($SQL_Query->rowCount() !== 1){
		throw new Exception('Edit Error: Either no Changes, or More than 1.');
	}	
}

function delete_note($ID) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$SQLUpdateCode = 'DELETE FROM notes WHERE ID=:ID';
	$SQL_Query = $connection->prepare($SQLUpdateCode);
	
	$Changes = ['ID' => $ID];
	
	if(!$SQL_Query->execute($Changes)){
		throw new Exception('Delete Query Failed');
	}
	/* If nothing changed, didn't go well */
	if($SQL_Query->rowCount() !== 1){
		throw new Exception('Delete Error: Either no Changes, or More than 1.');
	}	
}

function create_note($Name) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	if(empty($Name)) {
		throw new Exception('Creation Error: name may not be empty.');
	}
	
	$SQLUpdateCode = 'INSERT INTO notes (Name, LastModified) VALUES (:Name, NOW() )';
	$SQL_Query = $connection->prepare($SQLUpdateCode);
	
	$Changes = ['Name' => $Name];
	
	if(!$SQL_Query->execute($Changes)){
		throw new Exception('Error creating note.');
	}
}
