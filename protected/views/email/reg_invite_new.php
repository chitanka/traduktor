<?php
/**
 * @var RegInvite $invite
 */
?>
<p style="text-align: center;">
    <img src="http://translate.kursomir.ru/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>Привет!</p>
<p>
    НЛО пригласили тебя в клуб переводчиков курсов MIT
    <a href='http://translate.kursomir.ru'>"Курсомир.Переводы"</a>.
    Это приглашение — твой единственный шанс стать частью команды переводчиков.
    Чтобы зарегистрироваться, пройдите по ссылке:
</p>

<p>
    <a href='<?= $invite->urlAccept; ?>'><?= $invite->urlAccept; ?></a>
</p>

<p>
    И помни:
    1 правило клуба переводчиков — никому не говори о клубе
    2 правило — к чёрту все правила! Трещи о нашем клубе направо и налево!
</p>
<p>
    Ждём в клубе!
    Курсомир
</p>

<?php if ($invite->message != ""): ?>
    <p>
        Кстати, Вам просили передать следующее:<br>
        <?= nl2br(htmlspecialchars($invite->message)); ?>
    </p>
<?php endif; ?>

<p style='color:#777; font-style:italic;'>
    P. S. Это письмо написано искусственным интеллектом, отвечать на него ненадо.
</p>
