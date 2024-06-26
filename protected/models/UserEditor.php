<?php
class UserEditor extends User {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public $name, $icq, $lj, $url, $skype, $bdate, $bdate_y, $bdate_m, $bdate_d, $country_id, $city, $bio, $new_upic, $rm_upic;

	public static $Properties = array(
		1 => array('Име', 					'name', 60, 60),
		2 => array('ICQ', 					'icq', 16, 16),
		3 => array('ЖЖ', 					'lj', 16, 16),
		4 => array('Личен сайт', 	'url', 255, 60),
		5 => array('skype', 				'skype', 32, 16),
		6 => array('Дата на раждане', 		'bdate', 10, 16),
		7 => array('Държава', 				'country_id', 'не казвам'),
		8 => array('Град', 				'city', 60, 60),
		9 => array('Няколко думи за мен', 'bio', 3, 60, true),
	);

	public function rules() {
		return array_merge(parent::rules(), array(
			array("name, icq, lj, url, skype, bdate_y, bdate_m, bdate_d, country_id, city, bio", "safe"),
			array("name, icq, lj, url, skype, city", "clean"),
			array("bio", "safehtml"),
			array("bdate_y, bdate_m, bdate_d, country_id", "numerical", "integerOnly" => true),
			array("icq", "match", "pattern" => '/^[\d -]+$/', "message" => "номерът на icq се състои от цифри и тирета"),
			array("lj", "match", "pattern" => '/^[a-z\d_-]+$/i', "message" => "въведете ника си в ЖЖ"),
			array("url", "url", "defaultScheme" => "http", "message" => "това не прилича на адрес на сайт"),
			array("rm_upic", "boolean"),
			array("new_upic", "file", "allowEmpty" => true, "types" => "jpg, gif, png, jpeg", "wrongType" => "Грешен формат на файла. Молим, качвайте JPG, PNG или GIF"),
		));
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
			"name" => "Има",
			"icq" => "ICQ",
			"lj" => "ЖЖ",
			"url" => "Личен сайт",
			"skype" => "Skype",
			"bdate" => "Дата на раждане",
			"country_id" => "Държава",
			"city" => "Град",
			"bio" => "Няколко думи за мен",
			"new_upic" => "Аватар",
			"rm_upic" => "Изтриване",
		);
	}

	protected function afterFind() {
		// загружаем userinfo
		$r = Yii::app()->db->createCommand("SELECT prop_id, value FROM userinfo WHERE user_id = :user_id")->query(array(":user_id" => $this->id));
		foreach($r as $row) {
			$attr = self::$Properties[$row["prop_id"]][1];
			if($attr == "bdate") {
				list($this->bdate_y, $this->bdate_m, $this->bdate_d) = sscanf($row["value"], "%d-%d-%d");
				foreach(array("bdate_y", "bdate_m", "bdate_d") as $k) if($this->$k == 0) $this->$k = "";
			}
			$this->$attr = $row["value"];
		}

		parent::afterFind();
	}

	protected function afterValidate() {
		// Формируем bdate
		$this->bdate = sprintf("%04d-%02d-%02d", $this->bdate_y, $this->bdate_m, $this->bdate_d);

		parent::afterValidate();
	}

	protected function afterSave() {
		// сохраняем userinfo
		Yii::app()->db->createCommand("DELETE FROM userinfo WHERE user_id = :user_id")->execute(array(":user_id" => $this->id));
		foreach(self::$Properties as $prop_id => $P) {
			$attr = $P[1];
			Yii::app()->db->createCommand()->insert("userinfo", array("user_id" => $this->id, "prop_id" => $prop_id, "value" => $this->$attr));
		}
	}
}
