<div class='tools'>
<h5>Резюме</h5>
<dl class='info'>
	<dt>Пол:</dt>
	<dd><?=Yii::app()->params["sex"][$user->sex]; ?></dd>

	<dt>Роден език:</dt>
	<dd><?=Yii::app()->langs->inf($user->lang); ?></dd>

	<dt>С нас:</dt>
	<dd><?php
		echo "с " . Yii::app()->dateFormatter->formatDateTime($user->cdate, "long", "");
		$d1 = new DateTime();
		$siteAge = $d1->diff(date_create($user->cdate));
		if($siteAge->days > 0) {
			echo " (" . Yii::t("app", "{n}&nbsp;ден|{n}&nbsp;дни|{n}&nbsp;дни", $siteAge->days) . ")";
		} else {	//!M Да се провери третия вариант ^^^
			echo " (" . $user->sexy("регистриран", "регистрирана", "регистрирано") . " днес)";
		}
		if(!$user->can(User::CAN_LOGIN)) {
			echo "<br>Не е член на клуба. <a href='" . Yii::app()->user->getUrl("invites") . "?who=" . urlencode($user->login) . "'>Покана</a>.";
		}
		if($user->invited_by) {
			echo " по покана на " . $user->invitedBy->ahref;
		}
		?></dd>

	<?php
		$A = array();
		if($user->n_trs > 0) {
			$A[] =
				"<a href='" . $user->getUrl("books") . "'>" .
				"<strong>" . Yii::t("app", "{n} версия на превод|{n} версии на превод|{n} версии на превод", $user->n_trs) . "</strong>" .
				"</a>" .
				" с&nbsp;общ&nbsp;рейтинг&nbsp;<strong>{$user->rate_tFormatted}</strong>";
		}
		if($user->n_comments > 0) {
			$A[] = "<a href='" . $user->getUrl("comments") . "'><strong>" . Yii::t("app", "{n} коментар|{n} коментара|{n} коментара", $user->n_comments) . "</strong></a>";
		}
		if($user->n_karma > 0) {
			$A[] = "<a href='" . $user->getUrl("karma") . "'>Карма: {$user->rate_u}</a> (оценки: {$user->n_karma})";
		}

		if(count($A)) {
			echo "<dt>Дейност:</dt>";
			echo "<dd>" . join("<br />", $A) . "</dd>";
		}
	?>

</dl>
<?php if($user->id == Yii::app()->user->id) { ?>
	<div><i class="icon-pencil"></i> <a href='<?=$user->getUrl("edit"); ?>' class='act'>Редакция</a></div>
<?php } ?>

</div>
