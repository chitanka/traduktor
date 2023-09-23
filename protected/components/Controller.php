<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {
	/**
	* Макет
	*/
	public $layout='//layouts/column2';
	public $layout_layout = "v3";
	public $layoutOptions = array(
		"fluid" => false,
	);

	public $side_view = "";
	public $side_params = null;

	/**
	* Меню и области сайта
	*/
	public $siteAreas = array(
		"films"   => array("url" => '/search/?SearchFilter[typ]=S', "label" => 'Превод на филми'),
		"books"   => array("url" => '/search/?SearchFilter[typ]=A', "label" => 'Превод на книги'),
		"phrases" => array("url" => '/search/?SearchFilter[typ]=P', "label" => 'Превод на фрази'),
		"blog"       => array("url" => '/blog/',                    "label" => 'Блог'),
		"users"      => array("url" => '/users/',                   "label" => 'Преводачи'),
	);

	public $siteArea = "";

	public $breadcrumbs = array();
	public $menu = array();

	public function init() {
		parent::init();

		$user = Yii::app()->user;
		if ($user->isGuest) {
			(new ChitankaLogin(Yii::app()->params['singleLoginProvider']))->tryToLogin();
		}

		Yii::app()->clientScript
			->registerPackage("jquery")

			// я вот не ебу, остались ли где-нибудь вызовы этого богомерзкого поделия
//			->registerScriptFile("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js")
//			->registerCssFile("/css/ui-lightness/jquery-ui-1.8.23.custom.css")

			->registerScriptFile("/js/jquery.form.js")
			->registerScriptFile("/js/jquery.cookie.js")
			->registerScriptFile("/js/global.js?16")
			->registerCssFile(Yii::app()->user->ini["t.iface"] == 1 ? "/css/v3.1.css?3" : "/css/v3.css?13")    // чтобы в конец списка CSS ёбнулось
		;

		if(!$user->isGuest) {
			Yii::app()->clientScript->registerScriptFile("/js/user.js?3");

			Yii::app()->clientScript->registerScriptFile("/js/chat.js?3");

			if($user->can("betatest")) {
				$A = Yii::app()->params["blog_topics"];
				$A["common"][70] = "Стройплощадка";
				Yii::app()->params["blog_topics"] = $A;
			}

			Yii::app()->clientScript->registerScript("user_init", "var User = new CUser({id: " . Yii::app()->user->id . ", login: '" . Yii::app()->user->login . "'});\n", CClientScript::POS_HEAD);

			// Статистика использования интерфейса
			$higgsStat = Yii::app()->cache->get("higgsStat");
			$higgsStat[$user->id] = $user->ini["t.iface"];
			Yii::app()->cache->set("higgsStat", $higgsStat);
			unset($higgsStat);
		} else {
			Yii::app()->clientScript->registerScript("user_init", "var User = new CUser({id: 0, login: 'anonymous'});\n", CClientScript::POS_HEAD);
		}

		CHtml::$afterRequiredLabel = "";

		if(Yii::app()->user->isPaid) {
			Yii::app()->clientScript->registerScriptFile("/js/paiduser.js");
		}

		if(Yii::app()->user->ini["t.iface"] == 1) Yii::app()->clientScript->registerCss("user_ini_css", Yii::app()->user->ini->getCss());
	}

	public function beforeAction($action) {
		$user = Yii::app()->user;
		if(isRegistrationByInvite()) {
			if(!$user->isGuest) {
				if(!$user->model->can(User::CAN_LOGIN)) {
					$user->logout();
					Yii::app()->user->setFlash("error", "Съжаляваме, но вие не сте член на клуба.");
					$this->redirect("/");
				}

				$banned_until = Yii::app()->db
					->createCommand("SELECT until FROM ban WHERE user_id = :user_id AND until >= current_date")
					->queryScalar(array(":user_id" => Yii::app()->user->id));

				if($banned_until) {
					$user->setFlash("warning", "Имате бан до " . Yii::app()->dateFormatter->formatDateTime($banned_until, "medium", "") . " г. включително.");
					$user->logout();
				}
			}
		}
		return parent::beforeAction($action);
	}

	public function filterUsersOnly($filterChain) {
		if(Yii::app()->user->isGuest) {
			throw new CHttpException(403, "За да видите тази страница, трябва да влезете в сайта или да се регистрирате.");
		}

		$filterChain->run();
	}

	protected function forwardToHomepageIfSingleLogin() {
		if (!empty(Yii::app()->params['singleLoginProvider'])) {
			$this->forward('/');
		}
	}

}
