<p style="text-align: center;">
	<img style="width:50%;"
		 src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
	НЛО ви кани в клуба на преводачите MIT
	<a href='http://<?=Yii::app()->params["domain"]; ?>/'><?=Yii::app()->name; ?></a>.
</p>
<p>
	Тази покана е уникалният ви шанс да станете част от клуба на преводачите.<br>
	Регистрирайте се и се хващайте за работа!:<br>
	<a style="padding:10px; background: #a1ff80; color: #005580; border-radius: 9px; display: inline-block;"
	   href='<?=$invite->urlAccept; ?>'>
		<?=$invite->urlAccept; ?>
	</a><br>
	И помнете, вашата малка стъпка може да означава голямо бъдеще!
</p>

<?php if ($invite->message != ""): ?>
	<p>
		Между другото, помолиха да ви предадем:<br>
	</p>
	<p>
		<?=nl2br(htmlspecialchars($invite->message)); ?>
	</p>
<?php endif; ?>

<address style="margin-top: 20px; border-top: 1px solid gray; width: 200px;">
	Ждём в клубе!<br>
	"КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
	P. S. Това писмо е писано от изкуствен интелект. Не му отговаряйте.
</p>
