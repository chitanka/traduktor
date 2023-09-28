<?php
	/**
	 * @var Book $book
	 * @var BookBanReason $reason
	 * @var Controller $this
	 */

	$this->pageTitle = "Блокиране на превода";
?>
<h1>Заблокировать перевод</h1>

<?php
	/** @var TbActiveForm $form  */
	$form = $this->beginWidget("bootstrap.widgets.TbActiveForm", ["method" => "post", "type" => "horizontal"]);
	echo $form->errorSummary($reason);
?>
<div class="control-group">
	<label class="control-label">Име на организацията</label>
	<div class="controls">
		<?php echo $form->textField($reason, "title", ["class" => "span6"]); ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">URL</label>
	<div class="controls">
		<?php echo $form->textField($reason, "url", ["class" => "span6"]); ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">E-mail</label>
	<div class="controls">
		<?php echo $form->textField($reason, "email", ["class" => "span6"]); ?>
	</div>
</div>
<div class="control-group">
	<label class="control-label">Съобщение</label>
	<div class="controls">
		<?php echo $form->textArea($reason, "message", ["class" => "span6", "rows" => 6]); ?>
	</div>
</div>
<div class="form-actions">
	<button type="submit" class="btn btn-danger"><i class="icon-ban-circle icon-white"></i> Бан</button>
	<button type="button" class="btn btn-success"><i class="icon-ok icon-white"></i> Сваляне на бана</button>
</div>
<?php $this->endWidget(); ?>
