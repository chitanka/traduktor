<?php
	$this->pageTitle = "Огорчете Дачко";
?>
<style type="text/css">
#sadcat { display: block; margin: 15px auto; }
</style>
<h1>О боже, не!</h1>
<p>
	Дачко е много разстроен, че сте решили да ни напуснете.
</p>
<p>
	<img src="/i/deleteuser/cat<?=rand(1, 5); ?>.jpg" width="320" alt="Сърцето ви е от камък" id="sadcat" />
</p>
<p>
	Всъщност вие може изобщо да не влизате в сайта. Може би, след много години, отново ще ви се наложи да влезете тук.
	А ако ви дразнят писмата от нас и другите потребители, можете просто да ги <a href="/register/settings">изключите в настройкит на сайта</a>.
</p>
<p>
	Но ако сте твърдо убедени в решението си, въведете паролата си:
</p>
<form method="POST" class="form-inline" style="text-align:center;">
	<input type="hidden" name="really" value="1" />
	<input type="password" name="pass" class="span3" />
	<button type="submit" class="btn btn-danger"><i class="icon-ban-circle icon-white"></i> Премахване на акаунта</button>
</form>
<p>
	Всички ваши преводи, съобщения, коментари и оценки ще останат на сайта, защото написаното остава завинаги.
	Вашето потребителско име (<code><?=Yii::app()->user->login; ?></code>) също ще остане заето.
</p>
