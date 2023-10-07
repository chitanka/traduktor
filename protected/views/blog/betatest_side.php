<div class="tools">
    <h5>Строеж</h5>
	<p>Този блог е достъпен:</p>
	<?php
		foreach(WebUser::getRoles("betatest") as $i => $login) {
			if($i) echo ", ";
			echo "<a href='/users/go?login={$login}'>{$login}</a>";
		};
	?>
</div>
