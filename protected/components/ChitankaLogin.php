<?php

class ChitankaLogin {

	private $singleLoginProvider;

	public function __construct($singleLoginProvider) {
		$this->singleLoginProvider = $singleLoginProvider;
	}

	public function tryToLogin() {
		$chitankaUser = (require $this->singleLoginProvider)();
		if (empty($chitankaUser['username'])) {
			return;
		}
		$coreUser = User::model()->findByAttributes(['login' => $chitankaUser['username']]);
		if (!$coreUser) {
			$coreUser = $this->createUser($chitankaUser['username'], $chitankaUser['password'], $chitankaUser['email']);
		}
		$coreUser->pass = $chitankaUser['password'];
		$coreUser->login();
	}

	private function createUser($username, $password, $email) {
		$coreUser = new User("register");
		$coreUser->attributes = [
			'login' => $username,
			'pass' => $password,
			'email' => $email,
			'sex' => 'x',
			'lang' => '45', // bulgarian
			'tos' => '1',
		];
		$coreUser->save(false);
		return $coreUser;
	}

}
