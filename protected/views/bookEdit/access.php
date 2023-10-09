<?php
	/**
	 * @var Book $book
	 */

	$book->registerJS();

	$this->pageTitle = $book->isNewRecord ? "Нов превод: права на достъп" : "Права на достъп за превода " . $book->fulltitle;
?>
<style type='text/css'>
	#facecontrol-change-msg {display:none;}

	#ac_details {margin:20px 0 0 27px; width:auto;}
	#ac_details td, #ac_details th {padding:3px 6px; border-bottom:1px solid #aaa;}
	#ac_details th {white-space:nowrap;}
	#ac_details th.w {text-align:center;}
	#ac_details td {width:76px; text-align:center;}
	#ac_details td.hint {width:600px; text-align:left; color:#fff; }
	#ac_details td.hint a {color:#fff;}
	#ac_details tr:hover td.hint {color:#777;}
	#ac_details tr:hover td.hint a {color:#444;}
	#ac_details td.void {background:#000;}

	#ac_presets {}
	#ac_presets .row {}
	#ac_presets input {vertical-align:baseline;}
	#ac_presets label {font-size:16px;}
	#ac_presets .hint {margin:2px 0 16px 26px;}
</style>
<script type="text/javascript">
var E = {
	presets: {
		1: {ac_read: "a", ac_trread: "a", ac_gen: "a", ac_rate: "a", ac_comment: "a", ac_tr: "a", ac_blog_r: "a", ac_blog_c: "a", ac_blog_w: "a", ac_announce: "m", ac_chap_edit: "m", facecontrol: 0},
		2: {ac_read: "a", ac_trread: "a", ac_gen: "a", ac_rate: "a", ac_comment: "a", ac_tr: "g", ac_blog_r: "a", ac_blog_c: "a", ac_blog_w: "g", ac_announce: "m", ac_chap_edit: "m", facecontrol: 1},
		3: {ac_read: "a", ac_trread: "a", ac_gen: "a", ac_rate: "g", ac_comment: "g", ac_tr: "g", ac_blog_r: "a", ac_blog_c: "g", ac_blog_w: "g", ac_announce: "m", ac_chap_edit: "m", facecontrol: 1},
		4: {ac_read: "g", ac_trread: "g", ac_gen: "g", ac_rate: "g", ac_comment: "g", ac_tr: "g", ac_blog_r: "g", ac_blog_c: "g", ac_blog_w: "g", ac_announce: "m", ac_chap_edit: "m", facecontrol: 2}
	},

	init: function() {
		$("#ac_presets [name=preset]").click(E.preset);

		$("#form-edit [name=Book\\[ac_read\\]]").click(E.ac_read_ch);
//		E.ac_read_ch();
		E.facecontrol_ch();
	},

	ac_read_ch: function() {
		var v = $(this).val();
		d = {a: false, g: false, m: false};
		if(v == "g") d = {a: true, g: false, m: false};
		else if(v == "m") d = {a: true, g: true, m: false};
		else if(v == "o") d = {a: true, g: true, m: true};
		for(var i in d) {
			$("#form-edit :radio[value=" + i + "]").not("#form-edit [name=Book\\[ac_read\\]]").attr("disabled", d[i]);
		}
	},

	facecontrol_ch: function() {
		var facecontrol = $("#Book_facecontrol").val();
		if(facecontrol == 0) {
			$("#ac_details input[type=radio][value=g]").attr("disabled", true).each(function() {
				var $r = $(this);
				if($r.attr("checked")) {
					var sel = "#ac_details [name=" + $r.attr("name").replace("[", "\\[").replace("]", "\\]") + "]";
					$(sel + "[value=m]").prop("checked", true);
					$(sel + "[value=a]").prop("checked", true);

				}
			});
			if(Book.facecontrol != 0) $("#facecontrol-change-msg").show(100);
		} else {
			$("#ac_details input[type=radio][value=g]").attr("disabled", false);
			if(Book.facecontrol != 0) $("#facecontrol-change-msg").hide(100);
		}
	},

	preset: function() {
		var preset = $("#ac_presets [name=preset]:checked").val();
		var P = E.presets[preset];
		for(var field in P) {
			if(field == "facecontrol") {
				$("#form-edit [name=Book\\[facecontrol\\]]").val(P[field]);
			}
			var $radio = $("#form-edit [name=Book\\[" + field + "\\]]");

			$radio.each(function() {
				if(this.value == P[field]) $(this).click();
			})
		}
		E.facecontrol_ch();
	},

	rm: function() {
		if(!confirm("Абсолютно ли сте сигурни, че искате да изтриете този превод? С едно движение на мишката можете да изтрие труда на десетки преводачи!")) return;

		$("#form-rm").submit();
	}
};
$(E.init);
</script>

