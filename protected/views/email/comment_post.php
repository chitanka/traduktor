<p style="text-align: center;">
	<img style="width:50%;"
		src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
	<?=$comment->author->ahref; ?> оставил<?=$comment->author->sexy(); ?> нов коментар във вашия пост
	<a href="<?=$post->url; ?>">
		<?=$post->title; ?>
	</a>.
</p>
<blockquote style="border-left: 2px solid #777; padding: 10px 0px 10px 10px;">
	<?=nl2br($comment->body); ?>
</blockquote>
<p>
	<a href="<?=$post->url; ?>#cmt_<?=$comment->id; ?>"Отговор</a>.
</p>

<address style="margin-top: 20px; border-top: 1px solid gray; width: 200px;">
	С уважение,<br>
	"КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
	P. S. Това писмо е написано от изкуствен интелект. Не му отговаряйте. <br>
	Получавате тези писма, защото се включили получаване на
	<a href='http://<?=Yii::app()->params["domain"]; ?>/my/notices'>съобщения</a>
	на електронната си поща. Можете да я изключите в
	<a href='http://<?=Yii::app()->params["domain"]; ?>/register/settings'>настройките на сайта</a>.
</p>
