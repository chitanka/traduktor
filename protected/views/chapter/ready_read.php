<?php
	/**
	 * @var Chapter $chap
	 * @var GenOptions $options
	 * @var ReadyGenerator_base $generator
	 */

	$this->pageTitle = "Готов превод {$chap->book->fullTitle}: {$chap->title}";
?>
<h1><?php echo "Готов превод {$chap->book->fullTitle}: {$chap->title}"; ?></h1>
<?php
	$generator->generate(false);
?>
