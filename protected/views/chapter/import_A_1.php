<?php
	/**
	 * @var TextSource $options
	 * @var Chapter $chap
	 */

	$this->pageTitle = "Вмъкване на текст в превода {$chap->book->fullTitle}";

	Yii::app()->bootstrap->registerTabs();
?>
<style type="text/css">
	#TextSource_text {height:200px;}
	form.form-hide-errors .error span.help-block {display:none;}
</style>
<script type="text/javascript">
var I = {
	src_type: function(type) {
		$("#form-prepare-text [name=TextSource\\[src_type\\]]").val(type);
		return false;
	}
}
</script>
<h1>Импортиране на текст</h1>
<p>
	Перевод: <?=$chap->book->ahref; ?>, <?=$chap->ahref; ?>
</p>
<?php
	if($chap->n_verses != 0) {
		echo "<div class='alert alert-block alert-warning'><strong>Внимание!</strong> В тази глава вече има оригинален текст. Ако вмъквате нов материал, старият текст ще бъде унищожен заедно с коментарите!</div>";
	}
?>
<!-- form method='post' id='form-prepare-text' class="form-inline" action="<?=$chap->getUrl("import"); ?>" enctype="multipart/form-data" -->
<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-prepare-text",
		"action" => $chap->getUrl("import"),
		"type" => "horizontal",
		"inlineErrors" => false,
		"htmlOptions" => array(
			"class" => "form-hide-errors",
			"enctype" => "multipart/form-data",
		),
	));
?>
	<input type='hidden' name='TextSource[src_type]' value='1' />

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li <?=$options->src_type == 1 ? "class='active'" : ""; ?>><a href="#1" data-toggle="tab" onclick='return I.src_type(1)'>Слагане или писане</a></li>
			<li <?=$options->src_type == 2 ? "class='active'" : ""; ?>><a href="#2" data-toggle="tab" onclick='return I.src_type(2)'>От файл</a></li>
<?php if(0) : // NOT IMPLEMENTED, TODO Импорт оригинала из HTML ?>
	<li <?=$options->src_type == 3 ? "class='active'" : ""; ?>><a href="#3" data-toggle="tab" onclick='return I.src_type(3)'>От интернет</a></li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="1" class="tab-pane <?=$options->src_type == 1 ? "active" : ""; ?>">
				<div class="control-group">
					<textarea name='TextSource[text]' class='span8' id='TextSource_text'></textarea>
					<p class='help-block'>Молим, не повече от 500 килобайта. Разбийте на отделни глави по-големите текстове.</p>
				</div>
			</div>
			<div id="2" class="tab-pane <?=$options->src_type == 2 ? "active" : ""; ?>">
				<?php echo $form->fileFieldRow($options, "file", array("hint" => "Молим, само файлове .TXT не по-големи от 500 килобайта. Разбийте на глави по-големите текстове.")); ?>
				<?php echo $form->dropDownListRow($options, "encoding", Yii::app()->params["encodings"]); ?>
			</div>
<?php if(0) : // NOT IMPLEMENTED, TODO Импорт оригинала из HTML ?>
			<div id="3" class="tab-pane <?=$options->src_type == 3 ? "active" : ""; ?>">
				<?php echo $form->textFieldRow($options, "url", array("placeholder" => "http://", "class" => "span7", "hint" => "От страницата ще бъдат качени не повече от 500 КБ. Ако искате да преведете по-дълга страница, свалете я, отворете я в браузъра и копирайте по-къси парчета.")); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
		echo $form->dropDownListRow($options, "chopper", $options->choppers);
	?>

	<div class="form-actions">
		<button type="button" class="btn btn-success" onclick="location.href='<?=$chap->book->url; ?>'">
			<i class="icon-remove icon-white"></i>
			Отмяна
		</button>
		<button type="submit" class="btn btn-primary pull-right">
			Нататък
			<i class="icon-white icon-arrow-right"></i>
		</button>
	</div>
<?php $this->endWidget(); ?>
