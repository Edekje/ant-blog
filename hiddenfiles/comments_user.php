<?php
/* Database login with read-write permissions,
 * giving $host, $dbname, $username, $password variables. */
include 'sql_users/blogdb_user.php';

function write_comment(string $email, string $name, string $text, int $form_id, int $captcha_code) {
 	/* This function receives the email, name and commen text from a comment form.
	 * The form will also have a unique form_id which is generated for the user together with a captcha code
	 * In this way, a user being a human may be verified.
	 * This function checks the captcha, verifies the email, name, text validities, saves the comment, sends a verification email.
	 * This function should never fail, but return a status string.
	 * Only problem with this function is non-ASCII friendliness*/
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	/* Check the captcha, and retrieve the postnumber */
	$PostNumber = check_captcha($form_id, $captcha_code);
	if($PostNumber == 0) { // In this case the verification code was wrong.
		return 'Please enter a valid verification code.';
	} else if($PostNumber < 0) { // In this case, the form_id did not even exist: either it timed out, or this is likely malicious.
		return $PostNumber.'Please resend comment: Form timed out or is invalid.';
	}
	
	/* Check appropriate lengths */
	if(strlen($email) < 5 or strlen($email) > 100) {
		return 'Please enter an e-mail of length between 5 and 100 characters.';
	}
	if(strlen($name) < 3 or strlen($name) > 50) {
		return 'Please enter a name of length between 3 and 50 characters.';
	}
	if(strlen($text) < 8 or strlen($text) > 2000) {
		return 'Please enter a comment of length between 8 and 2000 characters.';
	}
	
	/* Verify that name only contains letters & spaces & hyphens & numbers */
	if (preg_match('/[^A-Za-z0-9- ]/', $name)) {
		return 'Please enter a name containing only English letters, numbers, hyphen and space.';
	}
	/* Verify validity of email (should we add idn to ascii??? conversion) */
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return 'Please enter a valid e-mail address.';
	}
	/* Also check domain of email ? */
	$tmp = explode("@",$email);
	$emaildomain = array_pop( $tmp );
	if(!checkdnsrr(idn_to_ascii($emaildomain),"MX")) {
		return 'Please enter an e-mail address with a valid domain.';
	}
	
	/* Here, we explicitly take the design decision to store comments unencoded, and encode them for HTML use after retrieval */
	/*$Converted_text = htmlspecialchars($text);*/
	
	/* Make comment with email, name, text, a random comment id, postnumber, time and date, public and confirmed to 0. */
	$WriteCommentSQL = 'INSERT INTO comments
	(PostNumber, Email, Name, CommentText) VALUES (:PostNumber, :Email, :Name, :CommentText); ';
	/* Also add a UUID pair in the comments_uuids table for email confirmation */
	$WriteCommentSQL .= 'INSERT INTO comments_uuids (Comment_ID, UUID) VALUES (LAST_INSERT_ID(), UUID()); ';
	/* And return the UUID for useL */
	$WriteCommentSQL .= 'SELECT UUID FROM comments_uuids WHERE Comment_ID=LAST_INSERT_ID();';
	$SQLInsertComment = $connection->prepare($WriteCommentSQL);
	$BindingValues = ['PostNumber' => $PostNumber, 'Email' => $email, 'Name' => $name,'CommentText' => $text];
	/* Run and Filter out False errors */

	if(!$SQLInsertComment->execute($BindingValues)){
		return 'Comment failed.';
	}
	/* Check it was successful and get the corresponding CommentID. */
	$SQLInsertComment->nextRowset();
	$SQLInsertComment->nextRowset();
	$SQLResult = $SQLInsertComment->fetch(PDO::FETCH_ASSOC);
	if(!$SQLResult){
		return 'Comment failed: nothing changed.';      // This case should be tested / re-examined
	}
	/* Now we assume success */
	$CommentUUID = $SQLResult['UUID']; // doesn't work for UUID
	
	/* Needs work to send verication email*/
	$Confirmation_Link = 'https://ethanvanwoerkom.com/sandbox/site/blog/confirm_comment.php?uuid='.$CommentUUID;
	$mail_status = send_verification_mail($email, $Confirmation_Link, $name);
	/* Success */
	if($mail_status) {
		return 'Comment made successfully; please confirm the validation e-mail sent to you within 7 days.';
	} else { /* Failure sending email */
		return 'Comment failed: contact administrator.';
	}
}

function start_email_validation(int $CommentID) {
	return; // Still needs to be written.
}

