<?php
	/**
	 * @var Mail $message
	 * @var User[] $buddies
	 */
?>
<div class="tools">
	<h5>Писане на писмо</h5>
	<?php
		if(count($buddies) > 0) {
			echo "Вече сте си писали с:";
			echo "<ul>";
			foreach($buddies as $buddy) {
				echo "<li><a href='/my/mail/write?to={$buddy->login}'>{$buddy->login}</a></li>";
			}
			echo "</ul>";
		}
	?>
</div>
