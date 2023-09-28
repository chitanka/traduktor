<?php
	$this->pageTitle = "{$book->fullTitle} - скопировать словарь";
?>

<h1>Копиране на речника</h1>

<p>
	Можете да копирате в превод &laquo;<?=$book->fullTitle; ?>&raquo; речника от всеки друг превод, в който
	сте модератор. Ако някоя дума вече е налична в речника, тя няма да бъде копирана. Изберете откъде да копирате речника:
</p>

<?php if(count($sources) == 0): ?>
<div class="alert alert-block alert-info">
	За съжаление, вие не сте модератор на нито един превод с речник.
</div>
<?php else: ?>
<ul>
<?php
	foreach($sources as $b) {
		echo "<li><a href='?from={$b->id}'>{$b->fullTitle}</a> ({$b->dict_cnt})</li>";
	}
?>
</ul>
<?php endif; ?>
<p>
    &larr; <a href="<?=$book->url; ?>">Връщане към съдържанието на превода.</a>
</p>
