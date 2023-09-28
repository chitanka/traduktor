<?php
	/**
	 * @var integer $topic
	 * @var Book $book
	 */
?>
<div class='tools'>
<h5>Блог на превода</h5>

<b>Рубрики:</b>
<ul class="nav nav-list">
<?php
	foreach(Yii::app()->params["blog_topics"]["book"] as $k => $v) {
		echo "<li" . ($k == $topic ? " class='active'" : "") . ">";
		echo "<a href='" . $book->getUrl("blog") . "?topic={$k}'>{$v}</a>";
		echo "</li>";
	}
	echo "<li" . ($topic == 0 ? " class='active'" : "") . "><a href='" . $book->getUrl("blog") . "'>всичко</a></li>";
	if($book->can("blog_w")) echo "<li><a href='" . $book->getUrl("blog/edit" . ($topic ? "?topic={$topic}" : "")) . "'><i class='icon icon-pencil'></i> писане на пост</a></li>";
?>
</ul>
</div>

