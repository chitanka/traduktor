<?php
class MyCommentsController extends Controller {
	public function filters() {
		return array(
			'accessControl',
		);
	}

	public function accessRules() {
		return [
			['allow', 'users' => ['@'], 'actions' => ["index", "add", "rm", "ini", "visited"]],
			['deny', 'users'=>['*']],
		];
	}

	public function actionIndex() {
		$user = Yii::app()->user;
		$modes = array("p" => "в постове", "o" => "във фрагменти от оригинала");

		if(key_exists("mode", $_GET) and isset($modes[$_GET["mode"]])){
			$mode = $_GET["mode"];
		} elseif(isset($_SESSION["my_comments_mode"])) {
			$mode = $_SESSION["my_comments_mode"];
		} else {
			$mode = "p";
		}
		$_SESSION["my_comments_mode"] = $mode;

		if($mode == "p") {
			$lenta = new CActiveDataProvider(BlogPost::model()->talks($user->ini_get(User::INI_MYTALKS_NEW)), [
				"pagination" => ["pageSize" => 20],
			]);
		} elseif($mode == "o") {
			$lenta = new CActiveDataProvider(Orig::model()->talks($user->ini_get(User::INI_MYTALKS_NEW)), [
				"pagination" => ["pageSize" => 20],
			]);
		}

		$this->side_view = "index_side";
		$this->side_params = array("mode" => $mode);

		$this->render("index", array("lenta" => $lenta, "modes" => $modes, "mode" => $mode));
	}

	public function actionIni() {
		Yii::app()->user->ini_set(User::INI_MYTALKS_NEW, (int) $_POST["new"]);

		$this->redirect("/my/comments/");
	}

	public function actionVisited() {
		$mode = $_POST["mode"];

		if($mode == "p") {
			Yii::app()->db->createCommand("
				WITH unseen AS (
					SELECT
						s.post_id,
						p.n_comments, p.lastcomment
					FROM seen s
						LEFT JOIN blog_posts p ON s.post_id = p.id
					WHERE
						s.user_id = :user_id AND s.track AND
						s.n_comments < p.n_comments
				)
				UPDATE seen SET n_comments = unseen.n_comments, seen = unseen.lastcomment FROM unseen WHERE seen.user_id = :user_id AND seen.post_id = unseen.post_id
			")->execute(array(":user_id" => Yii::app()->user->id));
		} elseif($mode == "o") {
			Yii::app()->db->createCommand("
				WITH unseen AS (
					SELECT
						s.orig_id,
						p.n_comments
					FROM seen s
						LEFT JOIN orig p ON s.orig_id = p.id
					WHERE
						s.user_id = :user_id AND s.track AND
						s.n_comments < p.n_comments
				)
				UPDATE seen SET n_comments = unseen.n_comments FROM unseen WHERE seen.user_id = :user_id AND seen.orig_id = unseen.orig_id
			")->execute(array(":user_id" => Yii::app()->user->id));
		}

		$this->redirect("/my/comments");
	}

	public function actionAdd() {
		$orig_id = isset($_POST["orig_id"]) ? (int) $_POST["orig_id"] : (int) $_GET["orig_id"];
		$post_id = isset($_POST["post_id"]) ? (int) $_POST["post_id"] : (int) $_GET["post_id"];

		if($orig_id) {
			$orig = Orig::model()->with("chap.book.membership")->findByPk($orig_id);
			if(!$orig) {
				throw new CHttpException(404, "Опитвате да добавите несъществуващ фрагмент на оригинала в &laquo;моите обсъждания&raquo;. Най-вероятно е изтрит");
			} elseif(!$orig->chap->can("read")) {
				throw new CHttpException(403, "Не можете да добавите този фрагмент в &laquo;моите обсъждания&raquo; тъй като нямате достъп до този превод.");
			} else {
				$orig->setTrack();
			}
		} elseif($post_id) {
			$post = BlogPost::model()->with("book", "seen")->findByPk($post_id);

			if(!$post) {
				throw new CHttpException(404, "Опитвате да добавите несъществуващ пост в &laquo;моите обсъждания&raquo;. Най-вероятно е изтрит.");
			} else if($post->book_id != 0 and !$post->book->can("blog_r")) {
				throw new CHttpException(403, "Не можете да добавите този пост в &laquo;моите обсъждания&raquo; тъй като нямате достъп до блога на този превод.");
			} else {
				$post->setTrack();
			}
		} else {
			throw new CHttpException(500, "Грешно търсене.");
		}

		if($_POST["ajax"]) {
			echo json_encode(array("status" => "ok", "id" => $orig_id ? $orig_id : $post_id));
			Yii::app()->end();
		} else {
			$this->redirect("/my/comments/?mode=" . ($orig_id ? "o" : "p"));
		}
	}

	public function actionRm() {
		$orig_id = isset($_POST["orig_id"]) ? (int) $_POST["orig_id"] : (int) $_GET["orig_id"];
		$post_id = isset($_POST["post_id"]) ? (int) $_POST["post_id"] : (int) $_GET["post_id"];

		if($orig_id) {
			Yii::app()->db->createCommand("UPDATE seen SET track = false WHERE user_id = :user_id AND orig_id = :orig_id")
				->execute(array(":user_id" => Yii::app()->user->id, ":orig_id" => $orig_id));

			$this->redirect("/my/comments/?mode=o");
		} elseif($post_id) {
			Yii::app()->db->createCommand("UPDATE seen SET track = false WHERE user_id = :user_id AND post_id = :post_id")
				->execute(array(":user_id" => Yii::app()->user->id, ":post_id" => $post_id));

			$this->redirect("/my/comments/?mode=p");
		} else {
			throw new CHttpException(500, "Грешно търсене.");
		}

	}

}
?>
