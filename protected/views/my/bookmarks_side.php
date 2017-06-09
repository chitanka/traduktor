<div class='tools'>
	<h5>Отметки</h5>

	<p>
		За да промените названието или да изтриете отметка, преместете курсора върху нея и натиснете <i class='icon-edit'></i>.
		За да промените реда на отметките, ги влачете с мишката.
	</p>

	<p id="bookmarks-mass-rm">
		<button class="btn btn-danger" onclick="E.mass_rm()"><i class="icon-remove-sign icon-white"></i> Масово премахване</button>
	</p>
	<div id="bookmarks-mass-rm-on" class="hide">
		<p>Цъкнете върху отметките, които искате да премахнете.</p>
		<form method="post" action="/my/bookmark_rm" id="form-mass-rm">
			<button type="submit" class="btn btn-danger" onclick="E.mass_rm_go()" disabled><i class="icon-remove-sign icon-white"></i> Премахни отбелязаните!</button>
			<button type="button" class="btn" onclick="E.mass_rm_cancel()"><i class="icon-remove"></i> Отказ</button>
		</form>
	</div>
</div>
