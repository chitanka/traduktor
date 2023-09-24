<style type="text/css">
#join_btn {display:block;}
#join_msg {display:none;}
</style>
<?php
	if(Yii::app()->user->isGuest) {
		echo "Трябва да се регистрирате или влезете в сайта, за да подадете заявка за влизане в групата.";
	} else {
?>
	<form method="post" action="<?=$book->getUrl("members_join"); ?>" class="well form-horizontal" style="margin-top:10px;">
		<div id="join_btn">
			<button type="button" onclick="$('#join_btn').hide(); $('#join_msg').show(); $('#join [name=message]').focus();" class="btn btn-success" />
			<i class="icon-plus icon-white"></i> Встъпване в групата
			</button>
		</div>
		<div id="join_msg">
			<label>
				Вашата заявка ще бъде разгледа първо от <?php echo $book->ac_membership == "m" ? "модераторите" : "създателя на превода"; ?>.
				Можете да им напишете кратко съобщение:
			</label>
			<input type="text" name="message" maxlength="200" class="span4" />
			<button type="submit" class="btn" /><i class="icon-ok"></i> Изпращане на заявка</button>
			<button type="button" class="btn" onclick="$('#join_btn').show(); $('#join_msg').hide(); $('#join_msg [name=message]').focus();"><i class="icon-remove"></i> Отмяна</button>
		</div>
	</form>
<?php
	}
?>
