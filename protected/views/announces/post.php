<?php
	Yii::app()->clientScript
		->registerScriptFile("/js/jquery.scrollTo.js")
		->registerScriptFile("/js/jquery.elastic.mod.js")
		->registerScriptFile("/js/ff_comments.js?3")
		->registerScriptFile("/js/blog.js");

	$this->pageTitle = "Обявление относно превода на " . $book->fullTitle;
?>

<script type='text/javascript'>
	$(function() {
		$(".comments").ff_comments();
	});
</script>

<?php require __DIR__.'/_nav.php' ?>

<?php
	$post->title = "Обявление";
	$this->renderPartial("//blog/_post", array("post" => $post, "placement" => "post", "has" => array("bookLink" => true)));
?>

<a name="Comments"></a><h2>Коментари</h2>
<div class='comments'>
	<?php
		$prev_indent = $indent = 0;
		foreach($comments as $comment) {
			$comment->post = $post;

			$indent = count($comment->mp);
			$j = $indent - $prev_indent;

			if($j <= 0) echo str_repeat("</div>", -$j + 1);
			echo "<div class='thread'>";

			$this->renderPartial("//blog/_comment", array("comment" => $comment));

			$prev_indent = $indent;
		}
		echo str_repeat("</div>", $indent);
	?>

	<?php if(!Yii::app()->user->isGuest && $book->can("blog_c")): ?>
	<div class="thread thread-form">
		<div class="comment" id="cmt_0">
			<form method="post" class="reply" action="<?=$post->getUrl("c0/reply"); ?>">
				<div>
					<textarea name="Comment[body]"></textarea>
				</div>
				<div>
					<input type="submit" value="Добавяне на коментара" title="Или натиснете Ctrl+Enter" class="btn" />
					<input type="hidden" name="Comment[pid]" value="0" />
				</div>
			</form>
		</div>
	</div>

	<p class="cmt_0_btn" style="display:none">(<a href="#" class="re">коментиране</a>)</p>

	<?php else: ?>
	<p class="info">
		Не може да коментирате в блога на този превод.
	</p>
	<?php endif; ?>

</div>
