<style type='text/css'>
	.note { color: #777; font-style: italic; }
	.token { padding:10px; background: #a1ff80; color: #005580; border-radius: 9px; }
	.logo { text-align: center; }
	.logo > img { width:50%; }
	address { margin-top: 20px; border-top: 1px solid gray; width: 200px; }
	blockquote { border-left: 2px solid #777; padding: 10px 0px 10px 10px; }
</style>
<body>
<p class="logo">
	<img src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
	НЛО приглашает вас в клуб переводчиков курсов MIT
	<a href='http://<?=Yii::app()->params["domain"]; ?>/'><?=Yii::app()->name; ?></a>.
</p>
<p>
	Это приглашение — ваш уникальный шанс стать частью команды переводчиков.<br>
	Регистрируйтесь — и за работу:<br>
	<a class="token" href='<?=$invite->urlAccept; ?>'>
		<?=$invite->urlAccept; ?>
	</a><br>
	И помните: ваш маленький шаг может создать большое будущее!
</p>

<?php if ($invite->message != ""): ?>
	<p>
		Кстати, вам просили передать:<br>
	</p>
	<p>
		<?=nl2br(htmlspecialchars($invite->message)); ?>
	</p>
<?php endif; ?>

<address>
	Ждём в клубе!<br>
	"КУРСОМИР"
</address>
<p class="note">
	P. S. Это письмо написано искусственным интеллектом, отвечать на него ненадо.
</p>
</body>
