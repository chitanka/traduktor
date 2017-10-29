<?php
if (! function_exists('bold_if_nonzero')){
    function bold_if_nonzero($t)
    {
        if ($t != 0) return " <b class='nbItems'>({$t})</b>";
        return "";
    }
}

$containerClass = $this->layoutOptions["fluid"] ? "container-fluid" : "container";

Yii::app()->bootstrap->registerModal();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $this->pageTitle != "" ? ($this->pageTitle . " :: ") : ""; ?><?= Yii::app()->name; ?></title>
    <meta name="description" content="Колективни преводи на текстове и субтитри"/>
    <link rel='icon' href='/i/favicon.ico' type='image/x-icon'>
    <link rel='shortcut icon' href='/i/favicon.ico' type='image/x-icon'>
    <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div id="project-links">
	<ul>
		<li id="project-main"><a href="//chitanka.info">Библиотека</a></li>
		<li id="project-biblioman"><a href="//biblioman.chitanka.info">Библиоман</a></li>
		<li id="project-forum"><a href="//forum.chitanka.info">Форум</a></li>
		<li id="project-prevodi" class="current"><a href="//prevodi.chitanka.info">Дачко</a></li>
		<li id="project-wiki-workroom"><a href="//wiki.workroom.chitanka.info">Читалие</a></li>
		<li id="project-rechnik"><a href="//rechnik.chitanka.info">Речник</a></li>
		<li id="project-gramofonche"><a href="//gramofonche.chitanka.info">Грамофонче</a></li>
		<li id="project-tools"><a href="//tools.chitanka.info">Сръчко</a></li>
	</ul>
</div>

<header>
    <div class="<?= $containerClass; ?>" style="background-color:#fff;">
        <a href="/" id="header-logo">
            <img src="/i/logo.png" width="124" height="130"
                 alt="<?= CHtml::encode(Yii::app()->name); ?>">
        </a>
        <nav>
            <ul id="header-menu">
                <li><a href="/catalog/1"><span class="fa fa-book"></span> Текстове</a></li>
                <li><a href="/catalog/2"><span class="fa fa-film"></span> Субтитри</a></li>
                <li><a href="/users"><span class="fa fa-users"></span> Преводачи</a></li>
<!--                <li><a href="/blog">Блог</a></li>-->
                <li><a href="/announces"><span class="fa fa-globe"></span> Анонси</a></li>
                <li class="search">
                    <form class="form-search" method="get" action="/search">
                        <input type="hidden" name="from" value="header">
                        <input type="text" name="t" class="input-medium search-query span3"
                               placeholder="Търсене"
                               title="За да намерите преводач, сложете @ пред името му, напр. @chitanka"/>
                        <input type="submit" value="&raquo;" class="btn" style="border-radius: 20px;"/>
                    </form>
                </li>
            </ul>
            <ul id="header-submenu">
                <?php if (Yii::app()->user->isGuest): ?>
                    <li><a href="//chitanka.info/login?returnto=<?= 'http://', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ?>"><strong>Вход</strong></a></li>
<!--                    <li id="header-login">-->
<!--                        <form method="post" action="/" class="form-inline btn-toolbar">-->
<!--                            <input type="text" name="login[login]" placeholder="Логин" class="span1"/>-->
<!--                            <input type="password" name="login[pass]" placeholder="Пароль" class="span1"/>-->
<!--                            <input type="submit" value="Войти" class="btn"/>-->
<!--                        </form>-->
<!--                    </li>-->
<!--                    <li><a href="/register"><strong>Зарегистрироваться</strong></a></li>-->
<!--                    <li><a href="/register/remind"><strong>Напомнить пароль</strong></a></li>-->
<!--                    <li><p>Зарегистрировавшись, вы сможете добавлять свои версии перевода, ставить оценки переводам.</p></li>-->
                <?php else: ?>
                    <li>
                        <a href="<?= Yii::app()->user->url; ?>" accesskey="i">
							<span class="fa fa-user"></span>
							<?= Yii::app()->user->login; ?>
                        </a>
                    </li>
                    <li id="hm-c">
                        <a href="/my/comments" accesskey="c">
							<span class="fa fa-comments"></span>
                            Коментари <?php echo bold_if_nonzero(Yii::app()->user->newComments); ?>
                        </a>
                    </li>
                    <li id="hm-n">
                        <a href="/my/notices" accesskey="n">
							<span class="fa fa-inbox"></span>
                            Известия <?php echo bold_if_nonzero(Yii::app()->user->newNotices); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#bookmarks" accesskey="b">
							<span class="fa fa-bookmark"></span>
                            Отметки
                        </a>
                    </li>
                    <li><a href="/book/0/edit">
							<span class="fa fa-plus"></span>
							Нов превод
						</a></li>
                    <li><a href="/register/settings">
							<span class="fa fa-cogs"></span>
							Настройки
						</a></li>
