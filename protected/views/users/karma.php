<?php
	Yii::app()->clientScript
		->registerScriptFile("/js/profile.js")->registerCssFile("/css/profile.css?3");

	$this->pageTitle = $user->login . ": карма";

	$this->renderPartial("profile_head", array("user" => $user, "h1" => "карма = <span title='оценки: {$user->n_karma}'" . ($user->rate_u < 0 ? " class='negative'" : "") . ">{$user->rate_uFormatted}"));
?>
<script type="text/javascript">
var KarmaSet = {
	init: function() {
		$("#form-karma [name=KarmaMark\\[mark\\]]").click(function() {
			$("#form-karma [name=KarmaMark\\[note\\]]").attr("disabled", $(this).val() == 0);
		});
        $("#form-karma [name=KarmaMark\\[note\\]]").attr("disabled", $("#form-karma [name=KarmaMark\\[mark\\]]:checked").val() == 0);
	}
}
$(KarmaSet.init);
</script>

<?php if($dir != "out"): ?>
<?php if(Yii::app()->user->isGuest): ?>
	<p class="alert alert-block alert-info fade in">
		За да оценявате други преводачи, трябва да се <a href="/register">регистрирате</a> или <a href="#" onclick="return Global.login()">влезете</a> в сайта.
	</p>
<?php elseif(!Yii::app()->user->can("karma")): ?>
	<p class="alert alert-block alert-info fade in">
		Само потребители, регистрирани повече от 180 дни могат да поставят оценки в кармата.
	</p>
<?php elseif(Yii::app()->user->id != $user->id): ?>
	<?php
		/** @var TbActiveForm $form */
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			"id" => "form-karma",
			"type" => "horizontal",
			"inlineErrors" => false,
		));
	?>
	<div class="row">
		<div class="span3">
			<h3>Вашата оценка</h3>
			<?php echo $form->radioButtonList($my_mark, "mark", array(1 => "Положителна", 0 => "Никаква", -1 => "Отрицателна"), array("uncheckValue" => null)); ?>
			<button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Поставете оценка</button>
		</div>
		<div class="span5">
			<h3>Комментари</h3>
			<?php echo $form->textArea($my_mark, "note", array("class" => "span5", "style" => "height:85px;")); ?>
		</div>
	</div>
	<?php
		$this->endWidget();
	?>
<?php endif; ?>
<?php endif; ?>


<?php if($marks->totalItemCount): ?>
<?php
	$html = array(-1 => "", 1 => "");
	$stat = array(-1 => 0, 1 => 0);
	foreach($marks->data as $mark) {
		$u = $dir == "out" ? $mark->to : $mark->from;
		$html[$mark->mark] .= "<li><a href='{$u->url}' title='" . Yii::app()->dateFormatter->formatDateTime($mark->dat, "medium", "short") . "'>{$u->login}</a>" . ($mark->note != "" ? ": &laquo;{$mark->note}&raquo;" : "") . "</li>";
		$stat[$mark->mark]++;
	}
	$header = array(-1 => "Минуси", 1 => "Плюсове");
	foreach(array(-1, 1) as $z) {
		if($stat[$z] != 0) $header[$z] .= " ({$stat[$z]})";
	}
?>

<div class="row marks-table">
<?php
	foreach(array(-1, 1) as $z) {
		echo "<div class='span4 " . ($z == -1 ? "minus" : "plus") . "'>";
		echo "<h3>{$header[$z]}</h3>";
		if($html[$z] != "") {
			echo "<ul>";
			echo $html[$z];
			echo "</ul>";
		} else {
			echo $dir == "out" ? "На никого" : "Никой";
		}
		echo "</div>";
	}
?>
</div>
<?php
	$this->widget('CLinkPager', array("pages" => $marks->pagination));
?>
<?php endif; ?>
