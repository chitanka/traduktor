<?php
	/**
	 * @var CActiveDataProvider $dp
	 * @var Announce[] $announces
	 */

	$this->pageTitle = "Анонси относно преводите";

	$announces = $dp->getData();
?>

<h1><?= $this->pageTitle ?></h1>

<div id="Announces">
<?php
	if($dp->getTotalItemCount() == 0) {
		echo "<div class='alert alert-info'>Нищо не е намерено. Опитайте се да разширите критериите за търсене.</div>";
	} else {
		$this->widget('bootstrap.widgets.TbPager', array("pages" => $dp->pagination));
		foreach($announces as $announce) {
			$this->renderPartial("_announce", array("announce" => $announce));
		}
		$this->widget('bootstrap.widgets.TbPager', array("pages" => $dp->pagination));
	}
?>
</div>
