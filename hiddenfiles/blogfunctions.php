<?php
/* Database Log-In with read-write permissions,
 * giving $host, $dbname, $username, $password variables. */
include 'sql_users/blogdb_user.php';

/* Gets Title, PostNumber, PostTag, DateTime, Views, Public of $MAX many posts
 * Either sorts to get most recent ($Top = False) or most viewed ($Top = True)
 * If $Public is false it also displays non public posts. $MAX = 0 implies get all posts.
 * Throws an exception if something goes wrong.
 */
 
function get_posts(int $MAX=0, bool $Public = True, bool $Top=False){
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Setup SELECT SQL Query to get Title, PostNumber, PostTag, DateTime, Views, Public from blogposts.
	$getposts_sql = 'SELECT Title, SubTitle, PostNumber, PostTag, DateTime, Views, Tags, Public FROM blogposts';
	
	# If $Public is true only show Public posts.
	if($Public) {
		$getposts_sql .=  ' WHERE Public=1';
	}
	
	# If $Top True, rank posts by views, else by PostNumber / Most Recent
	if($Top){
		$getposts_sql .=  ' ORDER BY Views DESC';
	}
	else{
		$getposts_sql .=  ' ORDER BY PostNumber DESC';
	}
	
	# Introduce a limit in posts if $MAX is non-zero.
	if($MAX !== 0){
		$getposts_sql .= " LIMIT $MAX";
	}

	# Now perform the query.
	$query = $connection->query($getposts_sql);
	
	# If the query is unsuccessful throw an exception.
	if(!$query){
		throw new Exception('Database query failure.');
	}
	
	$query->setFetchMode(PDO::FETCH_ASSOC); # Set fetchAll to return associative array (['Item'])
	$ans = $query->fetchAll();

	return $ans;
}

/* Improved get_post(int $pagenum):
 * Returns $post information array. Throws "Not found" or "Connection Error" exceptions,
 * if something goes wrong instead of returning false.
 * If $Public true, only show public posts and do not increment view counter. (Default)
 */

function get_post_int(int $PostNumber, bool $Public = True) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Prepare a Public and Non-Public statement to prevent MySQL Injection:
	# Attempts to find the post $PostTag and then increment the viewcounter.
	$PublicSQL		= 'SELECT * FROM blogposts WHERE (PostNumber=:PostNumber AND Public=1);
						UPDATE blogposts SET Views=Views+1 WHERE PostNumber=:PostNumber;';
	$NonPublicSQL	= 'SELECT * FROM blogposts WHERE PostNumber=:PostNumber';
	
	# Execute Public and Non-Public cases
	if($Public){
		$getpost_statement = $connection->prepare($PublicSQL);
	}
	else { # In admin case, do not increment views and ignore public marker.
		$getpost_statement = $connection->prepare($NonPublicSQL);
	}
	
	# Fetch data.
	$getpost_statement->execute(['PostNumber' => $PostNumber]);
	$getpost_statement->setFetchMode(PDO::FETCH_ASSOC); # Set fetch to return associative array (['Item'])
	$searchresult = $getpost_statement->fetch();
	
	# Filter out the case where nothing was found.
	if(!$searchresult) {
		throw new Exception("Post not found.");
	}
	
	return $searchresult; # Just return the single row entry
}

/* get_post_str(str $posttag) does the same as above:
 * Does the same but searches by tag instead.
 * But it is totally injection safe.
 * And does not output the tag to exception to prevent an injection of JS script.
 */
 
function get_post_str(string $PostTag, bool $Public = True) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Prepare a Public and Non-Public statement to prevent MySQL Injection:
	# Attempts to find the post $PostTag and then increment the viewcounter.
	$PublicSQL		= 'SELECT * FROM blogposts WHERE (PostTag=:PostTag AND Public=1);
						UPDATE blogposts SET Views=Views+1 WHERE PostTag=:PostTag;';
	$NonPublicSQL	= 'SELECT * FROM blogposts WHERE PostTag=:PostTag';
	
	# Execute Public and Non-Public cases
	if($Public){
		$getpost_statement = $connection->prepare($PublicSQL);
	}
	else { # In admin case, do not increment views and ignore public marker.
		$getpost_statement = $connection->prepare($NonPublicSQL);
	}
	
	# Fetch data.
	$getpost_statement->execute(['PostTag' => $PostTag]);
	$getpost_statement->setFetchMode(PDO::FETCH_ASSOC); # Set fetch to return associative array (['Item'])
	$searchresult = $getpost_statement->fetch();
	
	# Filter out the case where nothing was found.
	if(!$searchresult) {
		throw new Exception("Post not found.");
	}
	
	return $searchresult; # Just return the single row entry
}

/* Gets the next and previous post to the current post $PostNumber. If $Public is True, only show public posts.
 * Returns an array with ['Next'] and ['Prev'] table rows.
 * Sorts by POSTNUMBER not Date.
 * Throws an exception if nothing is found.
 * Gets Title, PostNumber, DateTime, PostTag. */
