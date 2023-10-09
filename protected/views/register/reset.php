<?php
/**
 * @var RegisterController $this
 * @var User $user
 */
?>
<h1>Въведете нова парола</h1>

<form method="post" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">Логин:</label>
		<div class="controls">
			<input type="text" disabled value="<?=CHtml::encode($user->login); ?>" class="span3">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Нова парола:</label>
		<div class="controls">
			<input type="password" name="pass" class="span3">
			<p class="help-block">Не по-къса от 8 символа, молим.</p>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Още веднъж:</label>
		<div class="controls">
			<input type="password" name="pass2" class="span3">
		</div>
	</div>
	<div class="form-actions">
		<button class="btn btn-default">
			<i class="icon icon-ok"></i>
			Запазване на паролата
		</button>
	</div>
</form>
