<?php
class SearchFilter extends CFormModel {
	public $t = "", $cat = 0, $s_lang = 0, $t_lang = 0, $ready = false, $gen = false, $tr = false, $sort = 4;
	public $topic = 0;
	public $category = null;

	public static $sortOptions = array(
		0 => "По степен на завършеност",
		1 => "По името на оригинала",
		2 => "По името на езика на превода",
		3 => "По дата на създаване",
		4 => "По дата на последна активност",
	);

	public static $sortSQL = array(
		0 => "ready(t.n_verses, t.d_vars) desc",
		1 => "t.s_title",
		2 => "t.t_title",
		3 => "t.cdate desc",
		4 => "t.last_tr desc NULLS LAST",
	);

	public function beforeValidate() {
		foreach(array("cat", "s_lang", "t_lang", "ready", "gen", "tr", "sort") as $k) $this->$k = (int) $this->$k;
		if($this->cat) {
			$this->category = Category::model()->findByPk($this->cat);
		}
		return true;
	}

	public function getFilterTitle($attr) {
		$html = array(
			"t" => "<a>Названието или описанието съдържа текст</a>",
			"cat" => '<a href="#" onclick="return S.catChoose()">От раздела на каталога</a>',
			"s_lang" => "<a>Език на оригинала</a>",
			"t_lang" => "<a>Език на превода</a>",
			"ready" => "<a>100% готови</a>",
			"gen" => "<a>Достъпни за сваляне</a>",
			"tr" => "<a>Достъпни за превод</a>",
		);
		return $html[$attr];
	}

	public function getFilterHtml($attr) {
		if($attr == "cat") {
			if(!$this->category) return "";
			return '<input type="hidden" name="cat" value="' . $this->category->id . '" />От раздела на каталога &laquo;<span class="name">' . $this->category->title . '</span>&raquo;';
		}
		$html = array(
			't' => '<input type="text" name="t" class="span8" />',
			'cat' => '<input type="hidden" name="cat" /От раздела на каталога &laquo;<span class="name"></span>&raquo;',
			's_lang' => 'Език на оригинала: <select name="t_lang">' . Yii::app()->langs->options(Langs::FORM_INF) . '</select>',
			't_lang' => 'Език на превода: <select name="t_lang">' . Yii::app()->langs->options(Langs::FORM_INF) . '</select>',
			'ready' => '<label><input type="checkbox" name="ready" value="1" checked /> Готови на 100%</label>',
			'gen' => '<label><input type="checkbox" name="gen" value="1" checked /> Достъпни за сваляне</label>',
			'tr' => '<label><input type="checkbox" name="tr" value="1" checked /> Достъпни за превод</label>',
		);
		return $html[$attr];
	}

	public function getHasOptions() {
		return  $this->cat || $this->s_lang || $this->t_lang || $this->ready || $this->gen || $this->tr;
	}

	public function getDoSearch() {
		return !empty($this->t) || !empty($this->cat) || !empty($this->s_lang) || !empty($this->t_lang);
	}

	public function rules() {
		return array(
			array("t", "filter", "filter" => "strip_tags", "on" => "search"),
			array("t", "filter", "filter" => "trim", "on" => "search"),
			array("cat", "numerical", "integerOnly" => true),
			array("s_lang, t_lang", "numerical", "integerOnly" => "true"),
			array("gen, tr", "boolean"),
			array("ready", "boolean", "on" => "search"),
			array("ready", "numerical", "integerOnly" => true, "on" => "announces"),
			array("sort", "in", "range" => array_keys(self::$sortOptions), "on" => "search"),
			array("topic", "numerical", "integerOnly" => true, "on" => "announces"),
		);
	}

	public function attributeLabels() {
		return array(
			"t" => "Заглавие",
			"s_lang" => "Език на оригинала",
			"t_lang" => "Език на превода",
			"cat" => "От раздела на каталога",
		);
	}
}
?>
