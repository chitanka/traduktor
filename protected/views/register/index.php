<?php
/**
 * @var User $model
 * @var Controller $this
 */
$this->pageTitle = "Регистрация";

function getAttrMinMaxLength($model, $attr, $default=[1, 32]) {
	foreach($model->rules() as $rule) {
		if($rule[0] == $attr && $rule[1] == "length") {
			return [$rule["min"], $rule["max"]];
		}
	}
	return $default;
}
?>
<style type="text/css">
.captcha {
	display:block;
	cursor:pointer;
}
</style>

<h1>Регистрация</h1>

<p>
	Колко е хубаво, че решихте да се регистрирате! След тази проста процедура ще можете да участвате в преводи, както
	да добавяте свои преводи, така и да оценявате чужди преводи, да създавате свои преводи. Вашият живот кардинално ще се промени.
</p>

<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-register",
		"type" => "horizontal",
		"inlineErrors" => false,
		"focus" => array($model, "verifyCode"),
	));
	CHtml::$afterRequiredLabel = "";
?>
<div class="control-group <?=$model->hasErrors("verifyCode") ? " error" : ""; ?>">
	<?php echo $form->labelEx($model, "verifyCode", array("class" => "control-label required", "style" => "margin-top:50px;")); ?>
	<div class="controls">
		<?php $this->widget(
			"CCaptcha",
			array(
				"clickableImage" => true,
				"showRefreshButton" => false,
				"imageOptions" => array("title" => "показване на друга картинка", "class" => "captcha"),
			)); ?>
		<?php echo $form->textField($model, "verifyCode"); ?>
		<?php echo $form->error($model, 'verifyCode'); ?>
		<p class="help-block" title="Всъщност, това е нужно, за да се убедим, че можете да четете">Защита от роботи: въведете буквите, които виждате на картинката, без значение главни или малки.</p>
	</div>
</div>
<?php
$mm = getAttrMinMaxLength($model, "login", [2, 16]);
echo $form->textFieldRow(
	$model,
	"login",
	[
		"class" => "span6",
		"hint" => "Латински букви, арабски цифри, интернационалния символ за подчертаване от {$mm[0]} до {$mm[1]} символа."
	]
);
?>
<div class="control-group <?=($model->hasErrors("pass") or $model->hasErrors("pass2")) ? " error" : ""; ?>">
	<label class="control-label required">Парола, 2 пъти:</label>
	<div class="controls">
		<?php echo $form->passwordField($model, "pass", array("class" => "span3")); ?>
		<?php echo $form->passwordField($model, "pass2", array("class" => "span3 offset5")); ?>
		<?php echo $form->error($model, "pass"); ?>
		<?php echo $form->error($model, "pass2"); ?>
		<p class="help-block">
			<?php
			$mm = getAttrMinMaxLength($model, "pass, pass2", [5, 32]);
			?>
			От <?=$mm[0]; ?> до <?=$mm[1]; ?> всякакви символи.
		</p>
	</div>
</div>
<?php
	echo $form->textFieldRow($model, "email", array("class" => "span6", "hint" => "Няма да ви изпращаме спам."));
	echo $form->radioButtonListInlineRow($model, "sex", array("m" => "мъж", "f" => "жена"), array("hint" => "За да знаем как да се обръщаме към вас."));
	echo $form->dropDownListRow($model, "lang", Yii::app()->langs->select());
	echo $form->checkBoxRow($model, "tos");
?>
<div class="form-actions">
<?php
	echo CHtml::htmlButton("<i class='icon-ok icon-white'></i> Регистрация", array("type" => "submit", "class" => "btn btn-primary")) . " ";
?>
</div>

<?php $this->endWidget(); ?>
