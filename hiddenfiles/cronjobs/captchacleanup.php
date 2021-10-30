<?php
# This cronjob removes captcha rows from the form_captchas table every hour,
# if they are older than 3 hours - thereby limiting the number of active captchas.

include '../sql_users/blogdb_user.php';

function delete_old_captchas(){
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Delete rows from form_captchas which are older than 3 hours.
	$SQL = 'DELETE FROM form_captchas WHERE TimeStamp < (NOW() - INTERVAL 3 HOUR)';
	
	# Perform Deletion.
	$query = $connection->query($SQL);
	
	if(!$query) {
		return 'Failed to refresh captchas: Error.';
	} else {
		# Number of deleted comments
		$count = $query->rowCount();
		return "Deleted $count old captchas.";
	}
}

echo delete_old_captchas();
