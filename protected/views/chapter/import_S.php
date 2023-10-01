<?php
	/**
	 * @var ImportSubsForm $options
	 * @var Chapter $chap
	 */

	$this->pageTitle = "Вмъкване на субтитри";
?>
<style type="text/css">
form.form-hide-errors .error span.help-block {display:none;}
</style>
<h1>Вмъкване на субтитри</h1>
<p>
	Перевод: <?=$chap->ahref; ?>
</p>
<?php
	if($chap->n_verses != 0) {
		echo "<div class='alert alert-block alert-warning'><strong>Внимание!</strong> В тази глава вече има оригинален текст. Ако вмъквате нов оригинал, то старият ще бъде изгубен заедно с преводите и коментарите!</div>";
	}

	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-import",
		"type" => "horizontal",
		"inlineErrors" => false,
		"action" => $chap->getUrl("import_subs"),
		"htmlOptions" => array(
			"class" => "form-hide-errors",
			"enctype" => "multipart/form-data",
		),
	));

	echo $form->errorSummary($options);

	echo $form->fileFieldRow($options, "src", array("hint" => "Не повече от 1 мегабайт, молим."));
	echo $form->dropDownListRow($options, "format", array("srt" => "SRT"));
	echo $form->dropDownListRow($options, "encoding", Yii::app()->params["encodings"]);
?>
<div class="form-actions">
	<button type="submit" class="btn btn-primary">
		<i class="icon-ok icon-white"></i>
		Вмъкване
	</button>
	<button type="button" class="btn btn-success" onclick="location.href='<?=$chap->book->url; ?>'">
		<i class="icon-remove icon-white"></i>
		Отмяна
	</button>
</div>
<?php
	$this->endWidget();
?>
