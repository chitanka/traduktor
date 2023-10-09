<?php
	/**
	 * @var Book $book
	 * @var Chapter[]  $chapters
	 */

	$this->pageTitle = $book->fullTitle;

	Yii::app()->clientScript
		->registerScriptFile("/js/jquery.scrollTo.js")
		->registerScriptFile("/js/book.js?1");

	if($book->can("chap_edit")) {
		Yii::app()->clientScript->registerScriptFile("/js/CE.js?2")
		->registerScriptFile("/js/Sortable.min.js");
	}

	$book->registerJS();
?>
<style type='text/css'>
#Info img {float:left; margin:0 20px 20px 0;}
#Info .cat span {display:none;}
#Info .cat:hover span {display:inline;}
#Chapters { width:100%; position:relative; z-index:2; }
#Chapters td.r {text-align:right;}
#Chapters .disabled {text-decoration: line-through; color:#aaa;}
#Chapters td.editing {border-top:10px solid black; border-bottom:10px solid black; background:#fff; margin-right:-10px;}
#Chapters td.loading {
	padding:50px;
	color:#444;
	background:#d0d0d0 url("/i/pacman-d0d0d0.gif") no-repeat center center;
}
#Chapters td, #Chapters th {white-space: nowrap; background:#fff;}
#Chapters td.t {white-space: normal;}
#Chapters .editing .b select {
	height:24px;
	font-size:14px;
}
#Status_Dropdown {
	position:absolute;
	width:150px;
	border:1px solid black;
	background:white;
	padding:5px 15px;
	white-space:normal;
	z-index:7;
}
#Status_Dropdown ul {
	margin:10px 0; padding:0;
}
#Status_Dropdown ul li {
	list-style:none;
	margin:5px 0;
}
</style>

<ul class='nav nav-tabs'>
	<li class='active'><a href='<?=$book->url; ?>/'>съдържание</a></li>
	<li><a href='<?=$book->getUrl("members"); ?>'>преводачи</a></li>
	<li><a href='<?=$book->getUrl("blog"); ?>'>блог</a></li>
	<li><a href='<?=$book->getUrl("announces"); ?>'>анонси</a></li>
</ul>

<h1><?=$book->fullTitle; ?></h1>
<?php
	if($book->opts_get(Book::OPTS_BAN_COPYRIGHT)) {
		echo "<div class='alert alert-box alert-danger'>Внимание! Този превод е блокиран по сигнал на правообладателя, затова е достъпен само в групата на преводачите. Всичко, което виждате тук, трябва да остане тайна.</div>";
	}
?>
<div id='Info'>
	<?php
		if($book->img->exists) echo $book->img->tag;

		if($book->ac_read == 'o') {
			echo "<div class='alert alert-block'><h4 class='alert-heading'>Внимание!</h4>Само вие виждате този превод. Можете да промените достъпа към него в <a href='" . $book->getUrl("edit/access") . "'>редактора за права за достъп</a>.</div>";
		}

		if($book->cat_id || $book->can("book_edit")) {
			echo "<p class='cat'>";
			if($book->cat_id) echo "<a href='{$book->cat->url}'>{$book->cat->pathHtml}</a>";
			if($book->can("book_edit") || Yii::app()->user->can("cat_moderate")) echo "<span> &larr; <a href='" . $book->getUrl("edit/cat") . "' class='act'>указване на раздел от каталога</a></span>";
			echo "</p>";
		}

		if($book->descr != '') echo "<p>" . Yii::app()->parser->out($book->descr) . "</p>";
	?>

</div>
<div class="clear"></div>

<?php
	echo "<h2 style='clear:both'>Съдържание:</h2>";

	if($book->can("chap_edit")) {
		Yii::app()->bootstrap->registerDropdown();

		echo "<div class='btn-toolbar' id='moderator-toolbar'>";

		// добавить главу
		echo "<div class='btn-group'>";
		echo "<a href='#' onclick='return CE.add(" . (count($chapters) == 0 ? 0 : 1) . ")' class='btn btn-small'><i class='icon-plus'></i> Добавяне на глава</a>";
		if(count($chapters) > 0) {
			echo "<a href='#' class='btn btn-small dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></a>";
			echo "<ul class='dropdown-menu'>";
			echo "<li><a href='#' onclick='return CE.add(1)'>в края</a></li>";
			echo "<li><a href='#' onclick='return CE.add(-1)'>в началото</a></li>";
			echo "</ul>";
		}
		echo "</div>";

		// Tools
		$Tools = array();
		if(count($chapters) > 0) {
			$Tools[] = array("#", "<i class='icon-random'></i> Промяна на реда на главите", "onclick" => "return CE.reorder()");
		}
		$Tools[] = array($book->getUrl("recalc"), "<i class='icon-refresh'></i> Преизчисляване на статистиката на превода");
		if($book->can("dict_edit")) $Tools[] = array($book->getUrl("dict_copy"), "<i class='icon-book'></i> Копиране на речника на друг превод");
		if(count($Tools) > 0) {
			echo "<div class='btn-group'>";
			echo "<a href='javascript:void()' class='btn btn-small'><i class='icon-wrench'></i></a>";
			echo "<a href='#' class='btn btn-small dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></a>";
			echo "<ul class='dropdown-menu'>";
			foreach($Tools as $t) {
				echo "<li>";
				echo CHtml::link($t[1], $t[0], $t);
				echo "</li>";
			}
			echo "</ul>";
			echo "</div>";
		}

		// только владельцу: редактировать свойства перевода
		if($book->can("book_edit")) {
			echo "<div class='btn-group'>";
			echo "<a href='" . $book->getUrl("edit") . "' class='btn btn-small'><i class='icon-cog'></i> Свойства на превода</a>";
			echo "</div>";
		}

		echo "</div>";
	}
