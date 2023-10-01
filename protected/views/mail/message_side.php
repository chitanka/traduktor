<?php
	/**
	 * @var Mail $message
	 */
?>
<div class="tools">
	<h5>Писмо</h5>
	<?php
		echo "<p>";
		echo $message->folder == Mail::INBOX ? "От: " : "Получател: ";
		echo $message->buddy->ahref . ", " . Yii::app()->dateFormatter->formatDateTime($message->cdate, "medium", "short");
		echo "</p>";
	?>

	<p><a href="/my/mail?folder=<?=$message->folder; ?>">Към списъка със съобщения</a></p>

	<button type="button" class="btn btn-danger btn-small" onclick="P.rm()"><i class="icon-remove-circle icon-white"></i> Изтриване</button>
	<button type="button" class="btn btn-primary btn-small" onclick="P.unseen()" title="Съобщението ще бъде маркирано като непрочетено"><i class="icon-eye-close icon-white"></i> Не съм го чел!</button>
	<?php if($message->folder == Mail::INBOX): ?>
		<a href="/my/mail/write?reply=<?=$message->id; ?>" class="btn btn-success btn-small" onclick="P.re()"><i class="icon-bullhorn icon-white"></i> Отговор</a>
	<?php endif; ?>
</div>