<!--                    <li><a href="/register/logout"><strong>Выход</strong> До свидания :(</a></li>-->
                <?php endif ?>
            </ul>
        </nav>
    </div>
</header>


<?php
if (!Yii::app()->user->isGuest):
    Yii::app()->bootstrap->registerButton();
    ?>
    <div id="bookmarks" class="modal hide">
        <div class="modal-header" style='padding-bottom:0;'>
            <a class="close" data-dismiss="modal">×</a>
            <h3>Закладки</h3>
            <div class="btn-toolbar">
                <div class="btn-group" data-toggle="buttons-radio" id="bookmarks-tb-sort">
                    <button class="btn btn-mini" data-v="1" title="Сортировка по алфавиту">
                        <i class="icon-text-height"></i>
                    </button>
                    <button class="btn btn-mini" data-v="2" title="Сортировка по вашей активности">
                        <i class="icon-fire"></i>
                    </button>
                    <button class="btn btn-mini" data-v="3" title="Сортировка по времени вступления в перевод">
                        <i class="icon-time"></i>
                    </button>
                    <button class="btn btn-mini" data-v="5" title="Сортировка по времени добавления закладки">
                        <i class="icon-shopping-cart"></i>
                    </button>
                    <button class="btn btn-mini" data-v="4" title="Сортировка по готовности перевода">%</button>
                    <button class="btn btn-mini" data-v="0" title="Ваша сортировка (таскайте закладки мышкой)">
                        <i class="icon-random"></i>
                    </button>
                </div>

                <div class="btn-group" data-toggle="buttons-radio" id="bookmarks-tb-title">
                    <button class="btn btn-mini" data-v="s" title="Показывать названия на языке оригинала">О</button>
                    <button class="btn btn-mini" data-v="t" title="Показывать названия на языке перевода">П</button>
                </div>
                <?php if (0): // @todo ?>
                    <div class="btn-group" id="bookmarks-tb-status">
                        <button class="btn btn-mini" data-toggle="button" title="Только те, где я - модератор"><i
                                class="icon-briefcase"></i></button>
                    </div>
                <?php endif; ?>
                <div class="btn-group pull-right">
                    <button class="btn btn-mini" title="Удалить" id="bookmarks-tb-rm">
                        <i class="icon-remove"></i>
                    </button>
                    <button class="btn btn-mini" title="Редактировать" id="bookmarks-tb-ed">
                        <i class="icon-edit"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-body" style="max-height:350px">
            <p class='loading'>минуточку...</p>
        </div>
    </div>
<?php endif; ?>


<div class="<?= $containerClass; ?>">
    <?php
    $this->widget('bootstrap.widgets.TbAlert');
    echo $content;
    ?>
</div>


<footer>
    <div class="<?= $containerClass; ?>">
        <div style="text-align: center">
            <a href="/site/help">Помощ</a>
        </div>
        <div style="text-align: right; opacity: .6">
            Създадено от <a href="http://romakhin.ru/" rel="nofollow">Дмитрий Ромахин</a>, донапаснато от Читанка
        </div>
    </div>
</footer>
<?php
$excluded_routes = [
    'book\/\d+\/\d+\/import',
];
$pattern = implode('|', array_map(function ($val) {return "($val)";}, $excluded_routes));
$matches = [];
preg_match("/{$pattern}/", Yii::app()->request->requestUri, $matches);
if (false && count($matches) == 0) : ?>
<script src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML&locale=bg"></script>
<script type="text/x-mathjax-config">
    MathJax.Hub.Config({
        tex2jax: {
            inlineMath: [['$', '$']],
            displayMath: [['$$', '$$']]
        },
        asciimath2jax: {
            delimiters: [['``', '``']]
        }
    });
</script>
<?php endif; ?>
</body>
</html>
