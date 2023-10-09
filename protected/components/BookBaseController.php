<?php
class BookBaseController extends Controller {
	/** @return Book */
	protected function loadBook($book_id, $class = "Book") {
		$book_id = (int) $book_id;
		
		if($this->book === null) {
			$this->book = $class::model()->with("owner", "membership", "cat")->findByPk($book_id);
		}
		if(!$this->book) throw new CHttpException(404, "Такъв превод не съществува. Възможно е изтрит или сте въвели грешен адрес. Пробвайте с <a href='/search'>търсене</a>, например.");

		if($this->book->typ == "P") {
			throw new CHttpException(410, "Извинете, разделът за превод на фрази е времено изключен за преосмисляне. Следете <a href='/blog?topic=64'>нашия блог</a>, ако искате първи да разберете, кога е заработил отново.");
		}

		if($this->book->opts_get(Book::OPTS_BAN_COPYRIGHT)) {
			$this->book->facecontrol = Book::FC_INVITE;
			foreach(Yii::app()->params["ac_areas"] as $ac => $title) {
				if($this->book->$ac == "a") $this->book->$ac = "g";
			}

			$reason = BookBanReason::model()->findByPk($this->book->id);
			if(!$reason) $reason = new BookBanReason();

			if(!$this->book->can("read")) {
				$html = "Съжаляваме, но този превод е блокиран по заявка на правообладателя";
				if($reason->url != "") $html .= " <a href='{$reason->url}' rel='nofollow'>{$reason->title}</a>";
				elseif($reason->title != "") $html .= " {$reason->title}";
				if($reason->email != "") $html .= " (<a href='mailto:{$reason->email}'>{$reason->email}</a>)";
				$html .= ".<br /><br />";
				$html .= "<img src='http://img.leprosorium.com/2182718' style='display: block; margin: 20px auto' />";
				throw new CHttpException(403, $html);
			}
		}

		// Формируем понятное сообщение об ошибке, если нам нельзя в этот перевод (!$this->book->can("read"))
		if(!$this->book->can("read")) {
			$msg = $this->book->deniedWhy;

			// Bells & Whistles, показываются только на странице с ошибкой, а не при ajax-запросе
			if(!Yii::app()->request->isAjaxRequest) {

				// Ебала с группами, предлагаем вступить или проверяем, есть ли инвайт
				if(!$this->book->checkMembershipStatus(GroupMember::BANNED)) {
					if($this->book->ac_read == "g") {
						if($this->book->facecontrol == Book::FC_CONFIRM) {
							$msg .= $this->renderPartial("//book/_join", array("book" => $this->book), true);
						} elseif($this->book->facecontrol == Book::FC_INVITE) {
							if($this->book->user_invited(Yii::app()->user->id)) {
								$msg .= "<br /><br />И, между другото, имате тази покана.<br /><br />" .
									"<a href='" . $this->book->getUrl("invite_accept")  . "' class='btn btn-success'><i class='icon-ok icon-white'></i> Приемане</a> " .
									"<a href='" . $this->book->getUrl("invite_decline") . "' class='btn btn-inverse'><i class='icon-remove-sign icon-white'></i> Отказ</a>";
							}
						}
					}
				}

				$msg .= "<br /><br /><a href='/search?t=" . urlencode($this->book->s_title) . "'>Търсене на подобни преводи</a> | ";
				$msg .= "<a href='" . $this->book->owner->getUrl("books") . "'>Други преводи от {$this->book->owner->login}</a> | ";
				if(!Yii::app()->user->isGuest) $msg .= "<a href='/mail/write?to=" . urlencode($this->book->owner->login) . "'>Напиши писмо {$this->book->owner->login}</a> ";
			}

			throw new CHttpException(403, $msg);
		}

		return $this->book;
	}
}
