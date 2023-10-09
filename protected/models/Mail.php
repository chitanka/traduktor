<?php
/**
 * @property $url
 * @property User $buddy
 *
 * @property $id
 * @property $user_id
 * @property $buddy_id
 * @property $folder
 * @property $seen
 * @property $cdate
 * @property $subj
 * @property $body
 */
class Mail extends CActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	public function tableName() {
		return "mail";
	}

	const INBOX = 1;
	const SENT = 2;

	public static $folders = array(
		self::INBOX => "Входящи",
		self::SENT => "Изходящи",
	);

	public $sendTo;

	public function attributeLabels() {
		return array(
			"sendTo" => "На кого",
			"subj" => "Тема",
			"body" => "Съобщение",
		);
	}

	public function relations() {
		return array(
			"buddy" => array(self::BELONGS_TO, "User", "buddy_id", "select" => array("id", "login", "sex", "upic", "email", "can", "ini")),
		);
	}

	public function rules() {
		return array(
			array("sendTo", "required", "message" => "Молим, въведете ника на получателя на писмото."),
			array("sendTo", "validateSendTo"),
			array("subj", "validateSubj"),
			array("body", "validateBody"),
			array("body", "required", "message" => "Писмото без текст е &dash; прекалено многозначителен начин за комуникация. Напишете нещо."),
		);
	}

	public function validateSendTo($attr, $params) {
		$buddy = User::model()->byLogin($this->$attr)->find();
		if(!$buddy) {
			$this->addError("sendTo", "Потребител с такъв ник не съществува.");
			return;
		}
		$this->buddy = $buddy;
	}

	public function validateSubj($attr, $params) {
		$this->$attr = trim(htmlspecialchars($this->$attr));
	}

	public function validateBody($attr, $params) {
		$p = new CHtmlPurifier();
		$p->options = Yii::app()->params["HTMLPurifierOptions"];
		$this->$attr = trim($p->purify($this->$attr));
	}

	public function folder($user_id, $folder) {
		$user_id = (int) $user_id;
		$folder = (int) $folder;

		$c = $this->getDbCriteria();

		$c->addCondition("t.user_id = {$user_id} AND t.folder = {$folder}");
		$c->order = "t.cdate desc";

		return $this;
	}

	public function setSeen() {
		if($this->id) Yii::app()->db->createCommand("UPDATE mail SET seen = true WHERE id = :id")->execute(array(":id" => $this->id));
	}

	public function send() {
		if(!$this->validate()) return false;

		if(!$this->isNewRecord) throw new CHttpException(500, "Системна грешка Mail::send::notNewRecord");
		if(!($this->buddy instanceof User)) throw new CHttpException(500, "Системна грешка Mail::send::buddyNotUser");

		// 2. Помещаем письмо в свои исходящие
		$sent = clone $this;
		$sent->folder = self::SENT;
		$sent->user_id = Yii::app()->user->id;
		$sent->buddy_id = $this->buddy->id;
		$sent->seen = true;

		// 1. Помещаем письмо в инбокс получателя
		$this->folder = self::INBOX;
		$this->user_id = $this->buddy->id;
		$this->buddy_id = Yii::app()->user->id;
		$this->seen = false;

		if(!$this->save()) return false;
		if(!$sent->save()) return false;

		// 3. Если нужно, шлём почту получателю
		if($this->buddy->ini_get(User::INI_MAIL_PMAIL)) {
			$msg = new YiiMailMessage("Имате писмо! Тема: \"" . $this->subj . "\"");
			$msg->view = "mail";
			$msg->setFrom(array(Yii::app()->params["systemEmail"] => Yii::app()->user->login . " - писмо"));
			$msg->addTo($this->buddy->email);
			$msg->setBody(array("message" => $this), "text/html");
			Yii::app()->mail->send($msg);
		}

		return true;
	}

	public function getUrl() {
		return "/my/mail/{$this->id}";
	}
}
