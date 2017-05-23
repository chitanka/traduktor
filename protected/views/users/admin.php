<?php
/**
 * @var UsersController $this
 * @var User $user
 * @var RemindToken $remindToken
 * @var RegInvite[] $sentInvites
 */
Yii::app()->clientScript
	->registerScriptFile("/js/profile.js")->registerCssFile("/css/profile.css?3")
	->registerScript("profile", "Profile.uid = {$user->id};", CClientScript::POS_HEAD);

$this->pageTitle = $user->login . ": редактиране";

$this->renderPartial("profile_head", array("user" => $user, "h1" => "редактиране"));

echo CHtml::errorSummary($user, "<div class='alert alert-box alert-danger'>", "</div>");
?>
<form method="post" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">E-mail:</label>
		<div class="controls">
			<?=CHtml::activeTextField($user, "email", ["class" => "span6"]); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Пол:</label>
		<div class="controls">
			<?=CHtml::activeDropDownList($user, "sex", ["m" => "мъж", "f" => "жена", "x" => "същество", "-" => "премахнат"]); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Може да:</label>
		<div class="controls">
			<?php
			$abilities = [
				User::CAN_LOGIN    => "Да влиза",
				User::CAN_RATE     => "Да поставя оценки на преводите",
				User::CAN_COMMENT  => "Да оставя коментари в блога",
				User::CAN_PMAIL    => "Да пише писма",
				User::CAN_POST     => "Да пише постове в блога",
				User::CAN_MODERATE => "Да модерира блога",
				User::CAN_TRANSLATE    => "Да превежда",
				User::CAN_CREATE_BOOKS => "Да създава преводи",
				User::CAN_ANNOUNCE     => "Да създава анонси",
			];
			foreach($abilities as $i => $title) {
				echo "<label class='checkbox'>";
				echo CHtml::checkBox("can[]", $user->can($i), ["value" => $i]);
				echo $title . "</label>\n";
			}
			?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Покани:</label>
		<div class="controls">
			<?=CHtml::activeTextField($user, "n_invites", ["class" => "span1"]); ?>
		</div>
	</div>

	<?php if(count($sentInvites) > 0): ?>
	<div class="control-group">
		<label class="control-label">Покани за <?=$user->login; ?>:</label>
		<div class="controls">
		<?php
		foreach($sentInvites as $invite) {
			echo CHtml::textField("", $invite->getUrlAccept(), ["class" => "span6", "onclick" => '$(this).select()']);
			echo "<span class='help-block'>";
			echo $invite->cdate . " от " . $invite->sender->ahref;
			echo "</span>";
		}
		?>
		</div>
	</div>
	<?php endif ?>

	<div class="control-group">
		<label class='control-label'>Нулиране на парола:</label>
		<div class='controls'>
			<?php
			if($remindToken) {
				echo CHtml::textField("", $remindToken->url, ["class" => "span6", "onclick" => '$(this).select()']);;
			} else {
				echo "<a href='/users/{$user->id}/adminRemindToken' class='btn btn-warning'>Вземете препратка</a>";
			}
			?>
			</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Запис</button>
	</div>

</form>
