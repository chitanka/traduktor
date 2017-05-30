<?php
class TrFilter extends CFormModel {
	public $show = 0, $to = "", $tt = "", $show_user = "";
	public $to_esc, $tt_esc;

	public static $modes = array(
		0 => "Всички",
		1 => "Непреведеното",
		7 => "С две и повече версии на превод",
		3 => "С коментари",
		4 => "С нови коментари",
		2 => "От преводача",
		5 => "Оригиналът съдържа",
		6 => "Преводът съдържа",
	);

	protected function beforeValidate() {
		$this->show = (int) $this->show;
		foreach(array("show_user", "to", "tt") as $k) $this->$k = trim($this->$k);

		$this->to_esc = preg_quote($this->to);
		$this->tt_esc = preg_quote($this->tt);

		return parent::beforeValidate();
	}

	public function rules() {
		return array(
			array("show", "numerical", "integerOnly" => true),
			array("to, tt, show_user", "safe"),
		);
	}

	public function attributeLabels() {
		return array(
			"show" => "Филтър",
			"to" => "Оригиналът съдържа",
			"tt" => "Преводът съдържа",
			"show_user" => "От преводача",
		);
	}

	public function getButtonTitle() {
		$ret = mb_strtolower(self::$modes[$this->show]);
		if($this->show == 2) $ret .= " {$this->show_user}";
		return CHtml::encode($ret);
	}

	public function getButton($orig_dp) {
		$html = "<a href='#' class='btn btn-small tb-filter' accesskey='F'";
		if($this->show) $html .= " title='{$this->buttonTitle}'";
		$html .= ">";
		$html .= "<i class='icon-filter'></i> Филтър";
		if($this->show) $html .= " <sup>{$orig_dp->totalItemCount}</sup>";
		$html .= "</a>";
		return $html;
	}
}
