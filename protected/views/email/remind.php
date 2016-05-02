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
    <img src="http://<?= Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    Потеряли ключи от "КУРСОМИРА"? Они здесь: <br>
    <a href="<?= $token->url; ?>" class="token"><?= $token->url; ?></a><br>
    И больше не теряйте.
</p>
<p>
    Если всё на месте, проигнорируйте это сообщение, — с вашим аккаунтом ничего не случится.
</p>
<address>
    Заходите почаще,<br>
    "КУРСОМИР"
</address>
<p class="note">
    P. S. Это письмо написано искусственным интеллектом, отвечать на него ненадо.
</p>
</body>
