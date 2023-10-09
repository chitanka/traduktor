<style type='text/css'>
	p.note {color:#777; font-style:italic;}
	address {margin-top:20px; border-top:1px solid gray; width:200px;}
	blockquote {border-left:2px solid #777; padding:10px 0px 10px 10px;}
</style>
<base href="http://<?=Yii::app()->params["domain"]; ?>" />
<body>
<p>Здравейте, <?=$message->buddy->login; ?>!</p>

<p><?=Yii::app()->user->ahref; ?> ви написа лично съобщение на сайта <?=Yii::app()->name; ?>:</p>

<blockquote>
	<?=nl2br($message->body); ?>

	<br /><br />
	<b><a href="/my/mail/write/?reply=<?=$message->id; ?>">Отговор.</a></b>
</blockquote>

<p class='note'>
	P. S. Това писмо е написано от изкуствен интелект. Не му отговаряйте.
	Получавате писмото, защото се включили опцията за оповестяване на <a href='/my/mail'>личните съобщения</a> на електронната си поща. Можете да я изключите в
	<a href='/register/settings'>настройките на сайта</a>.
</p>
