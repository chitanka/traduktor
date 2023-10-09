<?php
	/**
	 * @var CActiveDataProvider $orig_dp
	 * @var Chapter $chap
	 * @var TrFilter $filter
	 */

	Yii::app()->getClientScript()
		->registerCssFile("/css/translate-1div.css?2")
		->registerScriptFile("/js/translate-1div.js?5")
		->registerScriptFile("/js/jquery.scrollTo.js")
		->registerScriptFile("/js/jquery.elastic.mod.js")
		->registerCssFile("/css/jquery.mCustomScrollbar.css")
		->registerScriptFile("/js/jquery.mCustomScrollbar.min.js")
		->registerScriptFile("/js/ff_comments.js?2");

	Yii::app()->bootstrap->registerModal();

	$this->pageTitle = "Превод " . $chap->book->fullTitle . ": " . $chap->title;
	$this->layoutOptions["fluid"] = true;

	/** @var Orig[] $orig */
	$orig =  $orig_dp->getData();

	/** @var User $user  */
	$user = Yii::app()->user;

	$get = $_GET;
	unset($get["book_id"]);
	unset($get["chap_id"]);

	function getQS($merge = null, $unset = null) {
		$get = $_GET;
		unset($get["book_id"]);
		unset($get["chap_id"]);

		if(is_array($unset)) foreach($unset as $k) unset($get[$k]);

		if($merge !== null) $get = array_merge($get, $merge);

		return http_build_query($get);
	}
?>

<style type="text/css">
	.translator .text {font-size: <?=$user->ini["t.textfontsize"]; ?>px; }
</style>

<h1><?=$chap->book->ahref; ?>: <?=$chap->title; ?></h1>

<div id="tb-main"><div>
	<div class='group'><a href="<?=$chap->getUrl("go?" . getQS(array("nach" => "prev", "ord" => $chap->ord), array("Orig_page"))); ?>" title="Предишна глава"><i class="i icon-arrow-left"></i></a></div>

	<div class="tb-index btn-group">
		<a href="<?=$chap->book->url; ?>" class="btn btn-small"><i class="icon-list"></i> Съдържание</a>
		<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
		<ul class="dropdown-menu" id="tb-chapter-list">
			<li><a href="<?=$chap->book->getUrl("members"); ?>">Преводачи</a></li>
			<li><a href="<?=$chap->book->getUrl("blog"); ?>">Блог</a></li>
		</ul>
	</div>

	<div class='group'><a href="<?=$chap->getUrl("go?" . getQS(array("nach" => "next", "ord" => $chap->ord), array("Orig_page"))); ?>" title="Следваща глава"><i class="i icon-arrow-right"></i></a></div>

	<div class="btn-group">
		<a href="#" class="btn btn-small tb-dict" accesskey="V"><i class="icon-book"></i> Речник</a>

        <div id="tb-filter">
            <form method="get" class="form-inline" action="<?=$chap->url; ?>">
                <ul class='options'>
				<?php
					foreach ($filter::$modes as $k => $v) {
						if($k == 0) continue;
						echo "<li><label><input type='radio' name='show' value='{$k}' " . ($k == $filter->show ? " checked" : "") . "/> ";
						if($k == 2) {
							echo " <input type='text' name='show_user' placeholder='От преводача' class='span3' value='" . CHtml::encode($filter->show_user) . "' ";
							if(!$user->isGuest) echo "title='Ctrl+I: слагане на вашия ник' ";
							echo "/>";
						} elseif($k == 5) {
							echo " <input type='text' name='to' placeholder='Оригиналът съдържа' class='span3' value='" . CHtml::encode($filter->to) . "' />";
						} elseif($k == 6) {
							echo " <input type='text' name='tt' placeholder='Преводът съдържа' class='span3' value='" . CHtml::encode($filter->tt) . "' />";
						} else {
							echo "<a href='?show={$k}'>{$v}</a>";
						}
						echo "</label>";
						echo "</li>";
					}
				?>
                </ul>
                <button type="submit" class="btn btn-mini btn-primary">Показване</button>
                <a href="<?=$chap->getUrl(getQS(null, array("Orig_page", "show", "to", "tt", "show_user"))); ?>" class="btn btn-mini">Изтриване на всички</a>
            </form>
        </div>

		<?php echo $filter->getButton($orig_dp); ?>
	</div>

	<?php
		if($filter->show) echo "<div class='group'><a href='{$chap->url}' title='Изтриване на филтъра'><i class='i icon-remove'></i></a></div>";
	?>

	<?php if($chap->book->typ == "S" && $chap->book->can("chap_edit")): ?>
	<div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-small"><i class="icon-wrench"></i></a>
        <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
        <ul class="dropdown-menu" id="tools-list">
            <li><a href="#timeshift-modal" data-toggle="modal"><i class='icon-time'></i> Тайминг</a></li>
            <li><a href="#renum-modal" data-toggle="modal"><i class="icon-road"></i> Преномериране</a></li>
        </ul>
	</div>
	<?php endif; ?>

	<div class="group">
		<!--<a href="<?=$chap->getUrl("switchiface"); ?>"><i class="icon-thumbs-down" title="Стар интерфейс"></i></a>-->
		<a href="/blog/20938"><i class="icon-bullhorn" title="Обсъждане в блога"></i></a>
	</div>

	<?php if(0): ?>
	<div class="group tb-text-size">
		<a href="#" title="Намаляване на шрифта" class="smaller"><i class="icon-minus-sign"></i></a>
        <span class="current" title="Текущ размер на шрифта"><?=$user->ini["t.textfontsize"]; ?></span>
		<a href="#" title="Увеличаване на шрифта" class="bigger"><i class="icon-plus-sign"></i></a>
    </div>
	<?php endif; ?>

	<div class="tb-progress group"></div>

	<?php
		$p = $orig_dp->pagination;
		if($orig_dp->totalItemCount > $p->pageSize) {
			$g = $get;
			echo "<div class='chic-pages'>";
			if($p->currentPage + 1 == 1) echo "<span class='pseudo'>&larr;</span>";
			else {
				$g["Orig_page"] = $p->currentPage;
				echo "<a href='?" . http_build_query($g) . "' class='pseudo'>&larr;</a>";
			}

			echo " <ul class='selectable'>";
			for($i = 1; $i <= $p->pageCount; $i++) {
				$cur = $i == ($p->currentPage + 1);
				$g["Orig_page"] = $i;
				echo "<li" . ($cur ? " class='active'" : "") . ">";
				if($cur) echo "<input type='text' name='Orig_page' value='{$i}' placeholder='...' accesskey='g' />";
				else echo "<a href='?" . http_build_query($g) . "'>{$i}</a>";
				echo "</li>";
			}
			echo "</ul> ";

			if($p->currentPage + 1 >= $p->pageCount) echo "<span class='pseudo'>&rarr;</span>";
			else {
				$g["Orig_page"] = $p->currentPage + 2;
				echo "<a href='?" . http_build_query($g) . "' class='pseudo'>&rarr;</a>";
			}
			echo "</div>";
		}
	?>
