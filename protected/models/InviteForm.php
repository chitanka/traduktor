<?php
class InviteForm extends CFormModel {
	public $email, $who;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			// login and pass are required
			array("email", "required", "message" => "Молим, въведете адреса на ваш приятел."),
			array("email", "email", "message" => "Това не прилича на адрес на електронна поща, молим пробвайте отново."),
			array("who", "required", "message" => "Молим, подпишете се."),
			array("who", "filter", "filter" => "htmlspecialchars"),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			"email" => "Въведете e-mail на ваш приятел, и той ще получи красиво съобщение с вашите данни:",
			"who" => "Как да ви представим?"
		);
	}

}
