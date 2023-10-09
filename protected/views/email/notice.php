<p style="text-align: center;">
    <img style="width:50%;"
         src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    Здравейте, <?=$user->login; ?>!
</p>
<p>
    <?=nl2br($Notice->render()); ?>
</p>
<address style="margin-top: 20px; border-top: 1px solid gray; width: 200px;">
    С уважение,<br>
    "КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
    P. S. Това писмо е написано от изкуствен интелект. Не му отговаряйте. <br>
    Получавате тези писма, защото се включили препращане на
    <a href='http://<?=Yii::app()->params["domain"]; ?>/my/notices'>оповещения</a>
    на електронната си поща. Можете да я изключите на страницата на
    <a href='http://<?=Yii::app()->params["domain"]; ?>/register/settings'>настройките на сайта</a>.
</p>