</div></div>

<?php
	$tableClasses = array("translator");

	$tableEmpty = false;
	if($orig_dp->totalItemCount == 0):
		$tableClasses[] = "empty";
		if($filter->show == 0 || $chap->n_verses == 0) {
			$tableEmpty = true;
			echo "<p class='alert alert-block' id='alert-empty'>В тази част от превода липсва оригинален текст.";
			if($chap->book->can("chap_edit")) echo " Ако искате, можете да <a href='" . $chap->getUrl("import") . "'>качите</a> или <a href='#' class='create'>и създадете първи фрагмент</a>.";
			echo "</p>";
		} else {
			echo "<p class='alert alert-info'>Нищо не е намерено. <a href='{$chap->url}'>Показване на целия превод.</a></p>";
		}
	else:
		if(!$chap->book->can("trread")) {
			echo "<div class='alert alert-danger'>Собственикът е установил такива права за достъп, че не можете да виждате чужди версии на превода тук.</div>";
		}
	endif;

	if(Yii::app()->user->ini["t.hlr"] == 1) $tableClasses[] = "has-best";
	if(Yii::app()->user->ini["t.oe_hide"]) $tableClasses[] = "translator-oe-hide";
	if(!$chap->can("tr")) $tableClasses[] = "translator-te-hide";
