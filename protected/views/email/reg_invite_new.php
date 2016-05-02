<p style="text-align: center;">
    <img style="width:50%;"
         src="http://<?=Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    НЛО приглашает вас в клуб переводчиков курсов MIT
    <a href='http://<?=Yii::app()->params["domain"]; ?>/'><?=Yii::app()->name; ?></a>.
</p>
<p>
    Это приглашение — ваш уникальный шанс стать частью команды переводчиков.<br>
    Регистрируйтесь — и за работу:<br>
    <a style="padding:10px; background: #a1ff80; color: #005580; border-radius: 9px; display: inline-block;"
       href='<?=$invite->urlAccept; ?>'>
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

<address style="margin-top: 20px; border-top: 1px solid gray; width: 200px;">
    Ждём в клубе!<br>
    "КУРСОМИР"
</address>
<p style="color: #777; font-style: italic;">
    P. S. Это письмо написано искусственным интеллектом, отвечать на него ненадо.
</p>
