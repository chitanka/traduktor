<?php
return CMap::mergeArray(array(
	"basePath" => __DIR__.DIRECTORY_SEPARATOR."..",
	"name" => "Дачко",
	"language" => "bg",
	"sourceLanguage" => "en",

	"preload" => array("log", "bootstrap"),

	"import"=>array(
		"application.models.*",
		"application.components.*",
		"ext.yii-mail.YiiMailMessage",
	),

	"modules"=>array(
	),

	"components" => array(
		"request" => array(
			"enableCookieValidation" => true,
		),
		"urlManager" => array(
			"urlFormat" => "path",
			"showScriptName" => false,
			"rules" => array(
				'users/<id:\d+>' => 'users/books',
				'users/<id:\d+>/<action:\w+>' => 'users/<action>',
				'users/<id:\d+>/translations/<book_id:\d+>' => 'users/translations',

				'book/<book_id:\d+>/blog' => 'bookBlog/index',
				'book/<book_id:\d+>/blog/<post_id:\d+>' => 'bookBlog/post',
				'book/<book_id:\d+>/blog/<post_id:\d+>/c<comment_id:\d+>/<action>' => 'bookBlog/comment_<action>',
				'book/<book_id:\d+>/blog/<post_id:\d+>/<action:\w+>' => 'bookBlog/<action>',
				'book/<book_id:\d+>/blog/edit' => 'bookBlog/edit',

				'book/<book_id:\d+>/announces' => 'announces/book',
				'book/<book_id:\d+>/announces/<post_id:\d+>' => 'announces/post',
				'book/<book_id:\d+>/announces/<post_id:\d+>/c<comment_id:\d+>/<action>' => 'announces/comment_<action>',
				'book/<book_id:\d+>/announces/<post_id:\d+>/<action:\w+>' => 'announces/<action>',
				'book/<book_id:\d+>/announces/write' => 'announces/edit',

				'book/<book_id:\d+>/edit' => 'bookEdit/index',
				'book/<book_id:\d+>/edit/<action:\w+>' => 'bookEdit/<action>',

				'book/<book_id:\d+>' => 'book/index',
				'book/<book_id:\d+>/<chap_id:\d+>' => 'chapter/index',
				'book/<book_id:\d+>/<chap_id:\d+>/<orig_id:\d+>' => 'orig/index',
				'book/<book_id:\d+>/<chap_id:\d+>/<orig_id:\d+>/c<comment_id:\d+>/<action:\w+>' => 'orig/comment_<action>',
				'book/<book_id:\d+>/<chap_id:\d+>/<orig_id:\d+>/<action:\w+>' => 'orig/<action>',
				'book/<book_id:\d+>/<chap_id:\d+>/<action:\w+>' => 'chapter/<action>',
				'book/<book_id:\d+>/<action:\w+>' => 'book/<action>',

				'blog/<post_id:\d+>' => 'blog/post',
				'blog/<post_id:\d+>/c<comment_id:\d+>/<action>' => 'blog/comment_<action>',
				'blog/<post_id:\d+>/<action:\w+>' => 'blog/<action>',

				'chat/room/<room_id:\d+>' => 'chat/room',

				'my/comments' => 'myComments/index',
				'my/comments/<action:\w+>' => 'myComments/<action>',

				'my/bookmarks' => 'Bookmarks/index',
				'my/bookmarks/<action:\w+>' => 'Bookmarks/<action>',

				'my/mail/' => 'mail/index',
				'my/mail/<id:\d+>' => 'mail/message',
				'my/mail/<action:\w+>' => 'mail/<action>',

				'catalog/<cat_id:\d+>' => 'catalog/index',

				'site/login' => 'register/login',
			),
		),
		"db" => [
			"connectionString" => "pgsql:host=localhost;dbname=notabenoid",
			"username" => "notabenoid",
			"password" => "",
			"charset" => "utf8",
			"emulatePrepare" => true,
			"enableProfiling" => true,
			"schemaCachingDuration" => 6 * 60 * 60,
			"queryCacheID" => "cache",
		],
		"session" => [
			"class" => "CHttpSession",
			"cookieParams" => [
				"lifetime" => 86400 * 365
			],
			"timeout" => 86400 * 365
		],
		"cache" => [
			"class" => "system.caching.CMemCache",
			"servers" => [
				["host" => "localhost", "port" => 11211, "weight" => 100],
			],
			"keyPrefix" => "nb"
		],
		"readycache" => array(
			"class" => "application.components.ReadyCache",
			"directoryLevel" => "3",
			"gCProbability" => 0,	// garbage collection - только вручную, ну его нахуй
		),
		'errorHandler' => array(
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class' => 'CLogRouter',
			'routes' => [
				['class'=>'CFileLogRoute', 'levels'=>'error, warning'],
			],
		),
		"widgetFactory" => array(
			"widgets" => array(
				"CActiveForm" => array(
				),
				"TbPager" => array(
					"maxButtonCount" => 20,
					"header" => "<div class='pagination'>",
					"footer" => "</div>",
					"displayFirstAndLast" => true,
					"firstPageLabel" => "&laquo;&laquo;&laquo;",
					"lastPageLabel" => "&raquo;&raquo;&raquo;",
					"nextPageLabel" => "&raquo;",
					"prevPageLabel" => "&laquo;",
				),
				"CLinkPager" => array(
					"maxButtonCount" => 20,
					"cssFile" => "/css/pager.css",
					"header" => false,
					"firstPageLabel" => "&laquo;&laquo;&laquo;",
					"lastPageLabel" => "&raquo;&raquo;&raquo;",
					"nextPageLabel" => "&raquo;",
					"prevPageLabel" => "&laquo;",
				),
				"CGridView" => array(
					"template" => "{pager}\n{items}\n{pager}",
					"cssFile" => "/css/grid.css",
					"rowCssClass" => "",
					"selectableRows" => 0,
				),
				"CHtmlPurifier" => array(
					"options" => array(
						"HTML.Allowed" => "a[href],b,strong,i,em,u",
					)
				)
			),
		),

		/**
		* 3rd party-компоненты
		*/
		"mail" => array(
			"class" => "ext.yii-mail.YiiMail",
			"transportType" => "php",
			"viewPath" => "application.views.email",
		),
		"bootstrap" => array(
			'class' => 'ext.bootstrap.components.Bootstrap',
			'coreCss' => true,
			'responsiveCss' => true,
			'plugins' => array(
				'transition' => false, // disable CSS transitions
				'tooltip' => array(
					'selector' => 'a.tooltip', // bind the plugin tooltip to anchor tags with the 'tooltip' class
					'options' => array(
						'placement' => 'bottom', // place the tooltips below instead
					),
				),
			),
		),
		'curl' => array(
			'class' => 'application.extensions.curl.Curl',
			"options" => array(
				"timeout" => 30,
				"setOptions" => array(
					CURLOPT_USERAGENT => "Translation Service (support@chitanka.info)",
					CURLOPT_RANGE => "0-2048000",   // Качаем не более 2 мегов
					CURLOPT_TIMEOUT => 15,
				),
			),
		),
		"filecache" => array(
			"class" => "system.caching.CFileCache",
		),

		/**
		* Мои компоненты
		*/
		'user' => array(
			"class" => "application.components.WebUser",
			'allowAutoLogin' => true,
			"autoRenewCookie" => true,
		),
		"langs" => array(
			"class" => "application.components.Langs",
		),
		"parser" => array(
			"class" => "application.components.Parser",
		),
	),


	'params' => array(
		"domain" => "notabenoid.org",
		"passwordSalt" => "------------------------------------>>> ПРИДУМАЙТЕ СЮДА ЧТО-НИБУДЬ !!! <<<------------------",
		"adminEmail" => 'support@notabenoid.org',
		"commentEmail" => "comment@notabenoid.org",
		"systemEmail" => "no-reply@notabenoid.org",
		/**
		 * Тип регистрации: OPEN - открытая, INVITE - по инвайтам
		 */
		"registerType" => "OPEN",

		"HTMLPurifierOptions" => array(
			"HTML.Allowed" => "a[href],b,strong,i,em,u,s,blockquote,table,tr,th,td,ul,ol,li,dl,dt,dd,br,img[src],small,sub,sup,font[color],span,abbr,*[title],code,tt",
		),
		"sex" => array("m" => "мъж", "f" => "жена", "x" => "същество"),
		"countries" => array(
			"",
			1 => "Русия", 2 => "Украина", 3 => "Белоруссия", 4 => 'Абхазия', 5 => 'Австралия', 6 => 'Австрия', 7 => 'Азербайджан', 8 => 'Албания', 9 => 'Алжир',
			10 => 'Ангола', 11 => 'Андора', 12 => 'Антигуа и Барбуда', 13 => 'Аржентина', 14 => 'Армения', 15 => 'Афганистан', 16 => 'Бахамски острови', 17 => 'Бангладеш', 18 => 'Барбадос', 19 => 'Бахрейн',
			20 => 'Белиз', 21 => 'Белгия', 22 => 'Бенин', 23 => 'България', 24 => 'Боливия', 25 => 'Босна и Херцеговина', 26 => 'Ботсвана', 27 => 'Бразилия', 28 => 'Бруней', 29 => 'Буркина-Фасо',
			30 => 'Бурунди', 31 => 'Бутан', 32 => 'Вазиристан', 33 => 'Вануату', 34 => 'Ватиканът', 35 => 'Великобритания', 36 => 'Унгария', 37 => 'Венецуела', 38 => 'Източен Тимор (Тимор-Лести)', 39 => 'Виетнам',
			40 => 'Габон', 41 => 'Хаити', 42 => 'Гвиана', 43 => 'Гамбия', 44 => 'Гана', 45 => 'Гуатемала', 46 => 'Гвинея', 47 => 'Гвинея-Бисау', 48 => 'Германия', 49 => 'Хондурас',
			50 => 'Гренада', 51 => 'Гърция', 52 => 'Грузия', 53 => 'Дания', 54 => 'Джибути', 55 => 'Доминика', 56 => 'Доминиканска Република', 57 => 'Египет', 58 => 'Замбия', 59 => 'Зимбабве',
			60 => 'Израел', 61 => 'Индия', 62 => 'Индонезия', 63 => 'Йордания', 64 => 'Ирак', 65 => 'Иран', 66 => 'Ирландия', 67 => 'Исландия', 68 => 'Испания', 69 => 'Италия',
			70 => 'Йемен', 71 => 'Кабо-Верде', 72 => 'Казахстан', 73 => 'Камбоджа', 74 => 'Камерун', 75 => 'Канада', 76 => 'Катар', 77 => 'Кения', 78 => 'Кипър', 79 => 'Киргизстан',
			80 => 'Кирибати', 81 => 'Китай', 82 => 'Коморски острови', 83 => 'Република Конго', 84 => 'Конго, Демократична република (Заир)', 85 => 'Колумбия', 86 => 'Корея (Северна)', 87 => 'Корея (Южна)', 88 => 'Косово', 89 => 'Коста-Рика',
			90 => 'Кот-д\'Ивуар', 91 => 'Куба', 92 => 'Кувейт', 93 => 'Лаос', 94 => 'Латвия', 95 => 'Лесото', 96 => 'Либерия', 97 => 'Ливан', 98 => 'Либия', 99 => 'Литва',
			100 => 'Лихтенщайн', 101 => 'Люксембург', 102 => 'Маврикий', 103 => 'Мавритания', 104 => 'Мадагаскар', 105 => 'Македония', 106 => 'Малави', 107 => 'Малайзия', 108 => 'Мали', 109 => 'Малдивски острови',
			110 => 'Малта', 111 => 'Мароко', 112 => 'Маршаллови Острови', 113 => 'Мексико', 114 => 'Мозамбик', 115 => 'Молдова', 116 => 'Монако', 117 => 'Монголия', 118 => 'Мианмар', 119 => 'Нагорно-Карабахска Република',
			120 => 'Намибия', 121 => 'Науру', 122 => 'Непал', 123 => 'Нигер', 124 => 'Нигерия', 125 => 'Нидерландия', 126 => 'Никарагуа', 127 => 'Нова Зеландия', 128 => 'Норвегия', 129 => 'Обединени Арабски Емирства',
			130 => 'Оман', 131 => 'Пакистан', 132 => 'Палау', 133 => 'Панама', 134 => 'Папуа', 135 => 'Парагвай', 136 => 'Перу', 137 => 'Полша', 138 => 'Португалия', 139 => 'Приднестровска Молдовска Република',
			140 => 'Пунтленд', 141 => 'Руанда', 142 => 'Румъния', 143 => 'Салвадор', 144 => 'Самоа', 145 => 'Сан-Марино', 146 => 'Сан-Томе и Принсипи', 147 => 'Саудитска Арабия', 148 => 'Свазиленд', 149 => 'Сейшелски острови',
			150 => 'Сенегал', 151 => 'Сент-Винсент и Гренадини', 152 => 'Сейнт-Китс и Невис', 153 => 'Сент-Лусия', 154 => 'Сърбия', 155 => 'Силенд', 156 => 'Сингапур', 157 => 'Сирия', 158 => 'Словакия', 159 => 'Словения',
			160 => 'Съединени Америскански Щати', 161 => 'Соломонови Острова', 162 => 'Сомалия', 163 => 'Сомалиленд', 164 => 'Судан', 165 => 'Суринам', 166 => 'Сиера-Леоне', 167 => 'Таджикистан', 168 => 'Тайланд', 169 => 'Тайван',
			170 => 'Тамил-Илам', 171 => 'Танзания', 172 => 'Того', 173 => 'Тонга', 174 => 'Тринидад и Тобаго', 175 => 'Тувалу', 176 => 'Тунис', 177 => 'Туркменистан', 178 => 'Турция', 179 => 'Турска Република Северен Кипър',
			180 => 'Уганда', 181 => 'Узбекистан', 182 => 'Уругвай', 183 => 'Федеративные Штаты Микронезии', 184 => 'Фиджи', 185 => 'Филиппины', 186 => 'Финляндия', 187 => 'Франция', 188 => 'Хорватия', 189 => 'Центрально-Африканская Республика',
			190 => 'Чад', 191 => 'Черна гора', 192 => 'Чехия', 193 => 'Чили', 194 => 'Швейцария', 195 => 'Швеция', 196 => 'Шри-Ланка', 197 => 'Еквадор', 198 => 'Екваториална Гвинея', 199 => 'Еритрея',
			200 => 'Естония', 201 => 'Етиопия', 202 => 'Южноафриканска Република', 203 => 'Южна Осетия', 204 => 'Ямайка', 205 => 'Япония'
		),
		"month_acc" => array("", "януари", "февруари", "март", "април", "май", "юни", "юли", "август", "семтември", "октомври", "ноември", "декември"),
		"month_in" => array("", "януари", "февруари", "март", "април", "май", "юни", "юли", "август", "семтември", "октомври", "ноември", "декември"),
		"encodings" => array(
			// iconv-название => человеческое название
			"UTF-8" => "UTF-8",
			"CP1251" => "Windows-1251 (Кирилица Windows)",
			"CP1252" => "Windows-1252 (Западноевропейска латиница)",
			"KOI8-R" => "KOI8-R (руска KOI8)",
			"KOI8-U" => "KOI8-U (украинска KOI8)",
			"utf-16" => "Unicode UTF-16 (16-битов уникод)",
			"MacCyrillic" => "MacCyrillic (Кирилица Macintosh)",
			"MacCentralEurope" => "MacCentralEurope (Централна Европа Macintosh)"
		),
		"book_types" => array("A" => "текст", "S" => "субтитри"),
		"catalog_branches" => array(1 => "A", 2 => "S"),
		"book_topics" => array(
			'S' => array(
				0 => "Сериал",
				1 => "Анимационен филм",
				2 => "Документален филм",
				3 => "Фантастика",
				4 => "Комедия",
				5 => "Драма",
				6 => "Екшън, приключения",
				7 => "Ужаси, трилър",
				8 => "Детектив",
				9 => "Мелодрама",
				10 => "Мюзикъл",
			),
			'A' => array(
				0 => "Класическа литература",
				1 => "Художественна литература",
				10 => "Научна фантастика",
				2 => "Техническа литература",
				7 => "Детка литература",
				3 => "Поезия",
				4 => "Публицистика",
				5 => "Научни статии",
				6 => "Колективно творчество",
				8 => "Комикси",
				9 => "Игри",
				11 => "Стихове и песни",
			),
		),
		"ac_areas" =>  array(
			"ac_read" => "да влиза", "ac_trread" => "да вижда всички версии", "ac_gen" => "да сваля", "ac_rate" => "да оценява", "ac_comment" => "да коментира", "ac_tr" => "да превежда",
			"ac_blog_r" => "да чете блога", "ac_blog_c" => "да коментирa в блогa", "ac_blog_w" => "да пише постове в блога",
			"ac_announce" => "да пуска анонси относно превода", "ac_membership" => "да управлява членството в групата на превода",
			"ac_chap_edit" => "да редактира оригинала", "ac_book_edit" => "да редактира описанието на превода",
		),
		"ac_areas_chap" => array("ac_read" => "да чете", "ac_trread" => "да вижда всички версии", "ac_gen" => "да сваля", "ac_rate" => "да оценява", "ac_comment" => "да коментира", "ac_tr" => "да превежда"),
		"ac_roles" => array("a" => "всички", "g" => "група", "m" => "модератори", "o" => "никой"),
		"ac_roles_title" => array("a" => "всички", "g" => "само членове на преводаческата група", "m" => "само модератори", "o" => "само собственикът"),

		"translation_statuses" => array(
			0 => "",
			1 => "в процес на превод",
			2 => "преводът се редактира",
			3 => "готов превод",
		),
		"translation_statuses_short" => array(
			0 => "",
			1 => "превежда се",
			2 => "редактира се",
			3 => "готово",
		),

		"blog_topics" => array(
			"book" => array(	// 1 - 19
				1 => "Обсъждане на оригинала",
				2 => "Превод",
				3 => "Общуване",
			),
			"common" => 	array(	// 40 - 79
				64 => "Новини за проекта",
				65 => "Техническа поддръжка",
				66 => "Общуване",
				67 => "Хумор",
				69 => "Как да се преведе?",

//				70 => array("Стройплощадка", "can" => "betatest", "manifest" => "Этот блог доступен только участникам Стройплощадки.", "side_view" => "betatest_side"),
			),
			"announce" => array(	// 80 - 89
				81 => "Търсим преводачи",
				82 => "Готово",
				89 => "Всички"
			),
		),

		"ENVIRONMENT" => "production",
		"version" => "3.4"
	),
), require(__DIR__.'/main_custom.php'));
