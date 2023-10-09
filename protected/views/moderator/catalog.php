<?php
	$this->pageTitle = "Модерация: структура каталога";
?>
<style type="text/css">
	#Tree div.n {padding:1px 4px}
	#Tree div.current {background:#444; color:#fff;}
	#Tree div.current a {color:#fff;}
	#Tree div a.c {display:none;}
	#Tree div:hover a.c {display:inline;}

	#Tree.swap a {cursor:w-resize;}

	#swap, #chpid {display:none;}
</style>
<script type="text/javascript">
	var T = {
		cur: 0,
		save_html: "",

		init: function() {
			$("#Tree a.c").attr("title", "Създаване на подраздел");
//		console.log("T.init()");
//		$("#Tree a.t").click(T.select);
		},
		ed: function(id) {
			var $div = $("#n" + id);
			console.dir($div);
			var id = $div.attr("id").substr(1);
			var title = $("#n" + id + " a.t").text();

			console.log("T.select(), id = %d, text = '%s'", id, title);

			var $prev_div = $("#n" + T.cur);
			$prev_div.removeClass("current");
			if(T.cur != 0) $prev_div.html(T.save_html);
			$div.addClass("current");

			if(id != 0) {
				var html =
					"<form method='post' id='ed' class='form-inline'>" +
						"<input type='text' name='title' class='' /> " +
						"<input type='submit' value='запазване' /> " +
						"<input type='button' value='подраздел' /> " +
						"</form>";

				T.save_html = $div.html();
				$div.html(html);
				$("#ed [name=title]").val(title);
			}

			T.cur = id;
			return false;
		},
		sel2_html: "",
		sel2_selector: "",
		sel2_start: function(selector) {
			T.sel2_selector = selector;
			$("#edit").hide();
			$(T.sel2_selector).show();

			$("#Tree").addClass("swap");

			$("#Tree a").click(function(e) {
				e.preventDefault();

				var res = /edit=(\d+)/.exec($(this).attr("href"));
				if(!res) return false;
				$(T.sel2_selector + " [name=id2]").val(res[1]);
				$(T.sel2_selector).submit();

				return false;
			})
		},
		sel2_cancel: function() {
			$("#edit").show();
			$(T.sel2_selector).hide();
			$("#Tree").removeClass("swap");
			$("#Tree a").unbind("click");
		}
	}
	$(T.init);
</script>

<?php $this->renderPartial("_header"); ?>

<h1>Каталог</h1>

<div class="row">
	<div class="span8">
		<ul id="Tree">
			<li>
				<div id="n0" class="n<?=!$edit_node->id ? " current" : ""; ?>">
					<a href="/moderator/catalog">Каталог</a>
				</div>
				<?php
				$prev_indent = 0;
				foreach($categories as $cat) {
					$indent = count($cat->mp);

					if($indent > $prev_indent) {
						echo "\n<ul>\n";
					} else {
						echo str_repeat("</li>\n</ul>\n", $prev_indent - $indent) . "</li>\n";
					}
					echo "<li>";

					echo "<div id='n{$cat->id}' class='n" . ($cat->id == $edit_node->id ? " current" : "") . "'>";
					echo "<a href='?edit={$cat->id}' class='t'>{$cat->title}</a>";
					if(!$cat->available) echo " (x)";
					if($cat->booksCount > 0) echo " ({$cat->booksCount})";

//					echo " <span style='color:#2b6'>[";
//					echo join(",", $cat->path_id);
//					echo " - ";
//					echo join(",", $cat->path_title);
//					echo "]</span>";

					echo "</div>";

					$prev_indent = $indent;
				}
				echo str_repeat("</li>\n</ul>\n", $indent);
				?>
			</li>
		</ul>
	</div>

	<div class="span4">
		<?php if($edit_node->id) { ?>
		<?php
			/** @var TbActiveForm $form */
			$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
				"id" => "edit",
				"type" => "vertical",
				"inlineErrors" => false,
				"action" => "/moderator/catedit/id/{$edit_node->id}",
			));

			echo "<h3>Свойства на раздела</h3>";
			echo $form->textFieldRow($edit_node, "title", array("placeholder" => "Название", "class" => "span3"));
			echo $form->checkBoxRow($edit_node, "available");
		?>
		<div style='margin-bottom:5px'>
			<input type='submit' value='Запис' class='btn btn-success' />
			<input type='button' value='Изтриване' class='btn btn-danger' onclick='if(confirm("Да се изтрие ли разделът?")) $("#remove").submit()' />
		</div><div>
			<input type='button' value='Смяна на местата' class='btn' onclick="T.sel2_start('#swap')" />
			<input type='button' value='Преместване' class='btn' onclick="T.sel2_start('#chpid')" />
		</div>
		<?php $this->endWidget(); ?>

		<p>
			<a href="<?=$cat->url; ?>">В каталога &rarr;</a>
		</p>

		<form method='post' action='/moderator/catremove/id/<?=$edit_node->id; ?>' id="remove"></form>

		<form method="post" action="/moderator/catswap/id/<?=$edit_node->id; ?>" id="swap">
			<p>
				Изберете раздела, с който да смените местата с текущия. Имайте предвид, че той трябва да има същото ниво на вложеност.
				Подразделите ще бъдат пренесени съответно.
			</p>
			<input type="hidden" name="id2">
			<input type="button" value="Отмяна" class="btn" onclick="T.sel2_cancel()" />
		</form>

		<form method="post" action="/moderator/chpid/id/<?=$edit_node->id; ?>" id="chpid">
			<p>
				Изберете раздел, който ще стане новият "родителски" раздел за избрания.
			</p>
			<input type="hidden" name="id2">
			<input type="button" value="Отмена" class="btn" onclick="T.sel2_cancel()" />
		</form>

		<?php } ?>

		<h3>Нов подраздел</h3>
		<?php
			/** @var TbActiveForm $form */
			$kitten = new Category();
			$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
				"id" => "kitten",
				"type" => "vertical",
				"inlineErrors" => false,
				"action" => "/moderator/kitten/pid/{$edit_node->id}",
			));

			echo $form->textFieldRow($kitten, "title", array("placeholder" => "Заглавие", "class" => "span3"));
			echo $form->checkBoxRow($kitten, "available");
		?>
		<div>
			<input type='submit' value='Създаване не подраздел' class='btn' />
		</div>
		<?php $this->endWidget(); ?>

		<p>
			Ако преди да промените нещо, страницата е била отворена дълго в бразуъра, опреснете я,
			за да се убедите, че другите модератори не са променили нещо преди вас.
		</p>
	</div>

</div>
