<?php
	$this->pageTitle="Грешка {$code}";

	$codes = array(
		"404" => "Страницата не е намерена",
		"403" => "Забранен достъп",
		"500" => "Системна грешка"
	);
?>
<div class="errorpage">
	<h1><?php echo isset($codes[$code]) ? $codes[$code]: "Грешка {$code}"; ?></h1>
	<?php
		$p = new CHtmlPurifier();
		$p->options = Yii::app()->params["HTMLPurifierOptions"];
		echo $p->purify($message);
	?>
</div>
