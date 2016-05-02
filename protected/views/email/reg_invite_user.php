<?php
/**
 * @var RegInvite $invite
 */
?>
<p style="text-align: center;">
	<img style="width: 50%;"
		 src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
	НЛО приглашает вас в клуб переводчиков курсов MIT <a href='http://<?=Yii::app()->params["domain"]; ?>/'><?=Yii::app()->name; ?></a>.
</p>
<p>
	Это приглашение — ваш уникальный шанс стать частью команды переводчиков.<br>
	Регистрируйтесь — и за работу:<br>
	<a href='<?=$invite->urlAccept; ?>'><?=$invite->urlAccept; ?></a><br>
	И помните: ваш маленький шаг может создать большое будущее!
</p>
<p>
	Ждём в клубе!<br>
	"КУРСОМИР"
</p>

<?php if($invite->message != ""): ?>
	<p>
		Кстати, вам просили передать:<br>
	</p>
	<p>
		<?=nl2br(htmlspecialchars($invite->message)); ?>
	</p>
<?php endif; ?>

<p style='color:#777; font-style:italic;'>P. S. Это письмо написано искусственным интеллектом, отвечать на него не надо.</p>
