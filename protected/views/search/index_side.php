<div class="tools">
	<h5>Търсене</h5>

	<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"method" => "get",
		"action" => "/search",
		"id" => "form-search",
		"type" => "vertical",
		"inlineErrors" => false,
	));
	?>
	<input type="hidden" name="t" value="<?=CHtml::encode($filter->t); ?>" />
	<div class="control-group">
		<label class="control-label">В каталога:</label>
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
		<div class="controls">
			<label class="checkbox"><input type="checkbox" value="1" name="ready" <?=$filter->ready ? "checked" : ""; ?>/>Готови на 100%</label>
			<label class="checkbox"><input type="checkbox" value="1" name="gen" <?=$filter->gen ? "checked" : ""; ?>/>Достъпни за сваляне</label>
			<label class="checkbox"><input type="checkbox" value="1" name="tr" <?=$filter->tr ? "checked" : ""; ?>/>Достъпни за превод</label>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Сортиране:</label>
		<div class="controls">
			<?php echo CHtml::dropDownList("sort", $filter->sort, SearchFilter::$sortOptions); ?>
		</div>
	</div>

	<div class="control-group">
		<button type="submit" class="btn btn-primary"><i class="icon-search icon-white"></i> Намери</button>
	</div>

	<?php $this->endWidget(); ?>

</div>

<!-- Вставьте эту строчку туда, где планируется показывать рекламный блок Яндекс.Маркета -->
<!-- script type="text/javascript">yandex_market_print()</script -->
