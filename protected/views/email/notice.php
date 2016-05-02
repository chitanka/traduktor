<p style="text-align: center;">
    <img style="width:50%;"
         src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    Добрый день, <?=$user->login; ?>!
</p>
<p>
    <?=nl2br($Notice->render()); ?>
</p>
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
