<?php
/**
 * @var Chapter $chap
 */
?>
<h1>Сваляне на превода <?php echo "{$chap->book->fullTitle}: {$chap->title}"; ?></h1>
<p>
	В тази глава няма нито един преведен фрагмент.
</p>
<p>
	<a href="<?=$chap->book->url; ?>">Към съдържанието</a> |
	<?php if($chap->can("tr")) echo "<a href='{$chap->url}'>Превеждане</a>"; ?>
</p>
