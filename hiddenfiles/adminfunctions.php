<?php
/* Database login with read-write permissions,
 * giving $host, $dbname, $username, $password variables. */
include 'sql_users/blogdb_user.php';

/* make_post(string $PostTag, string $Title, [int $PostNumber=0])
 * Creates a post in the Blogposts table. It automatically sets the title,
 * and the given PostTag, and all the rest gets default numbers. You can choose a
 * custom PostNumber, but if PostNumber is 0, it just lets MYSQL auto-increment.
 * If a PostTag or PostNumber is already taken it throws a Taken Exception.
 * Any other errors get exceptions as well.
 * On Success, returns nothing.
 */
 
function make_post(string $PostTag, string $Title, int $PostNumber = 0) {
 	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	/* Verify that PostTag only contains english letters & digits, that it is valid */
	if (preg_match('/[^A-Za-z0-9-]/', $PostTag)) {
		throw new Exception('Invalid PostTag: Contains characters other than alphanumeric and hyphen.');
	}
	/* Verify PostNumber is valid, i.e.,  positive */
	if($PostNumber < 0){
		throw new Exception('Invalid PostNumber: Negative.');
	}
	
	/* Check if $PostTag already taken */
	$SQL_CheckTag = $connection->prepare('SELECT PostNumber FROM blogposts WHERE PostTag=:PostTag');
	$SQL_CheckTag->execute(['PostTag' => $PostTag]);
	if($SQL_CheckTag->rowCount()){
		$TakenNumber = $SQL_CheckTag->fetch()['PostNumber'];
		throw new Exception('PostTag already taken by post with PostNumber '.$TakenNumber);
	}
	
	/* If a specific $PostNumber is given, check if it is taken */
	if($PostNumber !== 0){
		$SQL_CheckNum = $connection->prepare('SELECT PostTag FROM blogposts WHERE PostNumber=:PostNumber');
		$SQL_CheckNum->execute(['PostNumber' => $PostNumber]);
		
		if($SQL_CheckNum->rowCount()){
			$TakenTag = $SQL_CheckNum->fetch()['PostTag'];
			throw new Exception('PostNumber already taken by post with PostTag '.$TakenTag);
		}
	}
	
	/* Both PostNumber and PostTag are not in table, proceed to insert:
	 * Note that we are outputting the new PostNumber on success
	 */
	if($PostNumber !== 0) { /* In this case we set an explicit PostNumber */
		$SQL_Code = 'INSERT INTO blogposts
		( Title, PostTag, PostNumber) VALUES (:Title, :PostTag, :PostNumber)';
		$SQL_InsertPost = $connection->prepare($SQL_Code);	
		$BindingValues = ['Title' => $Title, 'PostTag' => $PostTag, 'PostNumber' => $PostNumber];
	} else { /* Here we let MySQL set the PostNumber (Increment) */
		$SQL_Code = 'INSERT INTO blogposts
		( Title, PostTag) VALUES (:Title, :PostTag)';
		$SQL_InsertPost = $connection->prepare($SQL_Code);
		$BindingValues = ['Title' => $Title, 'PostTag' => $PostTag];
	}
	
	/* Run and Filter out False errors */
	if(!$SQL_InsertPost->execute($BindingValues)){
		print_r($SQL_InsertPost->errorInfo());
		throw new Exception('Inserting new Post Returned False (Error).');
	}
	/* Filter out no change error */
	if(!$SQL_InsertPost->rowCount()){
		throw new Exception('Inserting New Post Failed: 0 rows changed.');
	}

	return;
}

/* edit_post edits a post identified both by $PostTag and $PostNumber.
 * The Column changes are in the $Changes array.
 */