?>
<table id="Tr" class="<?php echo join(" ", $tableClasses); ?>">
    <thead>
    <tr>
        <?php if(!Yii::app()->user->isGuest): ?><th></th><?php endif; ?>
        <th>
			<?php
				$t = (Yii::app()->langs->Langs[$chap->book->s_lang][Langs::FORM_INF]);
				echo mb_strtoupper(mb_substr($t, 0, 1)) . mb_substr($t, 1);
			?> оригинал
		</th>
		<?php if($chap->can("tr")): ?><th></th><?php endif; ?>
        <th>
			Перевод  на <?=Yii::app()->langs->Langs[$chap->book->t_lang][Langs::FORM_INF]; ?>
		</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
	<?php
		if($tableEmpty) {
//			echo "<tr id='o0'><td class='b'></td><td class='o'><div></div></td><td class='u'></td><td class='t'></td><td class='c'></td></tr>";
		} else {
			$fixer = new OrigCountFixer();
			$can_tr_read = $chap->can("trread");

			foreach($orig as $o) {
				$o->chap = $chap;

				echo "<tr id='o{$o->id}'>";

				if(!Yii::app()->user->isGuest) {
					echo "<td class='b'>";
					if($o->bookmark->id) {
						$title = "Закладка" . ($o->bookmark->note != "" ? (": &quot;" . CHtml::encode($o->bookmark->note) . "&quot;") : "");
						echo "<a href='#'><i class='icon-star' title='{$title}'></i></a>";
					} else {
						echo "<a href='#'><i class='i icon-star-empty'></i></a>";
					}

					// AUTOFIX. Если есть какой-нибудь фильтр на переводы, то не делаем пересчёт orig.n_trs!
					if($filter->show != 0 && $o->n_trs != count($o->trs)) {
						$fixer->add($o->id, count($o->trs)) && Yii::app()->user->can("geek") && print("<i class='icon-bell'></i>");
					}
					echo "</td>";
				}



				echo "<td class='o'><div>";
				echo $o->render($filter);
				echo "</div></td>";



				if($chap->can("tr")) {
					echo "<td class='u'>";
					echo "<a href='#'><i class='i icon-arrow-right'></i></a>";
					echo "</td>";
				}



				echo "<td class='t'>";
				echo $o->renderTranslations($filter);
				echo "</td>";




				echo "<td class='c'>";
				echo "<a href='#'>";
				if($o->n_comments > 0) {
					$n_new = $o->n_comments - $o->seen->n_comments;
					if($n_new) echo "<i class='icon-comment-new'></i> ";
					else echo "<i class='icon-comment'></i>";
					if($o->seen->n_comments) echo "{$o->seen->n_comments}<br />";
					if($n_new) echo "<b class='n'>+{$n_new}</b>";
				} else {
					if($chap->can("comment")) echo "<i class='i icon-comment-empty'></i>";
				}
				echo "</a>";
				echo "</td>";

				echo "</tr>\n";
			}

			$fixer->fix();
		}

		echo "</tbody></table>";
?>

<div id="tr-sidebar">
	<div id="dict" <?php if($chap->book->can("dict_edit")) echo "class='has-edit'"; ?>>
		<div id="dict-search">
			<input type="text" placeholder="Търсене в речника" class="search" />
			<a href="#" class="b"><i class="i icon-remove-sign"></i></a>
		</div>
		<div id="dict-body">
			<div id="dict-body-content">
				Минутка...
            </div>
		</div>
		<?php if($chap->book->can("dict_edit")): ?>
		<div id="dict-add">
			<form method="post" action="<?=$chap->book->getUrl("dict"); ?>">
				<input type="text" name="term" placeholder="Оригинал" />
				<input type="text" name="descr" placeholder="Превод" />
				<button type="submit" class="btn btn-mini btn-primary">Добавяне</button>
				<button type="button" class="btn btn-mini cancel">Отмяна</button>
			</form>
		</div>
		<div id="dict-tools">
			<i class="icon-plus"></i> <a href="#" class="add">Добавяне на дума</a>
		</div>
		<?php endif; ?>
		<?php if(0): ?><div id="dict-pages"></div><?php endif; ?>
	</div>
</div>

<?php if($chap->book->typ == "S"): ?>
<div id="timeshift-modal" class="modal hide">
    <form method="post" class="form-inline" action="<?=$chap->getUrl("timeshift"); ?>">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Преместване на тайминг</h3>
        </div>
        <div class="modal-body">
			<div class="control-group advanced">
				<label class="control-label">Преместване на титри във времевия промеждутък</label>
				<div class="controls">
                    <input type="text" name="from" placeholder="ЧЧ:ММ:СС.ммм" value="00:00:00.000" /> &mdash;
                    <input type="text" name="to" placeholder="ЧЧ:ММ:СС.ммм" value="23:59:59.999" />
				</div>
			</div>
            <div class="control-group">
                <label class="control-label">Преместване <a href="#" class="ajax advanced">на всички субтитри</a> с:</label>
                <div class="controls">
                    <input type="text" name="value" placeholder="ЧЧ:ММ:СС.ммм" value="00:00:00.000" autofocus />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Започване</button>
            <a href="#" class="btn" data-dismiss="modal">Отмяна</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div id="renum-modal" class="modal hide">
    <form method="post" class="form-inline" action="<?=$chap->getUrl("renum"); ?>">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Преномериране на субтитрите</h3>
        </div>
        <div class="modal-body">
			Номерът на титъра, сивата цифра до тайминъга, на практика, не влияе на нищо, титрите се сортират по време.
			Ако добавяте нов титър, номерът му може да се различава от номерацията. Този инструмент ще направи номерацията
			на титрите в хронологичен ред.
        </div>
        <div class="modal-footer">
			<input type="hidden" name="mode" value="1" />
            <button type="submit" class="btn btn-primary">Започни</button>
            <a href="#" class="btn" data-dismiss="modal">Отмяна</a>
        </div>
    </form>
</div>

<script type="text/javascript">
	$("#Tr td.o").each(function() {
		var $this = $(this);
		$this.children("div").css("min-height", $this.height());
	});
</script>
