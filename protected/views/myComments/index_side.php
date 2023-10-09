<?php
	/**
	 * @var String $mode "p", "o"
	 */
?>
<div class='tools'>
<h5>Моите обсъждания</h5>

<p>
	Погледнете вдясно. Ако видите предмет, който не сте използвали повече от година, изхвърлете я. Сега погледнете вляво, в центъра на екрана. Тук виждате фрагменти от оригинала, към които сте оставяли коментари или сте решили да следите дискусиите в тях.
</p>

<form method="post" action="/my/comments/ini">
	<label class="checkbox">
		<input type="checkbox" name="new" value="1" <?=Yii::app()->user->ini_get(User::INI_MYTALKS_NEW) ? " checked='checked'" : ""; ?> onclick="this.form.submit()" /> само с нови коментари
	</label>
</form>

<form method="post" action="/my/comments/visited" onsubmit="return confirm('Ако сега натиснете Ok, всички линкове на <?=$mode == "o" ? "преводи" : "постове"; ?> ще изчеснат оттук и вие никога няма да разберете какво е било написано докато там не се появят нови коментари. Става ли?');">
	<input type="hidden" name="mode" value="<?=$mode; ?>" />
	<button type="submit" class="btn btn-small btn-inverse" title="Ще стане, сякаш сте влизали във всички тези <?=$mode == "o" ? "преводи" : "постове"; ?>, прочели сте новите коментари и веднага сте забравили за това."><i class="icon-eye-open icon-white"></i> Изтриване на всичко</button>
</form>

<?php if(false && !Yii::app()->user->isGuest) { ?>( <a href='/blog/edit/' class='act'>писане на пост в общия блог</a> )<?php } ?>
</div>