<h1>Права на достъп</h1>

<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		"id" => "form-edit",
		"type" => "horizontal",
		"inlineErrors" => false,
	));

	echo $form->errorSummary($book);

	$ac = array(
		"ac_read" =>       array("да влиза<sup>*</sup>", "кой може по принцип да влиза във всяка страница на превода; може да се укаже за всяка глава индивидуално"),
		"ac_trread" =>     array("да вижда чуждите версии<sup>*</sup>", "кой вижда всички предложени версии на превода"),
		"ac_gen" =>        array("да сваля резултата<sup>*</sup>", "може да се укаже за всяка глава индивидуално"),
		"ac_rate" =>       array("да оценява превода<sup>*</sup>", "може да се укаже за всяка глава индивидуално"),
		"ac_comment" =>    array("да коментира превода<sup>*</sup>", "може да се укаже за всяка глава индивидуално"),
		"ac_tr" =>         array("да превежда<sup>*</sup>", "добавяне на нови глави на превода; може да се укаже за всяка глава индивидуално"),

		"ac_blog_r" =>     array("да чете блога", "ако изберете &laquo;никто&raquo; то линкът към блога ще изчезне от менюто на превода"),
		"ac_blog_c" =>     array("да коментира в блога", "оставяне на коментари в блога"),
		"ac_blog_w" =>     array("да пише в блога", "писане на постове в блога"),

		"ac_announce" =>   array("да анонсира", "слагане на анонси на превода в <a href='/announce/'>съответния раздел</a>."),
		"ac_chap_edit" =>  array("да редактира оригинала", "добавяне/промяна на глава, импортване и редакция на оригинала"),
		"ac_book_edit" =>  array("да редактира описанието на превода", "промяна на името, описанието, езиците на превода, качване на нова картинка"),
		"ac_membership" => array("да управлява групата на превода", "разглеждане на заявки за встъпване в групата на превода, премахване от групата, канене в групата"),
	);
	$who = array("a" => "всички", "g" => "група", "m" => "модератори", "o" => "никой");
	$who3 = array("g" => "група", "m" => "модератори", "o" => "никой");
	$who2 = array("m" => "модератори", "o" => "никой");

	if($book->opts_get(Book::OPTS_BAN_COPYRIGHT)):
?>
<div class="alert alert-box alert-danger">
	<a class="close" data-dismiss="alert">×</a>
	<h4 class="alert-heading">Внимание!</h4>
	Този превод е блокиран по заявка на правообладателите. Всички операции с превода са достъпни само на групата преводачи, встъпване в групата &ndash; става само с покани.
