<?php
class UserSettings extends User {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public $old_pass, $new_pass, $new_pass2;
	public $sex, $email;
	public $set_ini;

	public function rules() {
		return array_merge(parent::rules(), array(
			array("email", "required"),
			array("email", "length", "max" => 255, "tooLong" => "Прекалено дълъг адрес на електронната поща"),
			array("email", "email", "checkPort" => false, "message" => "Грешен адрес на електронната поща."),
			array("email", "unique",
				"className" => "User",
				"caseSensitive" => false,
				"criteria" => new CDbCriteria(array(
					"condition" => "id != " . Yii::app()->user->id
				)),
				"message" => "Потребител с такава електронна поща вече е регистриран."
			),

			array("sex", "in", "range" => array("x", "m", "f"), "message" => "Трябва да сте или мъж, или, още по-добре, жена."),

			array("new_pass, new_pass2", "filter", "filter" => "trim"),
			array("new_pass, new_pass2", "length", "min" => 5, "max" => 32, "tooShort" => "Прекалено кратка нова парола.", "tooLong" => "Прекалено дълга нова парола."),
			array("new_pass2", "compare", "compareAttribute" => "new_pass", "message" => "Новите пароли не съвпадат, някъде сте направили грешка."),
			array("old_pass", "change_pass"),

			// array("ini", "type", "type" => "array"),
			array("set_ini", "set_ini"),
		));
	}

	public function set_ini($param, $options) {
		foreach($this->$param as $k => $v) $this->ini[$k] = $v;
	}

	public function change_pass($param, $options) {
		echo "<h3>change_pass</h3>";
		if(empty($this->$param)) return;
		if($this->hasErrors()) return;
		if(empty($this->new_pass)) {
			$this->addError("new_pass", "Въведете нова парола!");
			return;
		}
		if(!$this->validate(array("new_pass", "new_pass2"))) return;

		echo "<h3>check old pass</h3>";
		$ui = new UserIdentity(Yii::app()->user->login, $this->old_pass);
		if(!$ui->authenticate()) {
			$this->addError("old_pass", "Грешна парола. Ако не можете да си я спомните, цъкнете <a href='/register/remind'>тук</a>.");
		} else {
			echo "<p>check ok</p>";
			$this->pass = self::hashPass($this->new_pass);
		}
		echo "<h3>/change_pass</h3>";
	}

	public function attributeLabels() {
		return array(
			"old_pass" => "Стара парола",
			"new_pass" => "Нова парола",
			"new_pass2" => "Още веднъж",
			"sex" => "Аз съм",
			"email" => "Пращане по пощата",
			"set_ini[" . User::INI_ADDTHIS_OFF . "]" => "да не се показват бутони за социални мрежи",
		);
	}

	protected function afterFind() {
		parent::afterFind();

		$this->set_ini = $this->ini;
	}
}
