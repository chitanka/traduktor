<?php
/**
 * @property GroupMember $membership
 * @property User $owner
 *
 * @property integer $id
 * @property string $cdate
 * @property string $typ
 * @property string $opts
 * @property integer $owner_id
 * @property integer $s_lang
 * @property integer $t_lang
 * @property integer $cat_id
 *
 * @property integer n_verses
 * @property integer n_vars
 * @property integer d_vars
 *
 * @property string $facecontrol
 *
 * @property string $ac_read
 * @property string $ac_trread
 * @property string $ac_gen
 * @property string $ac_rate
 * @property string $ac_comment
 * @property string $ac_tr
 * @property string $ac_blog_r
 * @property string $ac_blog_c
 * @property string $ac_blog_w
 * @property string $ac_announce
 * @property string $ac_chap_edit
 * @property string $ac_book_edit
 * @property string $ac_membership
 */
class Book extends CActiveRecord {

	const TYPE_SUBTITLES = 'S';
	const TYPE_TEXT = 'A';

	const PAGE_SIZE_SUBTITLES = 200;
	const PAGE_SIZE_TEXT = 50;

	/** @return Book */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	public function tableName() {
		return "books";
	}

	const FC_OPEN = 0;
	const FC_CONFIRM = 1;
	const FC_INVITE = 2;

	public $rm_img, $new_img;

