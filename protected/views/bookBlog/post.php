<?php
	Yii::app()->clientScript
		->registerScriptFile("/js/jquery.scrollTo.js")
		->registerScriptFile("/js/jquery.elastic.mod.js")
		->registerScriptFile("/js/ff_comments.js?3")
		->registerScriptFile("/js/blog.js");

	$this->pageTitle = $post->title . ": блог на превода " . $book->fullTitle;
?>

<script type='text/javascript'>
	$(function() {
		$(".comments").ff_comments();
	});
</script>

<ul class='nav nav-tabs'>
	<li><a href='<?=$book->url; ?>/'>съдържание</a></li>
	<li><a href='<?=$book->getUrl("members"); ?>'>преводачи</a></li>
	<li class='active'><a href='<?=$book->getUrl("blog"); ?>'>блог</a></li>
	<li><a href='<?=$book->getUrl("announces"); ?>'>анонси</a></li>
</ul>

<?php
	$this->renderPartial("//blog/_post", array("post" => $post, "placement" => "post", "has" => array("bookLink" => false)));
?>

<a name="Comments"></a><h2>Коментари</h2>
<div class='comments'>
	<?php
		$view = Yii::app()->user->ini["t.iface"] == 1 ? "//blog/_comment-1" : "//blog/_comment";
		$prev_indent = $indent = 0;
		foreach($comments as $comment) {
			$comment->post = $post;

			$indent = count($comment->mp);
			$j = $indent - $prev_indent;

			if($j <= 0) echo str_repeat("</div>", -$j + 1);
			echo "<div class='thread'>";

			$this->renderPartial($view, array("comment" => $comment));

			$prev_indent = $indent;
		}
		echo str_repeat("</div>", $indent);
	?>

	<?php if(!Yii::app()->user->isGuest && $book->can("blog_c")): ?>
	<div class="thread thread-form">
		<div class="comment">
			<form method="post" class="reply" action="<?=$post->getUrl("c0/reply"); ?>">
				<div>
					<textarea name="Comment[body]"></textarea>
				</div>
				<div>
					<input type="submit" value="Добавяне на коментар" title="Или нажмите Ctrl+Enter" class="btn btn-mini btn-primary" />
                    <input type="button" value="Отмяна" class="btn btn-mini cancel" />
					<input type="hidden" name="Comment[pid]" value="0" />
				</div>
			</form>
		</div>
	</div>

	<p class="cmt_0_btn"><i class="i icon-comment"></i> <a href="#" class="re ajax">Комментировать пост</a></p>

	<?php else: ?>
	<p class="info">
		Вы не можете писать комментарии в блоге этого перевода.
	</p>
	<?php endif; ?>

</div>