?>



<?php if(count($chapters) == 0): ?>
	<div class='alert alert-info' id="info_empty">
		Все още няма добавени глави.
	</div>
	<table class="items" id="Chapters"></table>
<?php else: ?>
<table class="table table-condensed table-striped" id="Chapters">
<thead><tr>
	<?php if($book->typ == "S") echo "<td></td>"; ?>
	<th class='t'>Заглавие</th>
	<th title='Променя се от <?php echo $book->ac_chap_edit == "m" ? "модераторите" : "собственика"; ?>.' style='cursor:help;'>Статус</th>
	<th title='Кога е била добавена за последно, изтрита или редактирана последната версия на превода или се е променил оригиналният текст.' style='cursor:help;'>Активност</th>
	<th title='Преведени части / общо. Поставете курсора на мишката, за да разберете Коефициента на Плурализъм, средно количество варианти на тази част от превода.' style='cursor:help; text-align:center;' colspan='2'>Готово</th>
	<?php if($book->can("chap_edit")) echo "<th class='e'></th>"; ?>
</tr></thead>
<tbody>
<?php
	$AC = array("ac_read", "ac_trread", "ac_gen", "ac_rate", "ac_comment", "ac_tr");
	foreach($chapters as $chap) {
		echo "<tr id='c_{$chap->id}' data-id='{$chap->id}'>";

		if($book->typ == "S") {
			echo "<td>";
			if($chap->n_verses > 0) echo "<a href='" . $chap->getUrl("orig") . "' title='Сваляне на оригинала'>&laquo;&laquo;&laquo;</a>";
			echo "</td>";
		}

		echo "<td class='t'>";
			echo $chap->ahref;
//			if($chap->status) echo " &mdash; " . Yii::app()->params["translation_statuses"][$chap->status];
			foreach($AC as $k) {
				if($chap->$k != "") echo "&nbsp;<i class='{$k} {$chap->$k}'></i>";
			}
		echo "</td>";

		echo "<td>";
		if($chap->status) echo Yii::app()->params["translation_statuses_short"][$chap->status];
		echo "</td>";

		if($chap->n_verses == 0) {
			echo "<td colspan='3'>";
			if($book->can("chap_edit")) echo "<i class='icon-upload'></i> <a href='" . $chap->getUrl("import") . "'>вмъкване на оригинала</a>";
			else echo "(празно)";
			echo "</td>";
		} else {
			echo "<td>";
			echo "<span title='" . Yii::app()->dateFormatter->formatDateTime($chap->last_tr, "medium", "short") . "'>" . $chap->idle_time_text() . "</span>";
			echo "</td>";

			if($chap->d_vars > 0) $title = sprintf(" title='КП=%.01f'", $chap->n_vars / $chap->d_vars);
			else $title = "";
			echo "<td{$title} class='r'>{$chap->ready} <small>({$chap->d_vars} / {$chap->n_verses})</small></td>";

			echo "<td>";
			if($chap->n_vars > 0) {
				$verb = $book->typ == "S" ? "сваляне" : "прочит";
				$tdl = " ";
				if($chap->n_dl > 0) {
					$tdl .= "Брой сваляния: {$chap->n_dl}";
					if($chap->n_dl_today > 0) $tdl .= " (днес &ndash; {$chap->n_dl_today})";
				}
				if($chap->can("gen")) {
					echo "<a href='" . $chap->getUrl("ready") . "' class='act' title='Получаване на готовия превод.{$tdl}'>{$verb}</a>";
				} else {
					echo "<span class='disabled' title='Нямате право да сваляте превода на тази глава.{$tdl}'>{$verb}</span>";
				}
			}
			echo "</td>";
		}

		if($book->can("chap_edit")) {
			echo "<td><a href='" . $chap->getUrl("edit") . "' onclick='return CE.ed({$chap->id})' title='Редактиране'><i class='icon-pencil'></i></a></td>";
		}

		echo "</tr>";
	}
?>
</tbody>
</table>

<?php endif; ?>
