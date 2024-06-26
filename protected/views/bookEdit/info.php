<?php
	/**
	 * @var Book $book
	 */

	$this->pageTitle = $book->isNewRecord ? "Нов превод: свойства" : "Свойства на превода „{$book->fulltitle}“";
?>
<style type='text/css'>
	#Book_descr {height:200px;}
	#img_preview a, #img_preview img {display:block; margin:5px 0;}
</style>
<script type="text/javascript">
var E = {
	init: function() {
		$("#img_preview a").click(function() {
			var html = "<img src='" + $(this).attr("href") + "' alt='' />";
			$(this).replaceWith(html);
			return false;
		});
	},
	rm: function() {
		if(!confirm("Абсолютно ли сте сигурни, че искате да изтриете този превод?\С едно движение на мишката можете да изтриете работата на десетки хора!")) return;

		$("#form-rm").submit();
	}
};
$(E.init);
</script>

<h1>Свойства на превода</h1>

<?php
	if(!$book->isNewRecord) echo "<form id='form-rm' method='post' action='" . $book->getUrl('edit/remove') . "'><input type='hidden' name='really' value='1'/></form>";

	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-edit",
		"type" => "horizontal",
		"inlineErrors" => false,
		"htmlOptions" => array(
			"enctype" => "multipart/form-data",
		),
	));

	echo $form->errorSummary($book);

	if(!$book->isNewRecord) {
		echo "<div class='control-group'><label class='control-label'>Раздел от каталога:</label><div class='controls'>";
		echo $book->cat_id ? $book->cat->title : "Не е зададен";
		echo " &larr; <a href='" . $book->getUrl("edit/cat") . "' class='act'>Изменить</a>";
		echo "</div></div>";
	}

	echo $form->dropDownListRow($book, "s_lang", Yii::app()->langs->select());
	echo $form->textFieldRow($book, "s_title", array("class" => "span6"));
	echo $form->dropDownListRow($book, "t_lang", Yii::app()->langs->select());
	echo $form->textFieldRow($book, "t_title", array("class" => "span6"));
	echo $form->textAreaRow($book, "descr", array("class" => "span6", "hint" => "Тук могат да се използват HTML-тагове"));

?>
<div class="control-group <?=$book->hasErrors("new_img") ? " error" : ""; ?>">
	<?php echo $form->labelEx($book, "new_img", array("class" => "control-label")); ?>
	<div class="controls">
	<?php

		if(is_object($book->img) && $book->img->exists) {
			echo "<div id='img_preview'>";
			echo $book->img->tag;
			echo "<label class='checkbox'>" . $form->checkBox($book, "rm_img") . " изтриване</label>";
			echo "</div>";
		}
		echo $form->fileField($book, "new_img");
		echo $form->error($book, "new_img");
	?>
	<p class="help-block">Картинката ще бъде смалена до нужните размери автоматично.</p>
	</div>
</div>

<div class="form-actions">
<?php
	if($book->isNewRecord) {
		echo "<a class='btn btn-primary' href='" . $book->getUrl("edit/cat") . "'><i class='icon-arrow-left icon-white'></i> Назад</a> ";
		echo CHtml::htmlButton("Напред <i class='icon-arrow-right icon-white'></i>", array("type" => "submit", "class" => "btn btn-primary pull-right")) . " ";
	} else {
		echo CHtml::htmlButton("<i class='icon-ok icon-white'></i> Запис", array("type" => "submit", "class" => "btn btn-primary")) . " ";
		if(!$book->isNewRecord) echo CHtml::htmlButton("<i class='icon-ban-circle icon-white'></i> Изтриване", array("onclick" => "E.rm()", "class" => "btn btn-danger")) . " ";
		echo CHtml::htmlButton("<i class='icon-remove icon-white'></i> Отмяна", array("onclick" => "location.href='" . $book->url . "'", "class" => "btn btn-success"));
	}
?>
</div>
<?php $this->endWidget(); ?>
