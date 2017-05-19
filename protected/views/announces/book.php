<?php
	/**
	 * @var integer $topic
	 * @var Book $book
	 * @var BlogPost[] $lenta
	 */
	$this->pageTitle = $this->book->fullTitle . " - блог";

	Yii::app()->getClientScript()
		->registerScriptFile("/js/jquery.scrollTo.js")
		->registerScriptFile("/js/blog.js")
		->registerScriptFile("/js/book.js?1");

	$book->registerJS();
?>

<?php require __DIR__.'/_nav.php' ?>
<h1><?=$book->fullTitle; ?> &ndash; обявления</h1>

<?php
	$posts = $lenta->getData();
	if($lenta->totalItemCount == 0) {
?>
	<div class='alert alert-info' id="info_empty">
		<?php
			echo "Все още няма обявления.";

			if($book->can("blog_w")) echo " <a href='" . $book->getUrl("announces/write") . "' class='act'>Ново обявление</a>.";
		?>
	</div>
<?php
	} else {
		echo "<div id='Lenta'>";
		foreach($posts as $post) {
			$post->book = $book;
			$this->renderPartial("//blog/_post", array("post" => $post, "placement" => "index", "has" => array("bookLink" => false)));
		}
		echo "</div>";
	}

	$this->widget('CLinkPager', array('pages' => $lenta->pagination));
?>
