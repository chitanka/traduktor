<?php
	$this->pageTitle = $this->book->fullTitle;
?>
<h1><?=$this->book->fullTitle; ?></h1>
<p class="info">
	Отказахте участие в превода.
</p>
<p>
	<a href="<?=$this->book->url; ?>">Съдържание на превода</a> |
	<?php
		$here = $this->book->url("invite_decline");
		if($_SERVER["HTTP_REFERER"] != "" and substr($_SERVER["HTTP_REFERER"], -strlen($here)) != $here) {
			echo '<a href="', htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES), '">Назад</a> | ';
		}
	?>
	<a href="/">Главна</a>
</p>
