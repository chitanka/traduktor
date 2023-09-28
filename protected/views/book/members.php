<?php
	$this->pageTitle = $book->fullTitle . " - преводачи";

	Yii::app()->getClientScript()
		->registerScriptFile("/js/book.js?1");

	$book->registerJS();

	if($book->can("membership")) Yii::app()->getClientScript()->registerScriptFile("/js/GM.js");
?>
<script type="text/javascript">
var P = {
	init: function() {
		$(window).bind('hashchange', P.hashchange);
		P.hashchange();
	},
	hashchange: function() {
		if(location.hash == "#leave") {
			$("#leave-box").show();
			$(document).scrollTop(0);
		}
	}
}
$(P.init);
</script>

<style type="text/css">
#join_btn {display:block;}
#join_msg {display:none;}
#leave-box {display:none;}
#inquisition { margin:10px 0 30px; }
#inquisition-actions {display:none; }
</style>

<ul class='nav nav-tabs'>
	<li><a href='<?=$book->url; ?>/'>съдържание</a></li>
	<li class='active'><a href='<?=$book->getUrl("members"); ?>'>преводачи</a></li>
	<li><a href='<?=$book->url("blog"); ?>'>блог</a></li>
	<li><a href='<?=$book->getUrl("announces"); ?>'>анонси</a></li>
</ul>

<h1><?=$book->fullTitle; ?> &ndash; преводачи&nbsp;(<?=$members_dp->totalItemCount; ?>)</h1>

<?php if($book->facecontrol != Book::FC_OPEN and Yii::app()->user->id != $book->owner_id and !Yii::app()->user->isGuest): ?>
<?php
	if($book->checkMembershipStatus(0)) {
		$group_needed = "";
		$A = $book->role_areas("g");
		if(count($A) > 0) {
			foreach($A as $ac) {
				if($group_needed == "") $group_needed = "В този превод трябва да сте групата на преводачите, за да ";
				else $group_needed .= ", ";
				$group_needed .= Yii::app()->params["ac_areas"][$ac];
			}
			$group_needed .= ".";
		}

		if($book->facecontrol == Book::FC_CONFIRM) {
?>
			<form method="post" action="<?=$book->url("members_join"); ?>" class="well form-horizontal">
				<?php if($group_needed != ""): ?><p><?=$group_needed; ?></p><?php endif; ?>
				<div id="join_btn">
					<button type="button" onclick="$('#join_btn').hide(); $('#join_msg').show(); $('#join_msg [name=message]').focus();" class="btn btn-success">
						<i class="icon-plus icon-white"></i> Встъпване в групата
					</button>
				</div>
				<div id="join_msg">
					<label>
						Вашата заявка ще бъде разгледана от <?php echo $book->ac_membership == "m" ? "модераторите" : "създателя на превода"; ?>.
						Можете да им напишете кратко съобщение:
					</label>
					<input type="text" name="message" maxlength="200" class="span4" />
					<label class="checkbox">
						<input type="checkbox" name="bm" value="1" checked /> добавяне на превода в отметките
					</label>
					<button type="submit" class="btn"><i class="icon-ok"></i> Изпращане на заявка</button>
					<button type="button" class="btn" onclick="$('#join_btn').show(); $('#join_msg').hide(); $('#join_msg [name=message]').focus();"><i class="icon-remove"></i> Отмяна</button>
				</div>
			</form>
<?php
		} elseif($book->facecontrol == Book::FC_INVITE and $group_needed != "") {
			echo "<div class='well'>";
			echo "<p>{$group_needed}</p>";
			echo "<p>Но, за да станете член на тази група, трябва да получите персонална покана от " . ($book->ac_membership == "m" ? "модераторите" : "създателя на превода") . ".</p>";
			if($book->user_invited(Yii::app()->user->id)) {
				echo "<p>И, между другото, вече имате тази покана.</p>";
				echo "<a href='" . $book->url("invite_accept") . "' class='act'>Приемане</a> | <a href='" . $book->url("invite_decline") . "' class='act'>Отказать</a>";
			}
			echo "</div>";
		}
	} else {
?>
		<form method="post" id="leave-box" class="well" action="<?=$book->url("members_leave"); ?>" onsubmit="return confirm('Сигурни ли сте, че искате да излезете от тази група?')">
			<input type="submit" value="Напускане на групата" class="btn btn-danger" />
			<p class="help-block"><?php
				if($book->facecontrol == Book::FC_INVITE) {
					echo "Между другото, за да се върнете в групата, трябва да получите покана от " . ($book->ac_membership == "m" ? "модераторите" : "създателя на превода") . ".";
				} elseif($book->facecontrol == Book::FC_CONFIRM) {
					echo "Между другото, за да се върнете в групата, отново ще трябва да чакате решението на " . ($book->ac_membership == "m" ? "модераторите" : "създателя на превода") . ".";
				}
			?></p>
		</form>
<?php
	}
?>
<?php endif; ?>


<?php
	/** @var User[] $members */
	$members = $members_dp->getData();
?>

<?php $this->widget('bootstrap.widgets.TbPager', array("pages" => $members_dp->pagination)); ?>

<?php if($this->book->can("membership")): ?>
	<form method="post" action="<?=$book->url("members_manage"); ?>" id="members_manage">
	<input type="hidden" name="status" value="" />
	<?php if(isset($_GET["User_page"])): ?>
		<input type="hidden" name="User_page" value="<?php echo (int) $_GET["User_page"]; ?>" />
	<?php endif ?>
<?php endif ?>