function generate_captcha(int $PostNumber) {
	// Saves a form_id 9 digit integer to database, saves its 4 digit captcha code to database, as well as the postid.
	// The database captchas older than 6h are deleted.
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$captcha_code = mt_rand(1000, 9999);
	$captcha_seed = mt_rand(); // This will be used to seed the captcha generated image with the same value each time for consistency.
	$SQL_Gen_Cap = 'INSERT INTO form_captchas (FormID, CaptchaCode, CaptchaSeed, PostNumber) VALUES (:FormID, :CaptchaCode, :CaptchaSeed, :PostNumber);';
	$SQLGenerate = $connection->prepare($SQL_Gen_Cap);
	// Try to save in the database twice in case the FormID is taken.
	for($i = 0; $i < 2; $i++){
		$form_id = random_int(1, 999999999);
		$BindingValues = ['FormID' => $form_id, 'CaptchaCode' => $captcha_code, 'CaptchaSeed' => $captcha_seed, 'PostNumber' => $PostNumber];
		if($SQLGenerate->execute($BindingValues)) { // Assuming this is enough for successful insertion. It is definitely not.
			break;
		}
		/*if($SQLGenerate->rowCount() != 0) { <---- Assuming this check is unnecessary.
			break;
		} else */
		if($i == 1) { // Throw alarm if this fails twice.
			print_r( $SQLGenerate->errorInfo());
			error_log('Alarm: generate_captcha($PostNumber) failed to create a random FormID.');
			return -1; // Return -1 on fail.
		}
	}
	
	return $form_id; // No message if success, return -1 on fail.
}

function check_captcha(int $form_id, int $captcha_code) {
	// Returns $PostNumber on success, 0 if captcha incorrect, -1 if form_id does not exist (timed out or evil)
	// Checks captcha and deletes it immediately.
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	//$SQL_Check_Cap = 'SELECT * FROM form_captchas WHERE FormID=:FormID;';/* this version does not execute a delete */
	$SQL_Check_Cap = 'DELETE FROM form_captchas WHERE FormID=:FormID RETURNING CaptchaCode, PostNumber;'; // Finds/retrieves & deletes captcha
	$SQLGenerate = $connection->prepare($SQL_Check_Cap);
	$SQLGenerate->execute(['FormID' => $form_id]);
	$Output = $SQLGenerate->fetch(PDO::FETCH_ASSOC);
	
	if($Output == 0) { // If the fetch failed/there was no such entry, return -1.
		return -1;
	}
	if($Output['CaptchaCode'] != $captcha_code) { // If the Captcha was wrong, return 0. (Still need to delete Captcha)
		return 0;
	}
	$PostNumber = $Output['PostNumber'];
	
	return $PostNumber; 
}

function make_comment_form(int $PostNumber, $emailval = '', $nameval = '', $textval = '') {
	$form_id = generate_captcha($PostNumber);
	if($form_id <= 0) {
	return ''; // Failed to Generate comment form.
	}
	$Html  = '<form method="Post" id="CommentForm" action="#CommentForm"><input type="hidden" name="FormID" value="'.$form_id.'">';
	$Html .= '<label for="Name">Name:</label></br><input type="text" name="Name" value="'.$nameval.'"></br>';
	$Html .= '<label for="E-mail">E-Mail:</label></br><input type="text" name="E-mail" value="'.$emailval.'"></br>';
	$Html .= '<label for="CommentText">Comment:</label></br><textarea name="CommentText" style="padding: 5px 5px; width: 200px; height: 100px;">'.$textval.'</textarea></br>';
	$Html .= '<label for="HumVer">Please copy number to confirm your humanity:</label></br>';
	$Html .= '<img src="imageverify.php?id='.$form_id.'"></br>';
	$Html .= '<input type="text" name="HumVer"></br>';
	$Html .= '<input type="submit" value="Submit Comment">';

	return $Html;
}

function make_captcha_image(int $form_id) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$SQL_Get_Code = 'SELECT * FROM form_captchas WHERE FormID=:FormID;';
	$SQLGet = $connection->prepare($SQL_Get_Code);
	$SQLGet->execute(['FormID' => $form_id]);
	$Output = $SQLGet->fetch(PDO::FETCH_ASSOC);
	
	if($Output == 0) { // If the fetch failed/there was no such entry, return -1.
		return -1;
	}
	
	// Seed the random number generator with a pre-stored int which was generated
	// at the moment of creation of the form captcha, so that it always produces the same image
	// for the same captcha.
	mt_srand($Output['CaptchaSeed']);
	
	$captcha_code = $Output['CaptchaCode'];
	$im = imagecreatetruecolor(120, 50); // Make Image
	
	$background_colour = imagecolorallocate($im, 155, 155, 155); // Make gray colour
	imagefilledrectangle($im, 0, 0, 120, 50, $background_colour); // Set background to gray
	
	$font_colour = imagecolorallocate($im, 255, 255, 255); // Make white colour
	//imagestring($im, 5, mt_rand(0, 70)+5, mt_rand(0, 30)+5, strval($captcha_code), $font_colour);
	//imagettftext($im, 15, 0, mt_rand(0,70)+5, mt_rand(0,30)+5, $font_colour, '/usr/share/fonts/open-sans/OpenSans-Italic.ttf', strval($captcha_code));
	imagettftext($im, 15, 0, mt_rand(0,70), mt_rand(0,30)+20, $font_colour, '/usr/share/fonts/liberation-mono/LiberationMono-Regular.ttf', strval($captcha_code));
	
	$line_colour = imagecolorallocate($im, 230, 230, 230); // Make near white colour
	for($i = 0; $i < 6; $i++) { // Generate 6 random lines overlaid onto background.
		imageline($im, 0, mt_rand(-5, 55), 150, mt_rand(-5, 55), $line_colour);
	}
	
	$dot_color = imagecolorallocate($im, 0, 0, 255); // Make blue colour
	for($i = 0; $i < 400; $i++) { // Generate 400 random dots overlaid onto background.
		imagesetpixel($im, mt_rand(0, 119), mt_rand(0, 49), $dot_color);
	}
	return $im; 
}

