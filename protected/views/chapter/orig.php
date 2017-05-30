<?php
	/**
	 * @var Chapter $chap
	 * @var GenOptions $options
	 */
	$this->pageTitle = "Сваляне на оригиналните субтитри {$chap->book->fullTitle}: {$chap->title}";
?>
<h1>Сваляне на оригиналните субтитри <?php echo "{$chap->book->s_title}: {$chap->title}"; ?></h1>

<form method="get" action="<?=$chap->getUrl("orig_download"); ?>" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">Формат:</label>
		<div class="controls">
			<select name="format">
				<?php
				foreach(GenOptions::$format_options[$chap->book->typ] as $k => $v) {
					echo "<option value='{$k}'" . ($k == $options->format ? " selected" : "") . ">{$v}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Кодировка:</label>
		<div class="controls">
			<select name="enc">
				<?php
				foreach(Yii::app()->params["encodings"] as $k => $v) {
					echo "<option value='{$k}'" . ($k == $options->enc ? " selected" : "") . ">{$v}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">Пренасяне на редовете:</label>
		<div class="controls">
			<select name="crlf">
				<?php
				foreach(GenOptions::$crlf_options as $k => $v) {
					echo "<option value='{$k}'" . ($k == $options->crlf ? " selected" : "") . ">{$v}</option>";
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn btn-primary">
			<i class="icon-download-alt icon-white"></i> Сваляне
		</button>
		<a href="<?=$chap->book->url; ?>" class="btn">Към съдържанието</a>
		<a href="<?=$chap->url; ?>" class="btn">Превод на главата</a>
	</div>

</form>
