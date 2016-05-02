<style type='text/css'>
    .note { color: #777; font-style: italic; }
    .token { padding:10px; background: #a1ff80; color: #005580; border-radius: 9px; }
    .logo { text-align: center; }
    .logo > img { width:50%; }
    .orig { margin-left: 10px; }
    address { margin-top: 20px; border-top: 1px solid gray; width: 200px; }
    blockquote { border-left: 2px solid #777; padding: 10px 0px 10px 10px; }
</style>
<body>
<p class="logo">
    <img src="http://<?= Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    <?= $comment->author->ahref; ?> ответил<?= $comment->author->sexy(); ?> на ваш комментарий в переводе
    <a href="<?= $orig->url; ?>">
        <?= "{$orig->chap->book->fullTitle}: {$orig->chap->title}"; ?>
    </a>:
</p>
<p class="orig">
    <?= nl2br($orig->body); ?>
</p>

<p>Вы писали:</p>
<blockquote>
    <?= nl2br($parent->body); ?>
</blockquote>

<p>И вам ответили:</p>
<blockquote>
    <?= nl2br($comment->body); ?>
</blockquote>
<address>
    С уважением,<br>
    "КУРСОМИР"
</address>
<p class="note">
    P. S. Это письмо написано искусственным интеллектом, отвечать на него не надо. <br>
    Вы получаете эти письма потому, что включили пересылку
    <a href='http://<?= Yii::app()->params["domain"]; ?>/my/notices'>оповещений</a>
    на электронную почту. Отключить её можно на странице
    <a href='http://<?= Yii::app()->params["domain"]; ?>/register/settings'>настроек сайта</a>.
</p>
</body>
