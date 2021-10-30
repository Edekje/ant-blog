<?php
require 'adminheadertemplate.php';
require 'blogfunctions.php';

/* If The following vars have been posted, process form input and make post. */
try {
	$posts = get_posts(0, False); // Get all posts, whether public or not.
} catch (Throwable $t) {
	$msg = 'Failed to get posts.';
}
?>

<h2>View Posts</h2>

<table>
<tr>
	<th>No.</th>
	<th>Tag</th>
	<th>Title</th>
	<th>Subtitle</th>
	<th>Tags</th>
	<th>DateTime</th>
	<th>Views</th>
	<th>Public</th>
</tr>
<?php if ( isset($msg) ) { echo $msg; } else { foreach ($posts as $post) { ?>
<tr>
	<td><?php echo $post['PostNumber']	 ?></td>
	<td><?php echo '<a href="preview.php?n='.$post['PostTag'].'">'.$post['PostTag'].'</a>' ?></td>
	<td><?php echo $post['Title'] ?></td>
	<td><?php echo $post['SubTitle'] ?></td>
	<td><?php echo $post['Tags'] ?></td>
	<td><?php echo $post['DateTime'] ?></td>
	<td><?php echo $post['Views'] ?></td>
	<td><?php echo ($post['Public']) ? 'True' : 'False' ?></td>
</tr> 
<?php } } ?>
</table>

<?php require 'adminfootertemplate.php'?>
