<p style="text-align: center;">
    <img style="width:50%;"
         src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    <?=$comment->author->ahref; ?> оставил<?=$comment->author->sexy(); ?> новый комментарий в вашем переводе
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
    С уважением,<br>
    "КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
    P. S. Это письмо написано искусственным интеллектом, отвечать на него не надо. <br>
    Вы получаете эти письма потому, что включили пересылку
    <a href='http://<?=Yii::app()->params["domain"]; ?>/my/notices'>оповещений</a>
    на электронную почту. Отключить её можно на странице
    <a href='http://<?=Yii::app()->params["domain"]; ?>/register/settings'>настроек сайта</a>.
</p>
