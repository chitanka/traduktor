<?php
	/**
	 * @var Book[] $hot
	 * @var String $searchTop
	 * @var Announce[] $announces
	 * @var BlogPost[] $blog
	 */

	Yii::app()->clientScript->registerCssFile("/css/face.css?1");

	$this->pageTitle = "Система за колективни преводи";
?>
<div class="row">
	<div class="span7" id="f-hot">
		<h2>
			Превеждано в момента
			<a href="#" data-toggle="modal" data-target="#f-hot-ini" class="cog" title="Настроить внешний вид этого блока"><i class='icon-cog'></i></a>
		</h2>
		<?php
			function humanTime($t) {
				if($t < 60) return "{$t} сек.";
				if($t < 3600) return sprintf("%d мин. %d сек.", $t / 60, $t % 60);
				else return sprintf("%d час. %d мин.", $t / 3660, ($t / 60) % 60);
			}
			$ini = Yii::app()->user->ini;
			if(count($hot) == 0) {
				echo "<p class='alert alert-box alert-warning'>В момента нищо не се превежда от ";
				echo Yii::app()->langs->from_to($ini["hot.s_lang"], $ini["hot.t_lang"]);
				echo ". Можете да разгледате какво се превежда <a href='#' data-toggle='modal' data-target='#f-hot-ini'>на други езици</a>.</p>";
			} else {
				echo "<ul" . ($ini["hot.img"] ? " class='imged'" : "") . ">";
				foreach($hot as $book) {
					echo "<li>";
					$t = humanTime(time() - strtotime($book->last_tr));

					if($ini["hot.img"]) {
						echo "<div class='bimg'";
						if($book->img->exists) echo " style=\"background-image:url('" . $book->img->getUrl("5050") . "')\"";
						echo "></div>";

						echo "<p class='title'>";
						echo "<a href='{$book->url}' title='{$t}'>{$book->fullTitle}</a>";
						echo "</p><p class='info'>";
						echo Yii::app()->params["book_types"][$book->typ] . " ";
						echo Yii::app()->langs->from_to($book->s_lang, $book->t_lang) . " ";
						echo "<span class='r'>{$book->ready}</span>";
						echo "</p>";
					} else {
						echo Yii::app()->langs->from_to($book->s_lang, $book->t_lang, Langs::FORMAT_ABBR) . " ";
						echo "<a href='{$book->url}' title='{$t}'>{$book->fullTitle}</a> ";
					}

					echo "</li>";
				}
				echo "</ul>";
			}
		?>

		<div id="f-hot-ini" class="modal hide">
			<form method="post" class="form-horizontal" action="/site/ini" style="margin:0">
			<input type="hidden" name="area" value="hot" />
			<div class="modal-header" style='padding-bottom:0;'>
				<a class="close" data-dismiss="modal">×</a>
				<h3>Настройки на езиците</h3>
			</div>
			<div class="modal-body" style="max-height:350px">
				<div class="control-group">
					<label class="control-label">Език на оригинала:</label>
					<div class="controls"><select name="s_lang">
						<option value="0">Без значение</option>
						<?=Yii::app()->langs->options(Langs::FORM_INF, Yii::app()->user->ini["hot.s_lang"]); ?>
					</select></div>
				</div>

				<div class="control-group">
					<label class="control-label">Език на превода:</label>
					<div class="controls"><select name="t_lang">
						<option value="0">Без значение</option>
						<?=Yii::app()->langs->options(Langs::FORM_INF, Yii::app()->user->ini["hot.t_lang"]); ?>
					</select></div>
				</div>

				<div class="control-group">
					<div class="controls">
						<label class="checkbox"><input type="hidden" name="img" value="0" /><input type="checkbox" name="img" value="1" <?php if(Yii::app()->user->ini["hot.img"]) echo "checked"; ?>/> с изображения</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="subit" class="btn btn-success"><i class="icon-ok icon-white"></i> Запис</button>
				<button type="button" class="btn" data-dismiss="modal"><i class="icon-ban-circle"></i> Отмяна</button>
			</div>
			</form>
		</div>

	</div>
	<div class="span5" id="f-about">
		<div class='hero'>
			<h3>Какво е това тук?</h3>
			<p>
				Привет. Добре дошли в системата „Жеко“. Този сайт е предназначен за колективни преводи на всякакви текстове и субтитри на различни езици.
			</p>
			<h3>Как работи това?</h3>
			<p>
				Тестът се разбива на множество малки парчета (изречения, абзаци). Всеки участник в превода чете фрагмент на оригиналния език, мисли, и предлага
				свой вариант на превода. Ако точно този вариант се харесва на другите посетители, те му поставят „плюс“. Всички оценки се сумират
				и по такъв начин се определя кой вариант на превода е най-приятен за слуха. От най-добрите варианти се сглобява готовия превод. Получава се или много бързо, или много добре, или и двете.
				Също така на вашите услуги е <a href="/site/help">система за разделяне правата за достъп</a>, колективен блог на превода, коментиране на фрагменти, разнообразна статистика.
				Опитайте сами.
			</p>
			<h3>И още:</h3>
			<ol>
				<li>Да превеждаш нещо интересно в компанията на съмишленици е интересно и увлекателно занимание.</li>
				<li>Четенето и обсъждането на чужди варианти на преводите страхотно помага да изучите чужд език.</li>
				<li>Да гледате филм с оригинално озвучаване и български субтитри е далеч по-интересно. Опитайте!</li>
			</ol>
		</div>

		<div style="margin:10px auto; width:320px;">

		</div>

	</div>
</div>

<div id="f-search-top">
	<h2>
		<span rel='popover' data-content='Чем крупнее название, тем чаще его ищут на этом сайте.' data-title="Какво е това?">
		Популярни преводи
		</span>
		<script type='text/javascript'>$("span[rel=popover]").popover();</script>
	</h2>
	<p class="cloud"><?php echo $searchTop; ?></p>
	<p class="links">
		&rarr; <a href="/search">претърсване на преводите</a>
	</p>
</div>

<div class="row">
	<div id="f-announces" class="span7">
		<h2>
			Анонси относно преводите
		</h2>
		<?php
			foreach($announces as $announce) {
				$this->renderPartial("/announces/_announce", array("announce" => $announce));
			}
		?>
		<p class="links">
			&rarr; <a href="/announces">всички анонси</a>
		</p>
	</div>
<!--	<div id="f-blog" class="span5">-->
<!--		<h2>-->
<!--			Обсуждается в блоге-->
<!--			<span class="links">&rarr; <a href="/blog">весь блог</a></span>-->
<!--		</h2>-->
<!--		<ul>-->
<!--		--><?php
//			foreach($blog as $post) {
//				echo "<li>";
//				echo "<a href='{$post->url}'>{$post->title}";
//				if($post->n_comments > 0) echo " <span class='c'><i class='icon-nb-comment'></i> {$post->n_comments}</span>";
//				echo "</a>";
//				$b = strip_tags($post->body);
//				echo "<p>" . (mb_strlen($b) > 120 ? mb_substr($b, 0, 120) . "..." : $b) . "</p>";
//				echo "</li>";
//			}
//		?>
<!--		</ul>-->
<!--		<p class="links">-->
<!--			&rarr; <a href="/blog">остальные посты</a>-->
<!--		</p>-->
<!--	</div>-->
</div>
