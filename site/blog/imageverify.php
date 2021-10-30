<?php
require 'comments_user.php';

if(isset($_GET['id']) or 1) {
	$form_id = intval($_GET['id']);
	$im = make_captcha_image($form_id);
	
	header("Content-Type: image/png");
	imagepng($im);
	imagedestroy($im);
}
?>
