<?php
	/**
	 * @var Book $book
	 * @var Category[] $cats
	 */

	$this->pageTitle = $book->isNewRecord ? "Нов превод: избор на раздел" : "Раздел от каталога за " . $book->fulltitle;
?>

<style type="text/css">
	#Tree div.n {padding:1px 4px}
	#Tree div.current {background:#444; color:#fff;}
	#Tree div.current a {color:#fff;}
	#Tree div a.c {display:none;}
	#Tree div:hover a.c {display:inline;}
</style>

<script type="text/javascript">
var T = {
	init: function() {

	},
	done: false,
	s: function(id) {
		if(T.done) return false;
		T.done = true;

		$("#Tree #n" + id).addClass("current");

		$("#form-edit [name=cat_id]").val(id);
		$("#form-edit").submit();

		return false;
	}
}
</script>

<h1>Избор на раздел от каталога</h1>

<p>
	Ако не искате, можете да не избирате раздел, тогава вашият превод няма да се публикува в <a href="/catalog">каталога</a>,
	и може да бъде намерен само чрез <a href='/search'>търсещата систему</a>. Модераторите могат да променят раздела на каталога, ако го сметнат за нужно.
</p>

<ul id="Tree">
	<?php
		$branches = Yii::app()->params["catalog_branches"];
		$prev_indent = 0;
		$indent = 0;
		foreach($cats as $cat) {
			if($branches[$cat->mp[0]] != $book->typ) continue;

			$indent = count($cat->mp);

			if($indent > $prev_indent) {
				echo "\n<ul>\n";
			} else {
				echo str_repeat("</li>\n</ul>\n", $prev_indent - $indent) . "</li>\n";
			}
			echo "<li>";

			echo "<div id='n{$cat->id}' class='n" . ($book->cat_id == $cat->id ? " current" : "") . "'>";
			if($cat->available) echo "<a href='#' onclick='return T.s({$cat->id})'>";
			echo $cat->title;
			if($cat->available) echo "</a>";
			echo "</div>";

			$prev_indent = $indent;
		}
		echo str_repeat("</li>\n</ul>\n", $indent);
	?>
</ul>

<form method="post" action="<?=$book->getUrl("edit/cat"); ?>" id="form-edit">
	<input type="hidden" name="cat_id" />
</form>

<div class="form-actions">
<?php
	if($book->isNewRecord) {
		echo "<a class='btn btn-primary pull-right' href='" . $book->getUrl('edit/info') . "'>Пропускане <i class='icon-arrow-right icon-white'></i></a>";
	} else {
		echo "<a href='{$book->url}' class='btn'>Отмяна</a> ";
		echo "<button class='btn' onclick='T.s(0)'>Да не се публикова в каталога</button> ";
	}
?>

</div>
