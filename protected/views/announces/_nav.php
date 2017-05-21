<ul class='nav nav-tabs'>
	<li><a href='<?=$book->url; ?>/'>съдържание</a></li>
	<li><a href='<?=$book->getUrl("members"); ?>'>преводачи</a></li>
		<li><a href='<?=$book->getUrl("blog"); ?>'>блог</a></li>
	<li class='active'><a href='<?=$book->getUrl("announces"); ?>'>новини</a></li>
</ul>