<table id="people" class="table table-condensed table-striped">
<thead>
	<tr>
		<th>#</th>
		<th>Потребител</th>
		<th>Версии на превода</th>
		<th>Рейтинг</th>
		<th>Среден рейтинг</th>
	</tr>
</thead>
<tbody>
<?php
	$pos = $members_dp->pagination->currentPage * $members_dp->pagination->pageSize;
	foreach($members as $member) {
		$pos++;
		echo "<tr>";
		$o = array("data-id" => $member->id, "data-status" => $member->membership->status);
		echo CHtml::tag("td", $o, "{$pos}.");

		echo "<td>";
		if($member->membership->status == GroupMember::MODERATOR) echo "<i class='icon-briefcase' title='Модератор'></i> ";
		echo $member->ahref;
		if($member->id == $book->owner_id) echo " (создатель)";
		elseif($member->membership->status == GroupMember::CONTRIBUTOR and $book->facecontrol != Book::FC_OPEN) echo " (не е в групата)";
		elseif($member->membership->status == GroupMember::BANNED) echo " (баннат)";
		echo "</td>";

		echo "<td>";
		if($book->can("trread")) echo "<a href='" . $member->getUrl("translations/{$book->id}") . "'>";
		echo $member->membership->n_trs;
		if($book->can("trread")) echo "</a>";
		if($book->n_vars > 0 and $member->membership->n_trs > 0) printf(" <small>(%d%%)</small>", $member->membership->n_trs / $book->n_vars * 100);
		echo "</td>";

		echo "<td>";
		if($member->membership->rating < 0) echo "<span class='negative'>";
		echo $member->membership->rating;
		echo "</span>";
		echo "</td>";

		echo "<td" . ($member->membership->rating < 0 ? " class='negative'" : "") . ">";
		if($member->membership->n_trs != 0) printf ("%.2f", $member->membership->rating / $member->membership->n_trs);
		echo "</td>";

		echo "</tr>";
	}
?>
</tbody>
</table>

<?php $this->widget('bootstrap.widgets.TbPager', array("pages" => $members_dp->pagination)); ?>

<?php if($this->book->can("membership")): ?>
	<div id="inquisition">
		<?php if($this->book->facecontrol != Book::FC_OPEN): ?>
			<button onclick="GM.status_set(0)"  type="button" class="btn btn-inverse"><i class="icon-remove icon-white"></i> Изгонване</button>
		<?php endif ?>
		<button onclick="GM.status_set(-1)" type="button" class="btn btn-inverse"><i class="icon-ban-circle icon-white"></i> Бан / Сваляне на бана</button>
		<?php if($this->book->owner_id == Yii::app()->user->id): ?>
			<button onclick="GM.status_set(2)" type="button" class="btn btn-inverse"><i class="icon-fire icon-white"></i> Назначаване / Разжалване на модераторите</button>
		<?php endif ?>
	</div>
	<div id="inquisition-actions">
		<p><strong class='note'></strong></p>
		<button type="submit" class="btn btn-danger">Ok</button>
		<button type="button" onclick="GM.cancel()" class="btn btn-success">Отмяна</button>
	</div>
	</form>
<?php endif; ?>





<?php if($this->book->can("membership") and $this->book->facecontrol != Book::FC_OPEN): ?>
<h2>Покана на преводачи</h2>
<?php if($this->book->n_invites <= 0) { ?>
	<p class="info">
		Днес не можете да каните повече хора в този превод.
	</p>
<?php } else { ?>
	<form method="post" class="form-inline">
		<input type="text" name="invite" class="span4" />
		<input type="submit" value="Пригласить" class="btn" />
		<p class="help-block">
			Можете да укажете няколко ника през запетая. Днес можете да изпратите още
			<?=Yii::t("app", "<b>{n}</b> покана|<b>{n}</b> покани|<b>{n}</b> покани", $this->book->n_invites); ?>.
		</p>
	</form>
<?php } ?>

<?php if($invited_dp->totalItemCount > 0) { ?>
	<h3>Вече поканени</h3>
	<p class="userlist">
	<?php
		$cnt = 0;
		foreach($invited_dp->getData() as $invited) {
			if($cnt++) echo ", ";
			$title = "Поканил {$invited->from_login} " . Yii::app()->dateFormatter->formatDateTime($invited->cdate, "medium", "short");
			echo "<a href='{$invited->url}' class='user' title='{$title}'>{$invited->login}</a>";
		}
	?>
	</p>
<?php } ?>
<?php endif; ?>





<?php if($this->book->can("membership") and $this->book->facecontrol == Book::FC_CONFIRM and $queue_dp->totalItemCount > 0): ?>
<h2>Заявки за встъпване в групата</h2>
<form method="post">
<table class="table table-condensed table-striped table-oneline ">
<thead><tr>
	<th>Дата на заявката</th>
	<th>Приемане</th>
	<th>Отказ</th>
	<th>Ник</th>
	<th class="main">Съобщение</th>
</tr></thead>
<?php
	foreach($queue_dp->getData() as $member) {
		echo "<tr>";
		echo "<td>" . Yii::app()->dateFormatter->formatDateTime($member->q_cdate, "medium", "short") . "</td>";
		echo "<td class='c'><input type='radio' name='fate[{$member->id}]' value='accept' /></td>";
		echo "<td class='c'><input type='radio' name='fate[{$member->id}]' value='decline' /></td>";
		echo "<td>{$member->ahref}</td>";
		echo "<td class='t'>" . $member->q_message . "</td>";
		echo "</tr>";
	}
?>
<tr>
	<td></td>
	<td colspan="2" class='c'>
		<button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Ok</button>
	</td>
	<td></td>
	<td></td>
</tr>
</table>
</form>
<?php endif; ?>