function edit_post(string $PostTag, int $PostNumber, array $Changes) {
 	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	/* There must be keys at all */
	if(empty($Changes)) throw new Exception('Error: Changes array empty.');
	/* Check Keys Are Valid */
	$ValidColumnNames = ['Title', 'SubTitle', 'DateTime', 'PostTag', 'PostNumber',
						 'Body', 'Tags', 'Public', 'Comments', 'Views'];
						 
	/* Start Building SQL Query */
	$SQLUpdateCode = 'UPDATE blogposts SET';
	foreach ($Changes as $Column => $Value){
		if(! in_array($Column, $ValidColumnNames)){
			throw new Exception('Invalid Changes Key: Does not correspond to a Column.');
		}
		/*  Add to SQL Query */
		$SQLUpdateCode .= " $Column = :$Column,";
	}
	/* Remove Last Comma to Make Valid */
	$SQLUpdateCode = mb_substr($SQLUpdateCode, 0, -1);
	$SQLUpdateCode .= ' WHERE PostTag = :PostTagID AND PostNumber = :PostNumberID';
	/* We also need to prepare the identifiers $PostTag and $PostNumber */
	$Changes['PostTagID'] = $PostTag;
	$Changes['PostNumberID'] = $PostNumber;
	
	/* Do Query */
	$SQL_Query = $connection->prepare($SQLUpdateCode);
	if(!$SQL_Query->execute($Changes)){
		throw new Exception('Update Call Failed');
	}
	/* If nothing changed, didn't go well */
	if($SQL_Query->rowCount() !== 1){
		throw new Exception('Edit Error: Either no Changes, or More than 1.');
	}
	
	return;
}

/* Deletes the Post entry in database, ony if both Tag and Number are correct. */
function delete_post(string $PostTag, int $PostNumber) {
 	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$Delete_Query = $connection->prepare('DELETE FROM blogposts WHERE PostTag=:PostTag AND PostNumber=:PostNumber');
	/* Execute and verify success*/
	if(!$Delete_Query->execute(['PostTag' => $PostTag, 'PostNumber' => $PostNumber]) ) {
		throw new Exception('Error: MySQL Query Execution returned False.');
	}
	/* Verify that an entry was deleted*/
	if(!$Delete_Query->rowCount()) {
		throw new Exception('Error: No rows deleted; perhaps incorrect PostTag, PostNumber?');
	}
	
	return; /* Successful deletion */
}

/* Flips the Public Boolean. Can take a PostTag string or a PostNumber int as Post Identifier!
 * Throws exceptions if anything went wrong, otherwise it just ends.
 */
function flip_public($PostID) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# This part of Query flips Public
	$FlipSQL		= 	'UPDATE blogposts
						SET Public = NOT PUBLIC';
	# These two Select the post whether a PostTag or PostNumber has been given.
	$PostTagSQL = ' WHERE PostTag = :PostTag;';
	$PostNumberSQL = ' WHERE PostNumber = :PostNumber;';
	
	# Execute statement depending on what is supplied.
	if(is_string($PostID)){
		$SQL_statement = $connection->prepare($FlipSQL.$PostTagSQL);
		$SQL_statement->execute(['PostTag' => $PostID]);
	}
	elseif(is_int($PostID)) {
		$SQL_statement = $connection->prepare($FlipSQL.$PostNumberSQL);
		$SQL_statement->execute(['PostNumber' => $PostID]);
	}
	# This should never come to here
	else{
		throw new Exception('Wrong type supplied: needs int/string.');
	}
	# Pass on Exception on MYSQL Query error
	if(! $SQL_statement){
		throw new Exception('SQL Query Unsuccessful (Flip Public)');
	}
	# There has to be 1 row changed otherwise nothing happened, entry did not exist.
	if(! $SQL_statement->rowCount()) {
		throw new Exception('Update did not manage to change any rows.');
	}
}

function make_comment_public($Comment_ID, $Public=1) {
	# Make a Comment with $CommentID Public (1) or Not Public (0).
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$CommentSQL = 'UPDATE comments SET Public=:Public WHERE Comment_ID=:Comment_ID';
	# Prepare
	$SQL_Query = $connection->prepare($CommentSQL);
	# Execute
	$SQL_Query->execute(['Comment_ID' => $Comment_ID, 'Public' => $Public]);
	# Error check
	if(!$SQL_Query) {
		throw new Exception('SQL Query Failed: Make Comment Public');
	}
	# Check if UPDATE changed zero rows.
	if(!$SQL_Query->rowCount()) {
		throw new Exception('SQL Query Failed: No Comments Changed.');
	}
}

function delete_comment($Comment_ID) {
	# Delete Comment.
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$CommentSQL = 'DELETE FROM comments WHERE Comment_ID=:Comment_ID';
	# Prepare
	$SQL_Query = $connection->prepare($CommentSQL);
	# Execute
	$SQL_Query->execute(['Comment_ID' => $Comment_ID]);
	
	# Error check
	if(!$SQL_Query) {
		throw new Exception('SQL Query Failed: Delete Comment');
	}
	# Check if UPDATE changed zero rows.
	if(!$SQL_Query->rowCount()) {
		throw new Exception('SQL Query Failed: No Deletions.');
	}
}
