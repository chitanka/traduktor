<div class="tools">
	<h5>Анонси относно превода</h5>
	<p>
		Анонси &mdash; това са новините за проекта.
		Анонси <?php
			$A = array(
				"g" => "пишат членовете на <a href='" . $book->getUrl("members") . "'>групата на перевода</a>",
				"m" => "пишат модераторите на превода",
				"o" => "пише собственикът на превода ({$book->owner->ahref})",
			);
			echo $A[$book->ac_announce] . ". ";

			if($book->ac_read == "a") echo "Тъй като този превод е &mdash; отворен, всичките му анонси попадат и в <a href='/announces'>общата лента на анонсите</a>.";
			else echo "Тъй като това е &mdash; затворен превод, анонсите му не попадат в <a href='/announces'>общата лента на анонсите</a>."
		?>
	</p>

	<?php if($book->can("announce")): ?>
		<a href="<?=$book->getUrl("announces/write"); ?>" class="btn btn-warning"><i class="icon-wrench icon-white"></i> Писане на анонс</a>
	<?php endif; ?>
</div>
