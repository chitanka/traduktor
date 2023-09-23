<?php
class Announce extends BlogPost {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function rules() {
		return array(
			array("body", "required", "message" => "Молим, въведете текст за анонса."),
			array("body", "length", "max" => 4096, "tooLong" => "Текстът на анонса не може да бъде по-дълъг от 4 килобайта."),
			array("body", "safehtml"),
			array("topics", "required", "message" => "Молим, изберете рубрика."),
			array("topics", "in", "range" => array_keys(Yii::app()->params["blog_topics"]["announce"])),
		);
	}

	public function safehtml($attr, $params) {
		$p = new CHtmlPurifier();
		$HTMLPurifierOptions = Yii::app()->params["HTMLPurifierOptions"];
		$HTMLPurifierOptions["HTML.Allowed"] = "a[href],b,strong,i,em,u,small,sub,sup";
		$p->setOptions($HTMLPurifierOptions);
		$this->$attr = trim($p->purify($this->$attr));
	}

	public function attributeLabels() {
		return array(
			"body" => "Текст на анонса",
			"topics" => "Рубрика"
		);
	}

	public function getWasToday() {
		return Yii::app()->db->createCommand("SELECT 1 FROM blog_posts WHERE book_id = :book_id AND (topics BETWEEN 80 AND 89) AND cdate::date = current_date LIMIT 1")
			->queryScalar(array(":book_id" => $this->book_id));
	}

	public function afterValidate() {
		if($this->isNewRecord) {
			if($this->wasToday) {
				$this->addError("body", "Не може да правите анонси повече от веднъж за денонощие");
			}
		}

		parent::afterValidate();
	}
}
?>
