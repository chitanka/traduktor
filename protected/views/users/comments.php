<?php
	/**
	* @var integer $cache_time
	* @var string $mode ("blog", "tblog", "tr")
	* @var CActiveDataProvider $comments
	* @var User $user
	*/

	Yii::app()->clientScript
		->registerScriptFile("/js/profile.js")->registerCssFile("/css/profile.css?3");

	$this->pageTitle = $user->login . ": комментари";

	$this->renderPartial("profile_head", array("user" => $user, "h1" => "комментари"));
?>

<style type="text/css">
.comments {margin-left:0;}
.comments .comment {margin-bottom: 10px;}
</style>

<?php
$this->widget('bootstrap.widgets.TbMenu', array(
	'type' => 'pills', // '', 'tabs', 'pills' (or 'list')
	'stacked' => false, // whether this is a stacked menu
	'items'=>array(
		array('label'=>'В общия блог',      'url'=>'?mode=blog',  'active' => $mode == "blog"),
		array('label'=>'В блоговете на преводи', 'url'=>'?mode=tblog', "active" => $mode == "tblog"),
		array('label'=>'В преводи',        'url'=>'?mode=tr',    "active" => $mode == "tr"),
	),
));
?>

<?php
	if($comments->totalItemCount == 0) {
		$A = array("blog" => "в общия блог", "tblog" => "в блоговете на общодостъпните преводи", "tr" => "в общодостъпните преводи");
		echo "<p>{$user->login} не е написал" . $user->sexy() . " нито един коментар {$A[$mode]}</p>";
	} else {
		echo "<h2>" . Yii::t("app", "{n} коментар|{n} коментара|{n} коментара", $comments->totalItemCount) . "</h2>";
		if($cache_time) {
			echo "<div class='alert alert-box alert-info'>Информацията се обновява веднж на <strong>" . Yii::t("app", "{n} час|{n} часа|{n} часа", $cache_time) . "</strong></div>";
		}

		$data = $comments->data;

		$this->widget('bootstrap.widgets.TbPager', array("pages" => $comments->pagination));

		echo "<div class='comments'>";
		$view = Yii::app()->user->ini["t.iface"] == 1 ? "//blog/_comment-1" : "//blog/_comment";
		foreach($comments->data as $comment) {
			$x = "";

			if($mode == "blog") {
				if(!isset(Yii::app()->params["blog_topics"]["common"][$comment->post->topics])) {
					echo "<p class='access-denied'>Комментарът е написан в блог, до който нямате достъп.</p>";
					continue;
				}
				$x .= "<a href='{$comment->post->url}#cmt_{$comment->id}'>{$comment->post->title}</a>";
			} elseif($mode == "tblog") {
				$x .= "{$comment->post->book->ahref} - <a href='{$comment->post->url}#cmt_{$comment->id}'>{$comment->post->title}</a>";
			} elseif($mode == "tr") {
				$x .= "<a href='{$comment->orig->url}'>{$comment->orig->chap->book->fullTitle}</a>";
			}

			$this->renderPartial($view, [
					"comment" => $comment, "meta_extra" => $x,
					"disable_dot" => true, "disable_reply" => true,
					"disable_delete" => true, "disable_up" => true,
					"disable_rater" => true,
				]
			);
		}
		echo "</div>";

		$this->widget('bootstrap.widgets.TbPager', array("pages" => $comments->pagination));
	}
?>
