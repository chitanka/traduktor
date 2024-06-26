<?php
	$this->pageTitle = "Покани приятел";
?>

<style type='text/css'>
#InviteForm_email, #InviteForm_who {width:430px;}
</style>

<h1>Покани приятел</h1>

<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-invite",
		"type" => "vertical",
		"focus" => array($model, "login"),
	));

	echo $form->errorSummary($model);
	echo $form->textFieldRow($model, "email");
	echo $form->textFieldRow($model, "who");
?>

<div class="form-summary">
	<?php echo CHtml::htmlButton("<i class='icon-ok icon-white'></i> Покани", array("type" => "submit", "class" => "btn btn-primary")) . " "; ?>
</div>

<?php $this->endWidget(); ?>
