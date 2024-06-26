<style type="text/css">
	p, form { text-align: center; line-height: 100%;}
	p { width:800px; }
	p, em { font-family:Georgia, Times, serif; font-size:20px; margin: 20px auto; }
	p.first { margin-top: 80px; font-size: 40px; }
	p.first em { display: block; font-size: 60px; font-style: normal; font-weight: bold; }

	p.how { font-size: 14px; color: #888; font-style: italic; }

	p.flash { width: 200px; padding: 20px; font-size: 15px; border-radius: 10px; }
	p.flash.error { background-color: #FAC6AB; color: #f00; }
	p.flash.success { background-color: #b4fa96; color: #000000; }

	p.flash, form { width: 200px; }
	form { margin: 40px auto; }
	form input, form button, form a { display: block; margin: 5px 0; }
	form input { width: 100%;  }
	form a { text-align: right; color: #777; text-decoration: underline; }
</style>
<p class="first">
	Секретен клуб на преводачите
	<em>&laquo;Notabenoid&raquo;</em>
	им. С. Я. Маршака
</p>
<p>
	Вход само за членове на клуба.
</p>
<?php
foreach(Yii::app()->user->getFlashes() as $key => $message) {
	echo "<p class='flash {$key}'>{$message}</div>";
}
?>
<form method="post" action="/">
	<input type="text" name="login[login]" placeholder="Логин" class="form-control" autofocus
		   value="<?=CHtml::encode(isset($_POST["login"]["login"]) ? $_POST["login"]["login"] : Yii::app()->user->getState("loginAs")); ?>"><br>
	<input type="password" name="login[pass]" placeholder="Парола" class="form-control"><br>
	<a href="/register/remind">Забравена парола?</a>
	<button type="submit" class="btn btn-success"><i class="icon icon-ok-circle icon-white"></i></button>
</form>
<p class="how">
	Ако сте имали акаунт в notabenoid.com и сте успели да преведете 500 фрагмента там, без при това да 
	получите отрицателен рейтинг или карма, вие, най-вероятно, вече сте член на клуба.
</p>
