<?php
	if(Yii::app()->user->isGuest || !Yii::app()->user->ini_get(User::INI_ADDTHIS_OFF)) {
		Yii::app()->clientScript
			->registerScriptFile("http://userapi.com/js/api/openapi.js?49")
			->registerScript("VKLIKE", "VK.init({apiId: 3013223, onlyWidgets: true});", CClientScript::POS_HEAD);

		echo <<<FB
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
FB;

	}
?>
<div class='tools'>
	<h5>Превод</h5>

	<dl class='info'>
		<dt>Превод:</dt>
		<dd><?=Yii::app()->params["book_types"][$book->typ] . " " . Yii::app()->langs->from_to($book->s_lang, $book->t_lang); ?></dd>

		<dt>Създаден:</dt>
		<dd><?php echo Yii::app()->dateFormatter->formatDateTime($book->cdate, "medium", "") . ', собственик: ' . $book->owner->ahref; ?></dd>

		<?php if($book->n_dl > 0): ?>
		<dt>Брой сваляния:</dt>
		<dd>
			<span rel='popover' data-content='Смятат се само качвания от уникални IP адреси. Понякога компютрите от една домашна или офисна мрежа имат еднакъв IP-адрес, затова гледайте на тези цифри като примерна, занижена оценка.' data-title=Откъде се взимат тези цифри?">
			<?php
				echo "{$book->n_dl} чов.";
				if($book->n_dl_today > 0) {
					echo " (днес &ndash; {$book->n_dl_today})";
				}
			?>
			</span>
			<script type='text/javascript'>$("span[rel=popover]").popover();</script>
		</dd>
		<?php endif; ?>

		<dt>Права на достъп:</dt>
		<dd>
			<div id="ac_icons">
			<?php
				$ac_important = array("ac_read", "ac_trread", "ac_gen", "ac_rate", "ac_comment", "ac_tr");
				foreach($ac_important as $ac) {
					echo "<i class='{$ac} {$book->$ac}'></i> ";
				}
			?>
			<a href="#" class='more_btn' onclick="$('#side_ac_more').show(); $('#ac_icons').hide(); $(this.parentNode).hide(); return false;">подробности...</a>
			</div>
			<div class="more" id="side_ac_more" style="display:none;">
				<table class="t">
				<?php
					foreach(Yii::app()->params["ac_areas"] as $ac => $title) {
						echo "<tr><td>";
						if(in_array($ac, $ac_important)) echo "<i class='{$ac} {$book->$ac}'></i> ";
						echo $title;
						echo "</td><td class='d'>" . Yii::app()->params["ac_roles"][$book->$ac] . "</td></tr>";
					}
				?>
				<tr>
					<td>Участие в групата</td>
					<td class='d'><?php
						if($book->ac_membership == "m") {
							$A = array(Book::FC_OPEN => "няма група", Book::FC_CONFIRM => "след подтвърждение от модератор", Book::FC_INVITE => "по покана на модератор");
						} else {
							$A = array(Book::FC_OPEN => "няма група", Book::FC_CONFIRM => "след подтвърждение от създателя", Book::FC_INVITE => "по покана на създателя");
						}
						echo $A[$book->facecontrol];
					?></td>
				</tr>
				</table>
				<?php if($book->can("owner")) echo "<div style='text-align:right; margin-top:5px;'><a href='" . $book->getUrl("edit/access") . "' class='act'>Редактиране</a></div>"; ?>
			</div>
		</dd>

		<dt>Готово:</dt>
		<dd><?php
			if($book->n_vars == 0 || $book->n_verses == 0) echo "&mdash;";
			else {
				$procent = floor($book->d_vars / $book->n_verses * 10000) / 100;
				$classes = array(100 => "progress-danger", 80 => "progress-warning", 60 => "progress-success", 40 => "", 20 => "progress-info");
				foreach($classes as $p => $class) {
					if($procent >= $p) {
						break;
					}
				}
				$class = "progress-success";

				echo "<div class='progress progress-striped {$class}' style='margin-bottom:2px;'>";

				printf("<div class='bar' style='width: %d%%;'></div>", $procent);

				echo "<div class='text'>";
				printf("<span title='глави: %d, фрагменти: %d, преведено: %d'>%0.02f%%</span>", $book->n_chapters, $book->n_verses, $book->d_vars, $procent);
				if($book->d_vars > 0 and $book->n_vars > 0) printf(" <abbr title='Коефициент на плурализъм: средно количество на вариантите на превод на всеки фрагмент'>КП</abbr> = %.01f", $book->n_vars / $book->d_vars);
				echo "</div>";

				echo "</div>";
			}
		?></dd>

		<?php
			if(!Yii::app()->user->isGuest) {
				$myStatus = "";
				if ($book->membership) {
					if($book->membership->status == GroupMember::BANNED) {
						$myStatus = "Достъпът до превода ви е забранен.";
					} elseif($book->membership->status == GroupMember::MODERATOR) {
						$myStatus = "Вие сте модератор на групата. <a href='" . $book->getUrl("members") . "#leave'><span class=\"fa fa-sign-in\"></span> Вход</a>";
					} elseif($book->membership->status == GroupMember::MEMBER) {
						$myStatus = "Вие сте член на групата на превода. <a href='" . $book->getUrl("members") . "#leave'><span class=\"fa fa-sign-in\"></span> Вход</a>";
					}
				} else {
					if($book->owner_id == Yii::app()->user->id) {
						$myStatus = "Вие сте създател на превода.";
					} elseif($book->facecontrol != Book::FC_OPEN) {
						$myStatus = "Не сте член на групата на превода. ";
						if($book->facecontrol == Book::FC_CONFIRM) {
							$myStatus .= "<a href='" . $book->getUrl("members") . "' class='act' title='Вашу заявку сначала рассмотрят модераторы'>Вступить</a>.";
						} elseif($book->facecontrol == Book::FC_INVITE) {
							$myStatus .= "Членство в групата &ndash; само по покана на " . ($book->ac_membership == "m" ? "модератор" : "собственика на превода") . ".";
						}
					}
				}
				if($myStatus != "") echo "<dt>Вашият статус:</dt><dd>{$myStatus}</dd>";
			}
		?>
    </dl>
</div>


<?php
	$Tools = array();
	$Tools[] = "<a href='http://www.imdb.com/find?q=" . urlencode($book->s_title) . "&s=tt' target='_blank' rel='nofollow'>Търсене в IMDb</a>";
	if(!Yii::app()->user->isGuest) $Tools[] = "<a href='" . $book->getUrl("recalc") . "'>Преизчисляване на статистиката за превода</a>";
?>
<div class='tools'>
	<h5>Инструменти</h5>
	<?php if(!Yii::app()->user->isGuest):
		echo "<button class='btn btn-small' id='btn-bookmark' onclick='Book.bookmark({$book->id})'>";
		if($book->bookmark) {
			echo "<i class='icon-star'></i> Промяна на отметката";
		} else {
			echo "<i class='icon-star-empty'></i> Поставяне на отметка";
		}
		echo "</button>";
	?>
	<?php endif; ?>

	<ul style="margin-top:10px"><li><?=join("</li><li>", $Tools); ?></li></ul>

</div>
