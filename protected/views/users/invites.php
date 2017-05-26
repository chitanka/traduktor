<?php
/**
 * @var UsersController $this
 * @var User $user
 * @var RegInvite $invite
 * @var RegInvite[] $sent
 */
$this->pageTitle = "Покани";
$this->renderPartial("profile_head", array("user" => $user, "h1" => "покани"));
?>

<?php if($user->n_invites == 0): ?>
	<p>Нямате покани.</p>
<?php else: ?>
<p>
	Вие може да поканите в клуба ни още <?=Yii::t("app", "{n} човек|{n} човека|{n} човека", $user->n_invites); ?>.
</p>
<form method="post" class="form-horizontal" id="invite-send">
	<input type="hidden" name="invite[type]" value="new">
	<h4>Кого искате да поканите?</h4>

	<?=CHtml::errorSummary($invite, '<div class="alert alert-box alert-danger">', '</div>'); ?>

	<div id="invite-send-more">
		<p>
			<span class="invite-who invite-who-new">E-mail:</span>
			<input type="text" name="invite[clue]" value="<?=CHtml::encode($invite->clue); ?>">
		</p>

		<p>
			<label>Ако желаете, можете да добавите нещо от себе си към писмото с поканата:</label>
			<textarea name="invite[message]" rows="4" style="width:100%"><?=CHtml::encode($invite->message); ?></textarea>
		</p>
		<p>
			<button type="submit" class="btn btn-success">Поканете</button>
		</p>

	</div>
</form>
<?php endif; ?>

<?php if(count($sent) > 0): ?>
<h4>Изпратени покани</h4>
<table class="table table-bordered table-striped" id="sent">
<?php
foreach($sent as $inv) {
	echo "<tr data-id='{$inv->id}'>";
	echo "<td>" . Yii::app()->dateFormatter->format("dd.MM.yyyy HH:mm", $inv->cdate) . "</td>";
	echo "<td>";
	if($inv->to_id) echo $inv->buddy->ahref;
	else echo $inv->to_email;
	echo "</td>";
	echo "<td>";
	echo "<a href='#' class='btn btn-small revoke'><i class='icon icon-remove'></i> отозвать</a> ";
	echo "<a href='#' class='btn btn-small resend'><i class='icon icon-envelope'></i> ещё раз</a> ";
	echo "<a href='#' class='btn btn-small code'><i class='icon icon-leaf'></i> получить код</a> ";
	echo "</td>";
}
?>
</table>
<form id="form-revoke" method="post"><input type="hidden" name="revoke"></form>
<form id="form-resend" method="post"><input type="hidden" name="resend"></form>

<div id="modal-code" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Изпращане на покана</h3>
	</div>
	<div class="modal-body">
		<p>
			Вие можете да изпратите на ваш колега препратка за регистрация, например в социална мрежа,
			или да му я продиктувате по телефона.
		</p>
		<p id="code-code">

		</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">Ok</a>
	</div>
</div>

<script type="text/javascript">
	(function() {
		$("#sent").on("click", "a.revoke", function(e) {
			e.preventDefault();
			if(!confirm("Сигурни ли сте, че искате да отмените  тази покана?")) return;
			$("#form-revoke [name=revoke]").val($(this).parents("tr").data("id"));
			$("#form-revoke").submit();
		}).on("click", "a.resend", function(e) {
			e.preventDefault();
			if(!confirm("Повторно изпращане на поканата?")) return;
			$("#form-resend [name=resend]").val($(this).parents("tr").data("id"));
			$("#form-resend").submit();
		}).on("click", "a.code", function(e) {
			e.preventDefault();
			var $modal = $("#modal-code"), $codeP = $("#code-code");

			$.get(
				'/users/' + User.id + '/inviteCode',
				{iid: $(this).parents("tr").data("id")},
				function(data) {
					var $input = $('<textarea id="code-code" class="span5" rows="3">')
						.val(data)
						.on("focus", function() { $(this).select(); });
					$codeP.html('').append($input);

					$modal.modal('show').on('shown', function() { $input.focus(); });
				}
			);
		});
	})();
</script>


<?php endif ?>
