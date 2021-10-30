<?php
$title = 'Blog - Tags';
require 'headertemplate.php';
require 'blogfunctions.php';

$Tags = get_tags();
print_blog_sidebar();
?>

<h2>Blog Tags:</h2>

<?php foreach($Tags as $Tag) {?><a href="#<?php echo $Tag ?>" style="color: blue;"><?php echo $Tag ?></a> <?php } ?>
<br>
<br>
<?php foreach ($Tags as $Tag) {
$Posts = find_tag($Tag); ?>
<h3 id="<?php echo $Tag ?>">Tag: <?php echo $Tag ?></h3>
<table style="width: 500px;">
<!--<tr><th>Title</th> <th>Date</th></tr> -->
<?php foreach($Posts as $Post) { ?>
<tr> <td style=""><a href="post.php?n=<?php echo $Post['PostTag'] ?>"><?php echo $Post['Title'] ?></a></td> <td style="width : 110px;"><?php echo substr($Post['DateTime'], 0, 10) ?></td> </tr>
<?php } ?>
</table>
<br>
<?php } ?>
<?php require 'footertemplate.php'?>
