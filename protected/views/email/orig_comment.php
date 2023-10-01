<p style="text-align: center;">
    <img style="width:50%;"
         src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    <?=$comment->author->ahref; ?> написа<?=$comment->author->sexy(); ?> нов коментар във вашия превод
    <a href="<?=$orig->url; ?>">
        <?="{$orig->chap->book->fullTitle}: {$orig->chap->title}"; ?>
    </a>
    :
</p>
<p style="margin-left: 10px;">
    <?=nl2br($orig->body); ?>
</p>
<blockquote style="border-left: 2px solid #777; padding: 10px 0px 10px 10px;">
    <?=nl2br($comment->body); ?>
</blockquote>
<address style="margin-top: 20px; border-top: 1px solid gray; width: 200px;">
    С уважение,<br>
    "КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
    P. S. Това писмо е написано от изкуствен интелект. Не му отговаряйте. <br>
    Получавате тези писма, защото се включили препращане на
    <a href='http://<?=Yii::app()->params["domain"]; ?>/my/notices'>съобщения</a>
    на електронната си поща. Можете да го изключите на страницата на
    <a href='http://<?=Yii::app()->params["domain"]; ?>/register/settings'>настройките на сайта</a>.
</p>
