<?php
class TextSource extends CFormModel {
	public $src_type = 1, $text, $file, $encoding, $url, $chopper;

	public $choppers = array(1 => "през един ред", 2 => "през два реда", 0 => "не разбивай нищо, ще го направя ръчно");
	public $choppers_delim = array(1 => "\n", 2 => "\n\n");

	public function rules() {
		return array(
			array("src_type", "in", "range" => array(1, 2)),
			array("text", "safe"),
			array("file", "safe"),
			array("encoding", "in", "range" => array_keys(Yii::app()->params["encodings"])),
			array("url", "safe"),
			array("chopper", "in", "range" => array_keys($this->choppers)),
		);
	}

	public function attributeLabels() {
		return array(
			"file" => "Файл",
			"chopper" => "Разбиване на части",
			"encoding" => "Кодиране",
		);
	}

	protected function afterValidate() {
		switch($this->src_type) {
			case 1:
				$this->text = str_replace("\r", "", $this->text);
				break;
			case 2:
				// Читаем файл
				$file = CUploadedFile::getInstanceByName("TextSource[file]");
				$this->text = file_get_contents($file->tempName, false, null, -1, 500 * 1024);
				if($this->text === false) {
					$this->addError("file", "Файлът не се зареди. Възможно да е прекалено голям.");
					return false;
				}

				// Кодировка
				if($this->encoding != "UTF-8") {
					$this->text = iconv($this->encoding, "UTF-8//IGNORE", $this->text);
				} elseif(!mb_check_encoding($this->text, "utf-8")) {
					$this->addError("encoding", "Грешен енкодинг на текста, изберете правилния.");
				}
				break;
			case 3:
				exit;
				if(!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->url)) {
					$this->addError("url", "Грешен URL. Трябва да бъде нещо от сорта на http://someawesomesite.ru/foo/bar/baz.html");
					return false;
				}

				echo "<pre>";
				echo "Download text from '{$this->url}'\n";

				$html = Yii::app()->curl->run($this->url);
				file_put_contents("import.html", $html);

				print_r(Yii::app()->curl->info);
				echo "<hr />";

				// Пытаемся узнать кодировку, если её не указали явно
				if(preg_match('#<meta.*http-equiv=[\'"]?content-type[\'"]?.*>#is', $html, $res)) {
					echo "Got meta http-equiv: " . htmlspecialchars($res[0]) . "\n";
				} elseif(preg_match('#<meta.*charset=\'content-type\'.*>#is', $html, $res)) {
					echo "Got meta charset: " . htmlspecialchars($res[0]) . "\n";
				} elseif(Yii::app()->curl->info["content_type"] != "" && preg_match('#charset=([^; ]+)#i', Yii::app()->curl->info["content_type"], $res)) {
					echo "Found content-type header with charset: {$res[0]}\n";
					$charset = $res[1];
				} else {
					echo "Can't find encoding information (assume ISO)\n";
					$charset = "ISO−8859−1";
				}
				$charset = strtoupper($charset);
				$this->encoding = $charset;

				if($charset != "UTF-8" && $charset != "UTF8") {
					$html = iconv($charset, "UTF-8", $html);
				} elseif(!mb_check_encoding($html, "utf-8")) {
					$this->addError("encoding", "Грешен енкодинг на текста, изберете правилния.");
				}

				$html = preg_replace('#<head>.+</head>#isU', '', $html);
				$html = preg_replace('#<script[^>]*>.+</script[^>]*>#isU', '', $html);
//				$html = preg_replace('#<style[^>]*>.+</style[^>]*>#isU', '', $html);

				echo "<b>HTML</b> = '" . htmlspecialchars($html) . "'";

				$p = new CHtmlPurifier();
				// идея в том, чтобы оставить только блочные теги, а потом по ним разбить на фрагменты
				$html = preg_replace('/\s+/s', " ", $html);        // привели всё к одной строке
				$html = preg_replace('#</[^>]*>#s', " ", $html);   // убрали все закрывающие теги
				$html = preg_replace('/<(p|div|li|dd|dt|h\d|address|blockquote)[^>]*>/s', "\n\n", $html); // все открывающие блочные теги - в два переноса строки
				$html = preg_replace('/<br[^>]*>/s', "\n", $html); // br - в одинарный перенос строки
				$html = strip_tags($html);
				$html = preg_replace('/[ \t]+/', " ", $html);

				echo "</pre>";

				$this->text = $html;
				$this->chopper = 2;

				break;
			default:
				$this->addError("src_typ", "Системна грешка. Обърнете се към техническата поддръжка.");
				return false;
				break;
		}

		$this->text = trim($this->text);

		if($this->text == "") {
			$this->addError("src_typ", "Текстът не е открит. Понякога става така, когато избереш грешния енкодинг.");
			return false;
		}

		if(mb_strlen($this->text) > 500 * 1024) {
			$this->addError("src_typ", "Текстът е прекалено голям. Молим, разбийте го на няколко глави, не по-голями от 500 КБ всяка.");
			return false;
		}

		return parent::afterValidate();
	}

	/**
	 * @param Chapter $chap
	 */
	public function prepareText($chap) {
		if($this->chopper != 0) {
			$text = explode($this->choppers_delim[$this->chopper], $this->text);
			$n_long = 0;
			foreach($text as $i => $p) {
				if(trim($p) == "") unset($text[$i]);
				if(mb_strlen($p) > 1024) {
					$n_long++;
				};
			}

			$n_verses = count($text);
		} else {
			$text = array($this->text);
			$n_verses = 1;
			if(mb_strlen($this->text) > 1024) $n_long = 1;
			else $n_long = 0;
		}

		$warnings = array();
		if($n_long > 0)
			$warnings[] = "В текста има <strong>" . Yii::t("app", "{n} прекалено дълъг абзац.|{n} прекалено дълги абзаци.|{n} прекалено дълги абзаци", $n_long) . "</strong>, те са отделени с червена линия вляво. Ще бъдат неудобни за превод, разбийте ги на по-малки парчета.";
		if(count($text) > 5000)
			$warnings[] = "Текстът се разби на <strong>" . Yii::t("app", "{n} фрагмента|{n} фрагмента|{n} фрагмента", $n_verses) . "</strong>, възможно, ще е по-удобно ако разбиете текста на няколко глави.";
		if(count($warnings)) {
			Yii::app()->user->setFlash("warning", join("<br />", $warnings));
		}

		return $text;
	}

	public function getErrorsString() {
		$t = "";
		foreach($this->getErrors() as $field => $errors) {
			$t .= join("\n", $errors);
		}

		return $t;
	}

}
