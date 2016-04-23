<?php
/**
 * @var integer n_users
 * @var integer n_books
 * @var integer n_orig
 * @var integer n_tr
 */

/**
* DEPRECATED - use Yii::t()
*/
function RusEnding($n, $n1, $n2, $n5) {
		if($n >= 11 and $n <= 19) return $n5;
		$n = $n % 10;
		if($n == 1) return $n1;
		if($n >= 2 and $n <= 4) return $n2;
		return $n5;
	}
?>

<div class='tools'>
	<h5>Найти человека</h5>
	<form method="get" action="/users/go" class="form-inline">
		Ник: <input type='text' name='login' size='25' class='span2' />
		<input type='submit' value='&raquo;' class='btn' />
	</form>
</div>