</div>
<?php else: ?>
<div id="ac_presets">
	<div>
		<label class="radio"><input type="radio" name="preset" value="1" id="preset_1"/> Напълно отворен превод</label>
		<p class="hint">
			Превеждат, оценяват, обсъждат всички; Може да се създаде превод, да се назначат няколко добри модератора и тая работа да се зареже.
		</p>
	</div>

	<div>
		<label class="radio"><input type="radio" name="preset" value="2" id="preset_2" /> Групов превод</label>
		<p class="hint">
			Група преводачи превеждат, а останалите обсъждат и оценяват. Има още и модератори, които се занимават с добавяне на нови глави и анонси.
			Те разглеждат заявките за встъпване в групата. Добър вариант, ако сте се събрали група преводачи, но не сте достатъчно готини,
			за да не обръщате внимание на общественото мнение и ще се радвате на виждате нови хора в превода.
		</p>
	</div>

	<div>
		<label class="radio"><input type="radio" name="preset" value="3" id="preset_3"/> Перевод уверенной в себе группой</label>
		<p class="hint">
			Преждат, оценяват, обсъждат само членовете на групата. Простосмъртните могат само да изтеглят превода. Членството в групата става по заявки, които се разглеждат от модераторите.
			Отличен избор за създаване на идеален превод от група единомишленици.
		</p>
	</div>

	<div>
		<label class="radio"><input type="radio" name="preset" value="4" id="preset_4"/> Затворен превод</label>
		<p class="hint">
			Целият превод е достъпен само на преводачите. Участието в групата става само с покани.
			Ако искате да преведете нещо за себе си, изборът е ваш.
		</p>
	</div>
</div>

<h2>Подробности:</h2>
<?php

	echo $form->dropDownListRow(
		$book, "facecontrol",
		array(Book::FC_OPEN => "няма група", Book::FC_CONFIRM => "след потвърждение на модераторите", Book::FC_INVITE => "само с покана от модераторите"),
		array("onchange" => "E.facecontrol_ch()")
	);

	if(!$book->isNewRecord && $book->facecontrol != Book::FC_OPEN):
?>
<div id="facecontrol-change-msg" class="alert alert-block alert-warning">
	<a class="close" data-dismiss="alert">×</a>
	<h4 class="alert-heading">Внимание!</h4>
	Искате да отмените групата на превода. Това, което преди можеха да правят членовете на групата, ще могат да правят всички, а самата група
	ще бъде разпусната, впрочем, модераторите и баннатите ще запазят положението си. Статистиката на участието на всеки преводач също ще бъде запазена.
</div>
<?php
	endif;
	endif;
?>
<table id="ac_details" class="table">
	<thead><tr>
		<th>чкакво могат:</th>
		<th class='w'><?php echo join("</th><th class='w'>", $who); ?></th>
	</tr></thead>
	<?php
		foreach($ac as $role => $title) {
			echo "<tr>\n";
			echo "\t<th class='a'>{$title[0]}</th>\n";

			if($role == "ac_chap_edit" || $role == "ac_book_edit" || $role == "ac_membership") {
				$w = $who2;
				echo "<td>-</td><td>-</td>";
			} elseif($role == "ac_announce") {
				$w = $who3;
				echo "<td>-</td>";
			} else {
				$w = $who;
			}

			// echo "<td>" . $form->radioButtonList($model, $role, $w, array("template" => "{input}", "separator" => "</td><td>", "uncheckValue" => null)) . "</td>\n";
			foreach($w as $val => $t) {
				echo "<td><input type='radio' name='Book[{$role}]' value='{$val}' " . ($book->$role == $val ? "checked" : "") . ($book->opts_get(Book::OPTS_BAN_COPYRIGHT) && $val == "a" ? " disabled" : "") . "/></td>";
			};

//			echo "<td class='hint'>{$title[1]}</td>";
			echo "</tr>\n";
		}
	?>
</table>
<p class="help-block">
	<sup>*</sup> тези права могат да се дават отделно за всяка глава.
</p>

<div class="form-actions">
<?php
	if($book->isNewRecord) {
		echo "<a class='btn btn-primary' href='" . $book->getUrl("edit/info") . "'><i class='icon-arrow-left icon-white'></i> Назад</a> ";
		echo CHtml::htmlButton("Запис", array("type" => "submit", "class" => "btn btn-primary pull-right")) . " ";
	} else {
		echo CHtml::htmlButton("<i class='icon-ok icon-white'></i> Запис", array("type" => "submit", "class" => "btn btn-primary")) . " ";
		echo CHtml::htmlButton("<i class='icon-remove icon-white'></i> Отмяна", array("onclick" => "location.href='" . ($book->isNewRecord ? Yii::app()->user->url : $book->url) . "'", "class" => "btn btn-success"));
	}
?>
</div>
<?php $this->endWidget(); ?>
