<?php
class RemindForm extends CFormModel {
	public $clue;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			// login and pass are required
			array("clue", "required", "message" => "Трябва да напишете нещо тук."),
			array("clue", "filter", "filter" => "trim"),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			"clue" => "Въведете вашия логин или e-mail-а, който се въвели при регистрацията, и паролата ще бъде изпратена на електронен адрес:",
		);
	}

	public function findUser() {
		if(strpos($this->clue, "@") !== false) {
			$user = User::model()->find("LOWER(email) = :email", array(":email" => mb_strtolower($this->clue)));
			if(!$user) {
				$this->addError("clue", "Няма потребител, регистриран с такъв e-mail адрес.");
			}
		} else {
			$user = User::model()->find("LOWER(login) = :login", array(":login" => mb_strtolower($this->clue)));
			if(!$user) {
				$this->addError("clue", "Няма потребител с такъв логин.");
			}
		}

		return $user;
	}
}
