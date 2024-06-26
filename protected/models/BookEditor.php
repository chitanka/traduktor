<?php
class BookEditor extends Book {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public $rm_img, $new_img;
	public $ac_read = "a", $ac_trread = "a", $ac_gen = "a", $ac_rate = "a", $ac_comment = "a", $ac_tr = "g";
	public $ac_blog_r = "a", $ac_blog_c = "a", $ac_blog_w = "a";
	public $ac_announce = "g", $ac_membership = "m", $ac_chap_edit = "o", $ac_book_edit = "o";

	public function rules() {
		return array_merge(parent::rules(), array(
			array("typ", "in", "range" => array_keys(Yii::app()->params["book_types"]), "message" => "Грешен тип на превода."),
			array("s_lang, t_lang", "numerical", "integerOnly" => "true"),
			array("s_title, t_title", "required"),
			array("s_title, t_title", "clean"),
			array("descr", "safehtml"),
			array("rm_img", "boolean"),
			array("new_img", "file", "allowEmpty" => true, "types" => "jpg, gif, png, jpeg", "wrongType" => "Грешен формат на файла. Молим, качвайте JPG, PNG или GIF"),

			array("ac_read, ac_trread, ac_rate, ac_comment, ac_gen, ac_tr, ac_blog_r, ac_blog_c, ac_blog_w", "in", "range" => array("a", "g", "m", "o")),
			array("ac_announce", "in", "range" => array("g", "m", "o")),
			array("ac_chap_edit, ac_book_edit, ac_membership", "in", "range" => array("m", "o")),
		));
	}

	public function not_in_new($attr, $params) {
		Yii::log("not_in_new {$attr}: '{$this->$attr}'");
		if(empty($this->$attr)) return;
		if(!$this->isNewRecord) {
			$this->addError($attr, $this->getAttributeLabel("typ") . " Може да се отбележат само при създаване на превод.");
		}
	}
	public function clean($attr, $params) {
		$this->$attr = trim(htmlspecialchars(strip_tags($this->$attr, ENT_QUOTES | ENT_HTML5)));
	}

	public function safehtml($attr, $params) {
		$p = new CHtmlPurifier();
		$p->options = Yii::app()->params["HTMLPurifierOptions"];
		$this->$attr = trim($p->purify($this->$attr));
	}

	public function attributeLabels() {
		return array(
			"typ" => "Формат",
			"s_title" => "Заглавие на езика на оригинала",
			"t_title" => "Заглавие на езика на превода",
			"s_lang" => "Език на оригинала",
			"t_lang" => "Език на превода",
			"descr" => "Анотация",
			"topics" => "Рубрики от каталога:",
			"new_img" => "Изображение в съдържанието",
			"rm_img" => "Изтриване",

			"ac_read" => "Четене",
			"ac_rate" => "Оценяване",
			"ac_comment" => "Коментиране",
			"ac_gen" => "Качване",

			"ac_tr" => "Превеждане",

			"ac_blog_r" => "Да чете блога",
			"ac_blog_c" => "Да коментирова в блога",
			"ac_blog_w" => "Да пише в в блога",

			"ac_chap_edit" => "Да редактира оригинала",
			"ac_book_edit" => "Да редактира описанието на превода",
			"facecontrol" => "Участие в групата",
		);
	}

	private function imgUnlink() {
		if($this->img[0] == 0) return false;
		@unlink($this->imgPath);
		$this->img = array(0, 0, 0);
	}

	private function imgCheckDir() {
		if(!is_dir($this->imgDir)) mkdir($this->imgDir);
	}

	protected function afterValidate() {
		// Аватар
		if($this->rm_img) $this->imgUnlink();

		if($this->new_img) {
			// стереть старую фотку, если есть
			$this->imgUnlink();
			$this->imgCheckDir();

			$this->img[0] = rand(1, 32000);

			$R = new ImgResizer();
			list($this->img[1], $this->img[2]) = $R->resize($this->new_img->tempName, $this->imgPath, 200, 500);
		}

		parent::afterValidate();
	}
}
?>
