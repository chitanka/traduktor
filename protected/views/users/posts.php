<?php
/**
 * @var CActiveDataProvider $posts
 * @var User $user
 */

Yii::app()->clientScript
	->registerScriptFile("/js/profile.js")->registerCssFile("/css/profile.css?3")
	->registerScriptFile("/js/blog.js");

$this->pageTitle = $user->login . ": съобщения";

$this->renderPartial("profile_head", array("user" => $user, "h1" => "съобщения"));
?>

<?php if($posts->totalItemCount == 0): ?>

<p>
	<?=$user->login; ?> не е написал<?=$user->sexy(); ?> нито едно съобщение в блоговете.
</p>

<?php else: ?>

<h2><?=Yii::t("app", "{n} съобщение|{n} съобщения|{n} съобщения", $posts->totalItemCount); ?></h2>
<?php
	if(isset($cache_time)) {
		echo "<div class='alert alert-box alert-info'>Информацията се обновява веднъж на <strong>" . Yii::t("app", "{n} час|{n} часа|{n} часа", $cache_time) . "</strong></div>";
	}

	$data = $posts->data;

	$this->widget('bootstrap.widgets.TbPager', array("pages" => $posts->pagination, "header" => "<div class='pagination' style='margin-bottom:0'>"));

	foreach($posts->data as $post) {
		$post->author = $user;
		if($post->book_id != 0 && !$post->book->can("blog_r")) {
			echo "<p class='access-denied'>Съобщението е написано в блога за превод, до който нямате достъп.</p>";
		} elseif ($post->book_id == 0 && !isset(Yii::app()->params["blog_topics"]["common"][$post->topics])) {
			echo "<p class='access-denied'>Съобщението е написано в блог, до който нямате достъп.</p>";
		} else {
			$this->renderPartial("//blog/_post", array("post" => $post, "placement" => "user", "has" => array("edit" => false)));
		}
	}

	$this->widget('bootstrap.widgets.TbPager', array("pages" => $posts->pagination));
	?>

<?php endif ?>
