<style type='text/css'>
    p.note { color: #777; font-style: italic; }
    address { margin-top: 20px; border-top: 1px solid gray; width: 200px; }
    blockquote { border-left: 2px solid #777; padding: 10px 0px 10px 10px; }
</style>

<body>
<p style="text-align: center;">
    <img style="width: 50%;"
         src="http://<?= Yii::app()->params["domain"]; ?>/i/logo.png" alt="Курсомир.Переводы">
</p>
<p>
    Потеряли ключи от "КУРСОМИРА"? Они здесь: <br>
    <a href="<?= $token->url; ?>"
       style="padding:10px; background: #a1ff80; color: #005580; border-radius: 9px; "><?= $token->url; ?></a><br>
    И больше не теряйте.
</p>
<p>
    Если всё на месте, проигнорируйте это сообщение, — с вашим аккаунтом ничего не случится.
</p>
<p>
    Заходите почаще,<br>
    "КУРСОМИР"
</p>
<p style='color:#777; font-style:italic;'>
    P. S. Это письмо написано искусственным интеллектом, отвечать на него ненадо.
</p>
</body>
