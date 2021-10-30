<?php
require 'comments_user.php';
if(isset($_GET['uuid'])) {
	try {
	echo confirm_comment($_GET['uuid']);
	} catch (Exception $e) {
	echo "Comment Confirmation Failure.";
	}
} else {
	echo "Invalid Link.";
}

