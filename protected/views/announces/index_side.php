<?php
/**
 * @var SearchFilter $filter
 */
?>
<div class="tools">
	<h5>Анонси относно преводите</h5>

	<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"method" => "get",
		"action" => "/announces",
		"id" => "form-filter",
		"type" => "vertical",
		"inlineErrors" => false,
	));
	?>
	<div class="control-group">
		<label class="control-label">От раздела на каталога:</label>
		<div class="controls">
			<select name="cat">
				<option value="0">Без значение</option>
				<?php
					$tree = CHtml::listData(Category::model()->indented_list()->findAll(), "id", "title");
					$o = array();
					echo CHtml::listOptions($filter->cat, $tree, $o);
				?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Език на оригинала:</label>
		<div class="controls">
			<select name="s_lang">
				<option value="0">Без значение</option>
				<?=Yii::app()->langs->options(Langs::FORM_INF, $filter->s_lang); ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Език на превода:</label>
		<div class="controls">
			<select name="t_lang">
				<option value="0">Без значение</option>
				<?=Yii::app()->langs->options(Langs::FORM_INF, $filter->t_lang); ?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Тема:</label>
		<div class="controls">
			<select name="topic">
				<option value="0">Без значение</option>
				<?php
					echo CHtml::listOptions($filter->topic, Yii::app()->params["blog_topics"]["announce"], $o);
				?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<label class="radio"><input type="radio" value="1" name="ready" <?=$filter->ready==1 ? "checked" : ""; ?>/>Готови на 100%</label>
			<label class="radio"><input type="radio" value="2" name="ready" <?=$filter->ready==2 ? "checked" : ""; ?>/>Не готови</label>
			<label class="radio"><input type="radio" value="0" name="ready" <?=$filter->ready==0 ? "checked" : ""; ?>/>Всякакви</label>

			<label class="checkbox"><input type="checkbox" value="1" name="gen" <?=$filter->gen ? "checked" : ""; ?>/>Достъпни за сваляне</label>
			<label class="checkbox"><input type="checkbox" value="1" name="tr" <?=$filter->tr ? "checked" : ""; ?>/>Достъпни за превод</label>
		</div>
	</div>

	<div class="control-group">
		<button type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> Покажи</button>
	</div>

	<?php $this->endWidget(); ?>

	<p>
		<a href="/announces/rss<?php if($filter->topic) echo "?topic={$filter->topic}"; ?>">RSS</a>
	</p>
</div>
