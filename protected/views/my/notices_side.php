<?php
	/**
	 * @var integer[] $new_ids
	 */
?>
<div class="tools">
	<h5>Известия</h5>
	<ul class="nav nav-pills">
		<li <?=isset($_GET["new"]) && $_GET["new"] == 1 ? "" : "class='active'"; ?>><a href="/my/notices">всички</a></li>
		<li <?=isset($_GET["new"]) && $_GET["new"] == 1 ? "class='active'" : ""; ?>><a href="?new=1">само нечетените</a></li>
	</ul>

	<form method="post" action="/my/notices_rmseen">
		<input type="hidden" name="really" value="1" />
		<?php
			if(is_array($new_ids)) foreach($new_ids as $id) echo "<input type='hidden' name='x[]' value='{$id}' />";
		?>
		<button type="submit" class="btn"><i class="icon-remove"></i> Премахни всички прочетени</button>
	</form>
</div>
