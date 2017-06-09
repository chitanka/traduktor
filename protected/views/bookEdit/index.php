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
			„Фрагмент от оригинала“ е просто малко парче текст.
			Можете да ги заредите от текстов файл, да ги копирате от буфера или да ги наберете на ръка.
		</p>
	</div>
	<div class='span4 t'>
		<h2><a href='?typ=S'><span class="fa fa-film"></span> Субтитри</a></h2>
		<p>
			Всичко е същото, но всеки фрагмент е снабден с тайминг – времена за начало и край.
			Субтитрите могат да се зареждат във формат SRT.
		</p>
	</div>
</div>