function send_verification_mail($To, $Link, $Name) {
	$TITLE = 'Please confirm your comment on EthanvanWoerkom.com';

	// To send HTML mail, the Content-type header must be set:
	$HEADERS  = "MIME-Version: 1.0 \r\n";
	$HEADERS .= "Content-type: text/html; charset=iso-8859-1 \r\n";

	// Additional headers
	$HEADERS .= "From: Ethan van Woerkom's Blog <server@ethanvanwoerkom.com> \r\n";

	// Do we need to check htmlspecialchars, wrong charset utf8 etc. ?
	$MSG  = '<html><body>';
	$MSG .= '<p>Dear '.$Name.',</p><p>Thank you for your comment on EthanvanWoerkom.com.';
	$MSG .= '<br>Please click or paste the following link into your address bar to confirm your comment:</p>';
	$MSG .= '<p><a href="'.$Link.'">'.$Link.'</a></p>';
	$MSG .= '</body><html>';

	$status = mail($To,$TITLE,$MSG,$HEADERS);
	
	return $status;
}

function confirm_comment($UUID) {
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	$SQL_Verify_Comment = 'DELETE FROM comments_uuids WHERE UUID=:UUID RETURNING Comment_ID;'; // Finds Comment_ID & deletes UUID relation.
	$SQLVerify = $connection->prepare($SQL_Verify_Comment);
	$SQLVerify->execute(['UUID' => $UUID]);
	$Output = $SQLVerify->fetch(PDO::FETCH_ASSOC);
	
	if(!$Output) { // If the fetch failed/there was no such entry, return -1.
		return "Invalid Comment Confirmation Link. Have 7 days past since your comment? Then it has been deleted.";
	}
	$Comment_ID = $Output['Comment_ID'];
	
	$SQL_Confirm_Comment = "UPDATE comments SET Confirmed=1 WHERE Comment_ID=:Comment_ID;";
	$SQLConfirm = $connection->prepare($SQL_Confirm_Comment);
	if(!$SQLConfirm->execute(['Comment_ID' => $Comment_ID])) {
		return "Failed to Confirm Comment.";
	}
	
	return "Comment Confirmed. Once the administrator approves your comment, it will be displayed online."; 
}

function list_comments($Confirmed, $Public, $Count=false, $PostNumber=0) {
	# List all comments with corresponding Public / Confirmed status
	# List in oldest first order.
	# If $Count=true just give the number of comments for this post.
	# If $PostNumber is non-zero, only do this for a specific post.
	global $host, $dbname, $username, $password;
	$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	
	# Either count or get comments.
	if($Count) {
		$SelectCommentsSQL = 'SELECT count(*)';
	} else {
		$SelectCommentsSQL = 'SELECT *';
	}
	$SelectCommentsSQL .= ' FROM comments WHERE Confirmed=:Confirmed AND Public=:Public';
	# If necessary, specify to post.
	if($PostNumber) {
		$SelectCommentsSQL .= ' AND PostNumber=:PostNumber';
	}
	# Order by old-to-new
	$SelectCommentsSQL .= ' ORDER BY DateTime ASC';
	
	# Prepare
	$SQL_Query = $connection->prepare($SelectCommentsSQL);
	# Execute
	$Inputs = ['Confirmed' => $Confirmed, 'Public' => $Public];
	if($PostNumber) {
		$Inputs['PostNumber'] = $PostNumber;
	}
	$SQL_Query->execute($Inputs);
	# Error check
	if(!$SQL_Query) {
		throw new Exception('SQL Query Failed: List Comments');
	}
	
	if($Count) { # Return an int of post count.
		return $SQL_Query->fetchColumn();
	} else { # Return a list of rows
		return $SQL_Query->fetchAll();
	}
}

function generate_comment_html($Comment) {
	# Generates the HTML code for a comment to be printed to user.
	$HTML  = '<div class="Comment"><div class="CommentTitle">';
	$HTML .= '<span class="CommentAuthor">'.htmlspecialchars($Comment['Name']).'</span> wrote on <span class="CommentDateTime">'.$Comment['DateTime'].'</span>:</div>';
	$HTML .= '<div class="CommentText">'.htmlspecialchars($Comment['CommentText']).'</div></div>';
	return $HTML;
}
