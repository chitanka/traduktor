<?php
class SiteController extends Controller {

	public function actionIndex() {
		$this->checkUser();

		$this->layout = "column1";

		$this->render('index', array(
			"hot" => Cacher::getHot(),
			"searchTop" => Cacher::getSearchTop(),
			"announces" => Cacher::getAnnounces(),
			"blog" => $this->getBlogPosts(),
		));
	}

	public function actionIni() {
		$area = $_POST["area"]; unset($_POST["area"]);

		if(in_array($area, array("hot"))) {
			foreach($_POST as $k => $v) {
				Yii::app()->user->ini->set($area . "." . $k, $v);
			}
			Yii::app()->user->ini->save();
		}

		$this->redirect("/");
	}

    public function actionHelp() {
		$this->layout = "column1";
        $this->render("help");
    }

	public function actionTOS() {
		$this->layout = "column1";
		$this->render("tos");
	}

	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest)
				echo json_encode(array("error" => $error["message"]));
			else
				$this->render('error', $error);
		}
	}

	private function loginAttempt()
	{
		if (Yii::app()->request->isPostRequest && isset($_POST["login"])) {
			$user = new User("login");
			$user->setAttributes($_POST["login"]);
			$user->remember = true;
			if ($user->login()) {
				$this->redirect("/");
			} else {
				Yii::app()->user->setFlash("error", $user->getError("pass"));
			}
		}
	}

	private function checkUser()
	{
		if (Yii::app()->user->isGuest) {
			$this->loginAttempt();
			if (isRegistrationByInvite()) {
				$this->layout = "empty";
				$this->render("index_guest");
				exit();
			}
		}
	}

	/**
	 * @return static[]
	 */
	private function getBlogPosts()
	{
		if (!($blog = Yii::app()->cache->get("blog"))) {
			$blog = BlogPost::model()->common()->findAll(["limit" => 10]);
			Yii::app()->cache->set("blog", $blog, 105);
			return $blog;
		}
		return $blog;
	}
}
