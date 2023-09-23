<?php

class RegisterController extends Controller {
	public function actions() {
		return array(
			"captcha" => array("class" => "CCaptchaAction"),
		);
	}

	public function filters() {
		return array(
			"usersOnly + settings, logout, invite",
		);
	}

	protected function performAjaxValidation($model) {
		if(isset($_POST["ajax"]) && $_POST['ajax']==="form-register") {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionIndex($u = null, $invite = null) {
		$this->forwardToHomepageIfSingleLogin();

		$user = new User("register");
		$user->lang = 1;

		if(isRegistrationByInvite()) {
			/** Магия с инвайтами */
			$invite = RegInvite::model()->findByAttributes([
				"from_id" => (int) $u,
				"code" => $invite
			]);
			if(!$invite) {
				Yii::app()->user->setFlash("error", "Вашата покана не е открита. Причините може да са няколко: поканата може да е изтекла, може да е била изтеглена обратно от подателя или вашата поща може да е променила линка, по който сте стигнали тук.");
				$this->redirect("/");
			}

			if($invite->to_id) {
				// Инвайт адресный, просто активируем аккаунт и редиректим на главную
				$invite->buddy->invited_by = $invite->from_id;
				$invite->buddy->can_set(User::CAN_LOGIN, 1);
				$invite->buddy->save(false, ["invited_by", "can"]);

				// Удаляем инвайт, потому что его использовали
				$invite->delete();

				// И пиздуем на главную с флагом "fresh meat"
				Yii::app()->user->setFlash("success", "Радваме се да ви видим в рехавите ни редици, {$invite->buddy->login}! Разчитаме на вас. Въведете старата си парола.");
				Yii::app()->user->setState("loginAs", $invite->buddy->login);
				$this->redirect("/");
			}

			$user->email = $invite->to_email;
			$user->invited_by = $invite->from_id;
		}

		$this->performAjaxValidation($user);
		if(isset($_POST["User"])) {
			$user->attributes = $_POST["User"];
			if($user->save()) {
				// отправить письмо
				$message = new YiiMailMessage("Добро пожаловать на " . Yii::app()->name);
				$message->view = "welcome";
				$message->from = Yii::app()->params['adminEmail'];
				$message->addTo($user->email);
				$message->setBody(array("user" => $user), "text/html");
				Yii::app()->mail->send($message);

				// залогинить
				$identity = new UserIdentity($user->login, $_POST["User"]["pass"]);
				$identity->authenticate();
				$duration = 3600 * 24 * 30;
				Yii::app()->user->login($identity, $duration);

				if(isRegistrationByInvite()) {
					$invite->delete();
				}

				$this->redirect("/register/done");
			}
		}

		$this->render("index", array("model" => $user));
	}

	public function actionLogout() {
		$this->forwardToHomepageIfSingleLogin();

		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionDone() {
		$this->render('done');
	}

	public function actionRemind() {
		$this->forwardToHomepageIfSingleLogin();

		if(Yii::app()->request->isPostRequest) {
			$clue = "";
			if(isset($_POST["clue"])) {
				$clue = trim($_POST["clue"]);
			}
			$user = null;
			if(strpos($clue, "@") !== false) {
				$user = User::model()->find("LOWER(email) = :email", [":email" => mb_strtolower($clue)]);
			} else {
				$user = User::model()->find("LOWER(login) = :login", [":login" => mb_strtolower($clue)]);
			}

			if(!$user) {
				Yii::app()->user->setFlash("error", "Потребителят не е открит.");
			} else {
				$token = RemindToken::gen($user);

				$message = new YiiMailMessage("Вашата парола за " . Yii::app()->name);
				$message->view = "remind";
				$message->from = Yii::app()->params["adminEmail"];
				$message->addTo($user->email);
				$message->setBody(["user" => $user, "token" => $token], "text/html");
				Yii::app()->mail->send($message);

				$this->render("remind_done");
				return;
			}
		}

		$this->render("remind");
	}

	public function actionReset($u, $c) {
		$this->forwardToHomepageIfSingleLogin();

		$token = RemindToken::model()->find("user_id = :user_id", ["user_id" => (int) $u]);

		if(!$token || !$token->check($c)) {
			$this->render("reset_fail");
			echo "";
			return;
		}

		$user = User::model()->findByPk($token->user_id);

		if(Yii::app()->request->isPostRequest) {
			$pass = $_POST["pass"];
			if(strlen($pass) < 8) {
				Yii::app()->user->setFlash("error", "Паролата не може да бъде по-кратка от 8 символа. Включете въображението си.!");
			} elseif($pass != $_POST["pass2"]) {
				Yii::app()->user->setFlash("error", "Паролите не съвпадат, пробвайте пак!");
			} else {
				/** @todo: чувак, немножко отпустит - перепиши этот кусок, некрасиво */
				$user->pass = User::hashPass($pass);
				$user->save();

				$user->pass = $pass;
				$user->login();

				$token->delete();

				Yii::app()->user->setFlash("success", "Радваме се да ви видим отново!");
				$this->redirect("/");
			}
		}

		$this->render("reset", ["user" => $user]);
	}

	public function actionSettings() {
		$form = UserSettings::model()->findByPk(Yii::app()->user->id);

		$user = Yii::app()->user;

		if(isset($_POST["UserSettings"])) {
			if (isset($_POST["ini"]["t"]["copy"])) {
				$_POST["ini"]["t"]["copy"] = (int) $_POST["ini"]["t"]["copy"];
			}

			if($_POST["ini"]["t"]["iface"] != $user->ini["t.iface"]) {
				file_put_contents(
					Yii::app()->basePath . "/runtime/higgs.log",
					date("Y-m-d H:i:s") . "\t" . $user->login . "\t" . $_POST["ini"]["t"]["iface"] . "\n",
					FILE_APPEND
				);
			}

			foreach($_POST["ini"] as $area => $ini) {
				if(!in_array($area, array("l", "t", "c"))) continue;
				foreach($ini as $k => $v) {
					$user->ini->set($area . "." . $k, $v);
				}
			}
			$user->ini->save();

			$form->attributes = $_POST["UserSettings"];
			if($form->save()) {
				$back = $form->url;
				if(!empty($_POST["referer"])) {
					$referer = parse_url($_POST["referer"]);
					if(!empty($referer["path"]) && trim($referer["path"], "/") != "register/settings" && $_SERVER["SERVER_NAME"] == $referer["host"]) $back = $referer["path"];
				}

				$form->pass = $form->new_pass;
				$form->login();

				$this->redirect($back);
			}
		}


		$this->render("settings", array("model" => $form));
	}
}
