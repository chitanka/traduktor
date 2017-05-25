<?php
	/**
	 * @var integer $cache_time
	 * @var CActiveDataProvider $translations
	 * @var User $user
	 */

	Yii::app()->clientScript
		->registerScriptFile("/js/profile.js")->registerCssFile("/css/profile.css?3");

	$this->pageTitle = $user->login . ": преводи";

	$this->renderPartial("profile_head", array("user" => $user, "h1" => "преводи"));
?>
<div class="alert-block">
	За съжаление <?=$user->login . " е написал" . $user->sexy(); ?> твърде много версии на превода и не можем да ги покажем тук.
</div>
