<?php
# This cronjob removes comments with confirmed=0 as well as their corresponding commentss_uuids row,
# every day, if the comment is older than 7 days - thereby enforcing that comments must be verified by
# email within seven days.

include '../sql_users/blogdb_user.php';

function delete_unconfirmed_comments(){
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Delete rows from comments which are unconfirmed and older than 7 days.
	# This will cascade into the comments_uuids table and delete the corresponding row there as well.
	$SQL = 'DELETE FROM comments WHERE Confirmed=0 AND DateTime < (NOW() - INTERVAL 7 DAY)';
	
	# Perform Deletion.
	$query = $connection->query($SQL);
	
	if(!$query) {
		return 'Failed to delete unconfirmed comments: Error.';
	} else {
		# Number of deleted comments
		$count = $query->rowCount();
		return "Deleted $count old unconfirmed comments.";
	}
}

echo delete_unconfirmed_comments();
