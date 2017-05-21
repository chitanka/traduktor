<?php
	$this->pageTitle = "Нов превод";
?>
<style type="text/css">
	.t { float:left; margin-top:30px; margin-bottom:30px; text-align: center; }
	.t:hover {background:#f0f0f0; border-radius: 10px;}
	.t big {font-size:30px; margin-top:10px; margin-bottom: 20px; display: block; line-height: 100%;}
	.t p { margin-left: 10px; margin-right: 10px;}
</style>
<h1><?= $this->pageTitle ?></h1>

<p>
	Създаването на проект за превод става в четири стъпки. Нека първо изберем <b>формата на превода</b>. Какво ще превеждате?
</p>

<div class="row">
	<div class='span4 t'>
		<h2><a href='?typ=A'><span class="fa fa-book"></span> Текст</a></h2>
		<p>
			Фрагмент оригинала &ndash; это просто небольшой кусочек текста.
			Их можно загрузить из текстового файла, скопировать из буфера обмена или набрать вручную.
		</p>
	</div>
	<div class='span4 t'>
		<h2><a href='?typ=S'><span class="fa fa-film"></span> Субтитри</a></h2>
		<p>
			Всё то же самое, но каждый фрагмент снабжён таймингом &ndash; временем начала и конца.
			Субтитры можно загрузить в формате SRT.
		</p>
	</div>
</div>