	public $img = array(0,0,0,0,0);

//	public $banned, $ban_until; /* OBSOLETE */

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			's_lang' => 'Език на оригинала',
			't_lang' => 'Език на превода',
			's_title' => 'Заглавие на езика на оригинала',
			't_title' => 'Заглавие на езика на превода',
			'descr' => 'Описание',
			'new_img' => 'Изображение в съдържанието',
			"facecontrol" => "Участие в групата",
		);
	}

	public function rules() {
		return array(
			array("cat_id",            "exist", "allowEmpty" => true, "className" => "Category", "attributeName" => "id", "criteria" => array("condition" => "available"), "message" => "В този раздел не бива да се добавят преводи", "on" => "cat"),

			array("s_title, t_title",  "clean", "on" => "info"),
			array("descr",             "safehtml", "on" => "info"),
			array("s_title, t_title",  "required", "on" => "info"),
			array('s_lang, t_lang',    'numerical', 'integerOnly' => true, "allowEmpty" => false, "on" => "info"),
			array('s_title, t_title',  'length', 'max' => 255, "allowEmpty" => false, "on" => "info"),
			array("rm_img",            "boolean", "on" => "info"),
			array("new_img",           "file", "allowEmpty" => true, "types" => "jpg, gif, png, jpeg", "wrongType" => "Неподдържан формат на файла. Моля, качвайте само JPG, PNG или GIF", "on" => "info"),

			array("facecontrol",        "numerical", "integerOnly" => true, "on" => "access"),
			array('ac_read, ac_trread, ac_gen, ac_rate, ac_comment, ac_tr, ac_blog_r, ac_blog_w, ac_blog_c',
				                        'in', 'range' => array("a", "g", "m", "o"), "on" => "access"),
			array("ac_announce",        "in", "range" => array("g", "m", "o"), "on" => "access"),
			array("ac_chap_edit, ac_book_edit, ac_membership",
				                        "in", "range" => array("m", "o"), "on" => "access"),
		);
	}

	public function clean($attr, $params) {
		$this->$attr = trim(htmlspecialchars(strip_tags($this->$attr, ENT_QUOTES | ENT_HTML5)));
	}

	public function safehtml($attr, $params) {
		$p = new CHtmlPurifier();
		$p->options = Yii::app()->params["HTMLPurifierOptions"];
		$this->$attr = trim($p->purify($this->$attr));
	}

	public function relations() {
		$rel = array(
			"chapters"     => array(self::HAS_MANY,   "Chapter",      "book_id",   "select" => array("*", new CDbExpression("EXTRACT(EPOCH FROM now() - last_tr)::int as idle_time")), "order" => "ord"),
			"owner"        => array(self::BELONGS_TO, "User",         "owner_id",  "select" => array("id", "login", "sex", "email", "upic", "ini")),
			"members"      => array(self::HAS_MANY,   "GroupMember",  "book_id"),
			"membership"   => array(self::HAS_ONE,    "GroupMember",  "book_id",   "on" => "membership.user_id = " . intval(Yii::app()->user->id)),
			"cat"          => array(self::BELONGS_TO, "Category",     "cat_id"),
			"moder_cat"    => array(self::HAS_ONE,    "ModerBookCat", "book_id",   "joinType" => "RIGHT JOIN"),
			"dict_cnt"     => array(self::STAT,       "Dict",         "book_id")
		);
		if(!Yii::app()->user->isGuest) {
			$rel["bookmark"] = array(
				self::HAS_ONE, "Bookmark", "book_id", "on" => "bookmark.user_id = " . Yii::app()->user->id . " AND bookmark.orig_id IS NULL"
			);
		} else {
			$rel["bookmark"] = array(
				self::HAS_ONE, "Bookmark", "book_id", "on" => "bookmark.user_id IS NULL"
			);
		}

		return $rel;
	}

	public function membership($user_id) {
		$this->getDbCriteria()->mergeWith(array(
			"with" => array("membership" => array("on" => "membership.user_id = {$user_id}")),
		));
		return $this;
	}

	public function moderated_by($user_id) {
		$this->getDbCriteria()->mergeWith(array(
			"with" => array(
				"membership" => array(
					"joinType" => "RIGHT JOIN",
					"condition" => "membership.user_id = {$user_id} AND membership.status = 2",
					"on" => "",
					"order" => "t.s_title",
				),
				"dict_cnt" => array(),
			),
		));
		return $this;
	}




	protected function afterFind() {
		$this->img = new UploadedImage("book", $this->img);

		if($this->hasRelated("chapters")) foreach($this->chapters as $chap) {
			Yii::log("Set book for {$chap->title}");
			$chap->book = $this;
		}

		if($this->facecontrol == Book::FC_OPEN) {
			foreach(array_keys(Yii::app()->params["ac_areas"]) as $ac) {
				if($this->$ac == "g") $this->$ac = "m";
			}
		}

		parent::afterFind();
	}

	protected function afterValidate() {
		// Аватар
		if($this->scenario == "info" && $this->rm_img) $this->img->delete();

		if($this->scenario == "info" && $this->new_img) {
			$new_img = new UploadedImage("book");

			if(!$new_img->upload($this->new_img, 200, 500)) {
				$this->addError("new_img", "Качването на изображението не сполучи. Пробвайте пак или изберете друго изображение.");
				return;
			}

			if($this->img instanceof UploadedImage) $this->img->delete();
			$this->img = $new_img;
		}

		parent::afterValidate();
	}

	protected function beforeSave() {
		if(!parent::beforeSave()) return false;

		if(is_array($this->img)) $this->img = "{" . join(",", $this->img) . "}";

		return true;
	}

	protected function afterDelete() {
		// 1. Удаляем картинку
		if($this->img) $this->img->delete();
	}


	const OPTS_BAN_COPYRIGHT = 0;
	const OPTS_SHOW_CH_CDATE = 7;
	public function opts_get($pos) {
		return substr($this->opts, $pos, 1);
	}

	public function opts_set($pos, $value) {
		$this->opts = substr_replace($this->opts, $value, $pos, 1);
	}

	public function url($area = "") {
		Yii::log("DEPRECATED: Book::url()", "warning", "app");
		return "/book/" . intval($this->id) . ($area != "" ? "/{$area}" : "");
	}
	public function getUrl($area = "") {
		return "/book/" . intval($this->id) . ($area != "" ? "/{$area}" : "");
	}
	public function getAhref($area = "") {
		return "<a href='" . $this->getUrl($area) . "'>{$this->fullTitle}</a>";
	}
	public function getFullTitle() {
		return $this->s_title . ($this->t_title != "" ? " / {$this->t_title}" : "");
	}

	public function getReady() {
		if($this->n_verses == 0) return "&mdash;";
		if($this->d_vars == 0) return "0%";
		if($this->n_verses == $this->d_vars) return "100%";
		return sprintf("%.01f%%", floor($this->d_vars / $this->n_verses * 1000) / 10);
	}

	// todo: refactor
	public function can($what)
	{
		// владельцу можно всё
		if ($this->isCurrentUserTheOwner()) return true;

		if($what == "owner") return $this->isCurrentUserTheOwner();

		// read, trread, gen, rate, comment, tr, blog_r, blog_c, blog_w, chap_edit, book_edit, membership, announce
		$ac = "ac_{$what}";

		// если юзер забанен, то ему нельзя ничего
		if ($this->checkMembershipStatus(GroupMember::BANNED)) return false;

		if ($what == "moderate") return $this->checkMembershipStatus(GroupMember::MODERATOR);
		if ($what == "dict_edit") return $this->checkMembershipStatus(GroupMember::MEMBER);

		if ($this->$ac == "g") return $this->checkMembershipStatus(GroupMember::MEMBER) or $this->checkMembershipStatus(GroupMember::MODERATOR);
		if ($this->$ac == "m") return $this->checkMembershipStatus(GroupMember::MODERATOR);
		if ($this->$ac == "o") return $this->isCurrentUserTheOwner();

		// "a" разрешает анонимам только read, trread, gen и blog_r, любым юзерам - все
		if ($this->$ac == "a") {
			if (Yii::app()->user->isGuest and !($what == "read" || $what == "trread" || $what == "gen" || $what == "blog_r")) return false;
			else return true;
		}

		return false;
	}

	/**
	 * @return bool
     */
	public function isCurrentUserTheOwner()
	{
		return $this->owner_id == Yii::app()->user->id;
	}

	/**
	 * @param Integer $status
	 *
	 * @return bool
	 */
	public function checkMembershipStatus($status)
	{
		if (is_null($this->membership)) return false;
		return $this->membership->status == $status;
	}

	public function role_areas($role) {
		$A = array();
		foreach(Yii::app()->params["ac_areas"] as $ac => $title) {
			if($this->$ac == "g") $A[] = $ac;
		}
		return $A;
	}

	public function user_invited($user_id) {
		if(Yii::app()->user->isGuest) return false;

		return Yii::app()->db
			->createCommand("SELECT 1 FROM invites WHERE book_id = :book_id AND to_uid = :my_uid")
			->query(array(":book_id" => $this->id, ":my_uid" => $user_id))
			->count();
	}

	public function getWhoCanDoIt($ac = "read", $tools = true) {
		$ac = "ac_" . $ac;

		$msg = "Это " . ($this->$ac == "o" ? "може" : "могат") . " да правят " . Yii::app()->params["ac_roles_title"][$this->$ac] . ".";

		if($this->$ac == "g") {
			if($this->facecontrol == self::FC_CONFIRM) {
				$msg .= " За да встъпите в групата, трябва да подадете заявка на собственика ({$this->owner->ahref})" . ($this->ac_membership == "m" ? " или на модераторите" : "") . ".";
				if($tools) $msg .= Yii::app()->controller->renderPartial("//book/_join", array("book" => $this), true);
			} elseif($this->facecontrol == self::FC_INVITE) {
				$msg = "За да встъпите в групата трябва да получите покана от собственика ({$this->owner->ahref})" . ($this->ac_membership == "m" ? " или от модераторите" : "") . ".";
				if($tools && $this->user_invited(Yii::app()->user->id)) {
					$msg .= " Между другото, имате тази покана.<br /><br /><a href='" . $this->getUrl("invite_accept") . "' class='act'>Приемане</a> | <a href='" . $this->getUrl("invite_decline") . "' class='act'>Отказ</a>";
				}
			}
		}

		return $msg;
	}

	public function getDeniedWhy() {
		if($this->can("read")) return "";

		if($this->checkMembershipStatus(GroupMember::BANNED)) {
			$msg = "Не можете да влезете в превод &laquo;{$this->fullTitle}&raquo;, тъй като собственикът или модераторите са ви баннали.";
		} elseif($this->ac_read == "o") {
			$msg = "Собствеинкът на превода &laquo;{$this->fullTitle}&raquo;, {$this->owner->ahref}, е затворил достъпа до него за всички.";
		} elseif($this->ac_read == "m") {
			$msg = "Преводът &laquo;{$this->fullTitle}&raquo; е достъпен само за модераторите, определени от собственика ({$this->owner->ahref}).";
		} elseif($this->ac_read == "g") {
			if($this->facecontrol == Book::FC_CONFIRM) {
				$msg = "За да влезете в превод &laquo;{$this->fullTitle}&raquo;, трябва да встъпите в групата на превода. ";
			} elseif($this->facecontrol == Book::FC_INVITE) {
				$msg = "За да влезете в превод &laquo;{$this->fullTitle}&raquo;, трябва да получите покана от собственика ({$this->owner->ahref})" . ($this->ac_membership == "m" ? " или от модераторите" : "") . ".";
			} else {
				$msg = "Между нас казано, това не трябва да е така. Създателят на превода &laquo;{$this->fullTitle}&raquo;, {$this->owner->ahref}, е разрешил входа само за членове на групата, а е изтрил самата група. Тоест, за да влезете в превода, трябва да сте член на група, която не съществува. Опитайте да пляснете с една ръка.";
			}
		} else {
			$msg = "Не можете да влезете в този превод.";
		}

		return $msg;
	}

	public function getErrorsString() {
		$t = "";
		foreach($this->getErrors() as $field => $errors) {
			$t .= join("\n", $errors);
		}

		return $t;
	}

	/**
	 * Добавляет в javascript-код страницы инициализацию объекта Book
	 */
	public function registerJS($varName = "Book") {
		$js = "var {$varName} = new CBook({\n";
		foreach(array("id", "owner_id", "facecontrol", "s_lang", "t_lang", "n_verses", "n_vars", "d_vars") as $k) {
			$js .= "\t{$k}: " . intval($this->$k) . ",\n";
		}
		foreach(array("typ", "s_title", "t_title") as $k) {
			$js .= "\t{$k}: '" . addcslashes($this->$k, "\t\r\n'\"") . "',\n";
		}
		if($this->membership) {
			$js .= "\tmembership: {status: " . intval($this->membership->status) . "}\n";
		} else {
			$js .= "\tmembership: {status: null}\n";
		}
		$js .= "});\n";

		Yii::app()->getClientScript()->registerScript("book_" . $varName, $js, CClientScript::POS_HEAD);
	}

	/**
	 * @param Category|null $category
	 *
	 * @return \CActiveDataProvider|null
     */
    public static function getByCategory($category)
	{
		if (is_null($category)) {
			return null;
		}

		$branches_count = $category->getBranchesCount();
		return new CActiveDataProvider(Book::model()->with("cat"), array(
			"criteria" => array(
				"condition" => "cat.mp[1:{$branches_count}] = '{$category->mpPacked}'",
				"order" => "t.s_title",
			),
			"pagination" => array("pageSize" => 50)
		));;
	}

	public function isSubtitles() {
		return $this->typ === 'S';
	}

	public function getPageSize() {
		if ($this->isSubtitles()) {
			return self::PAGE_SIZE_SUBTITLES;
		}
		return self::PAGE_SIZE_TEXT;
	}
}
