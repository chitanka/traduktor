<style type="text/css">
#recalc-form {text-align:center; margin-top:40px;}
</style>

<h1>Обновяване на брояча</h1>

<p>
	Понякога се случва така, че статистиката на превода &ndash; количеството части от оригинала, версиите на превода и полученият от това процент на готовност
	се смятат грешно. Ако смятате, че подобна неприятност се е случила с този превод, молим, натиснете Големия Червен Бутон
	внизу.
</p>
<form method="post" id="recalc-form">
	<input type="hidden" name="go" value="1" />
	<button type="submit" class="btn btn-large btn-danger"><i class="icon-fire icon-white"></i> Голям Червен Бутон</button>
	<br /><br />
	<?php if(Yii::app()->user->can("geek")): ?>
    	<input type="checkbox" name="full" value="1" id="cb-full"/>
		<label for="cb-full">
			 Също така пресмятане на рейтингите, броя коментари, броя преводи на всяка част
		</label>
	<?php endif; ?>
	<a href='<?=$book->url; ?>' class='btn'>Отмяна</a>
</form>