function get_next_prev(int $PostNumber, bool $Public = True) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Prepare a Public and Non-Public statement to prevent MySQL Injection:
	$SelectQuery = 'SELECT Title, PostNumber, DateTime, PostTag FROM blogposts';	
	$NextPublicSQL		= $SelectQuery.' WHERE (PostNumber > :PostNumber AND Public=1) ORDER BY PostNumber LIMIT 1';
	$PrevPublicSQL		= $SelectQuery.' WHERE (PostNumber < :PostNumber AND Public=1) ORDER BY PostNumber DESC LIMIT 1';
	
	$NextNonPublicSQL	= $SelectQuery.' WHERE PostNumber > :PostNumber ORDER BY PostNumber LIMIT 1';
	$PrevNonPublicSQL	= $SelectQuery.' WHERE PostNumber < :PostNumber ORDER BY PostNumber DESC LIMIT 1';
	
	# Execute Public and Non-Public cases
	if($Public){
		$getnext_statement = $connection->prepare($NextPublicSQL);
		$getprev_statement = $connection->prepare($PrevPublicSQL);
	}
	else { # In admin case, do not increment views and ignore public marker.
		$getnext_statement = $connection->prepare($NextNonPublicSQL);
		$getprev_statement = $connection->prepare($PrevNonPublicSQL);
	}

	# Fetch data.
	$getnext_statement->execute(['PostNumber' => $PostNumber]);
	$getprev_statement->execute(['PostNumber' => $PostNumber]);
	
	# Save results.
	$nextresult = $getnext_statement->fetch(PDO::FETCH_ASSOC); # Set fetch to return associative array (['Item'])
	$prevresult = $getprev_statement->fetch(PDO::FETCH_ASSOC); # Set fetch to return associative array (['Item'])
	
	$result = [];
	
	# Save Next result associatively
	if($nextresult) {
		$result['Next'] = $nextresult;
	}

	# Save Prev result associatively
	if($prevresult) {
		$result['Prev'] = $prevresult;
	}
	
	# If we have just have [] (=False), throw exception.
	if(!$result) {
		throw new Exception('Found Nothing');
	}
	
	return $result;
}

function get_tags(bool $Public=True) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	/* Get all tags or only from Public Posts*/
	$SQL_Tags = 'SELECT Tags FROM blogposts' . ( ($Public) ? ' WHERE Public=1' : '' );
	
	$query = $connection->query($SQL_Tags);
	if(!$query) {
		throw new Exception('Tags Query failure.');
	}
	
	$query->setFetchMode(PDO::FETCH_ASSOC); # Set fetchAll to return associative array (['Item'])
	$QueryResult =  $query->fetchAll();
	
	/* Get All Tags Now */
	$Tags = [];
	foreach($QueryResult as $Row){
		$Tags = array_merge($Tags, explode(',', $Row['Tags']));
	}
	/* Ensure unique */
	$Tags = array_unique($Tags);
	/* Sort Naturally */
	natcasesort($Tags);
	/* Enforce typical array order (Keys not messed up)*/
	$Tags = array_values($Tags);
	return $Tags;
}

function find_tag(string $Tag, bool $Public = True) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	/* Setup a Query to search for any posts containing a tag ';Mytag2;' for example.*/
	$SQLTagQuery = "SELECT Title, Tags, PostNumber, PostTag, DateTime, Views
					FROM blogposts WHERE (CONCAT(',',Tags,',') LIKE :Needle
					";
	/* If $Public, only show Public posts */
	if($Public){
		$SQLTagQuery .= ' AND Public=1)';
	}
	else {
		$SQLTagQuery .= ')';
	}
	/* In Ascending order *//*	
	$SQLTagQuery .= ' ORDER BY PostNumber';*/
	/* Run and verify success */
	$TagStatement = $connection->prepare($SQLTagQuery);
	if(!$TagStatement->execute(['Needle' => '%,'.$Tag.',%'])){
		throw new Exception('Tag Query Failed');
	}
	/* Return an associative array */
	
	return $TagStatement->fetchAll(/*PDO::FETCH_ASSOC*/);
}

function print_blog_sidebar(){
	$Output = '<aside class="BlogBar">';
	$Output .= '<h3>Quick Links</h3>';
	
	/* Link to blogposts overview & blog home */
	$Output .= '<a href="index.php">Blog Home</a></br>';
	$Output .= '<a href="posts.php">View All Blog Posts</a></br>';
	/* Link to tags */
	$Output .= '<a href="tags.php">View Post Tags</a></br>';
	$Output .= '<a href="../rss.php">RSS Feed <img style="height:16px;" src="../images/rss-icon.png"></a></br>';
	
	/* Print 3 most recent posts */
	$Output .= '<h3>Recent Posts</h3>';
	$RecentPosts = get_posts(3);
	$Output .= '<ul>';
	foreach ($RecentPosts as $Post) {
		$Output .= '<li><a href="post.php?n='.$Post['PostTag'].'">'.$Post['Title'].'</a></li>';
	}
	$Output .= '</ul>';

	
	/* Print top 3 viewed posts */
	$Output .= '<h3>Most Viewed Posts</h3>';
	$TopPosts = get_posts(3, True, True);
	$Output .= '<ul>';
	foreach ($TopPosts as $Post) {
		$Output .= '<li><a href="post.php?n='.$Post['PostTag'].'">'.$Post['Title'].'</a></li>';
	}
	$Output .= '</ul>';
	
	$Output .= '</aside>';
	
	echo $Output;
}

