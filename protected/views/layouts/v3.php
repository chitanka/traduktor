<?php
if (! function_exists('bold_if_nonzero')){
    function bold_if_nonzero($t)
    {
        if ($t != 0) return " <b>({$t})</b>";
        else return " <b></b>";
    }
}

$containerClass = $this->layoutOptions["fluid"] ? "container-fluid" : "container";

Yii::app()->bootstrap->registerModal();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $this->pageTitle != "" ? ($this->pageTitle . " :: ") : ""; ?><?= Yii::app()->name; ?></title>
    <meta name="description" content="Коллективные переводы субтитров и книжек"/>
    <meta name="language" content="ru"/>
    <meta name="keywords"
          content="перевод, translation, коллективный перевод, кино с субтитрами, кино по-английски с русскими субтитрами, кино по-немецки с русскими субтитрами, кино по-испански с русскими субтитрами, примеры перевода, краудсорсинг, crowdsourcing, нотабеноид"/>
    <link rel='icon' href='/i/favicon.ico' type='image/x-icon'>
    <link rel='shortcut icon' href='/i/favicon.ico' type='image/x-icon'>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <!--[if IE]>
    <script type="text/javascript" src="/js/jquery.input-placeholder.js"></script>
    <link rel='stylesheet' href="/css/jquery.input-placeholder.css"/>
    <script type="text/javascript">
        $(function () {
            $('input[placeholder]').inputDefault();
        });
    </script>
    <![endif]-->
</head>
<body>

<header>
    <div class="<?= $containerClass; ?>" style="background-color:#fff;">
        <a href="/" id="header-logo">
            <img src="/i/logo.png" width="124" height="130"
                 style="padding-top: 50px;"
                 alt="<?= CHtml::encode(Yii::app()->name); ?>"
                 title="<?= appParams()["version"]; ?>"/>
        </a>
        <nav>
            <ul id="header-menu">
                <li><a href="/catalog/1">ВИДЕО</a></li>
                <li><a href="/catalog/2">ТЕКСТЫ</a></li>
                <li><a href="/users">ПЕРЕВОДЧИКИ</a></li>
                <li><a href="/blog">БЛОГ</a></li>
                <li><a href="/announces">АНОНСЫ</a></li>
                <li class="search">
                    <form class="form-search" method="get" action="/search">
                        <?php
                        $a = array("Вы что-то потеряли?", "Ищете что-нибудь?", "Поиск переводов", "Ищите и обрящете");
                        ?>
                        <input type="hidden" name="from" value="header">
                        <input type="text" name="t" class="input-medium search-query span3"
                               placeholder="<?= $a[rand(0, count($a) - 1)]; ?>"
                               title="Кстати, отсюда можно найти и переводчика, если собаку (@), а потом сразу его ник."/>
                        <input type="submit" value="&raquo;" class="btn" style="border-radius: 20px;"/>
                    </form>
                </li>
            </ul>
            <ul id="header-submenu">
                <?php if (Yii::app()->user->isGuest): ?>
                    <li id="header-login">
                        <form method="post" action="/" class="form-inline btn-toolbar">
                            <input type="text" name="login[login]" placeholder="Логин" class="span1"/>
                            <input type="password" name="login[pass]" placeholder="Пароль" class="span1"/>
                            <input type="submit" value="Войти" class="btn"/>
                        </form>
                    </li>
                    <li><a href="/register"><strong>Зарегистрироваться</strong></a></li>
                    <li><a href="/register/remind"><strong>Напомнить пароль</strong></a></li>
                    <li><p>Зарегистрировавшись, вы сможете добавлять свои версии перевода, общаться в блоге, ставить
                            оценки переводам.</p></li>
                <?php else: ?>
                    <li>
                        <a href="<?= Yii::app()->user->url; ?>" accesskey="i">
                            <strong><?= Yii::app()->user->login; ?></strong>
                            Всё о вас
                        </a>
                    </li>
                    <li id="hm-c">
                        <a href="/my/comments" accesskey="c">
                            <strong>Обсуждения <?php echo bold_if_nonzero(Yii::app()->user->newComments); ?></strong>
                            Места, где вы общались
                        </a>
                    </li>
                    <li id="hm-n">
                        <a href="/my/notices" accesskey="n">
                            <strong>Оповещения <?php echo bold_if_nonzero(Yii::app()->user->newNotices); ?></strong>
                            События для вас
                        </a>
                    </li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#bookmarks" accesskey="b">
                            <strong>Закладки</strong>
                            На память
                        </a>
                    </li>
                    <li><a href="/book/0/edit"><strong>Создать</strong> Перевод</a></li>
                    <li><a href="/register/settings"><strong>Настройки</strong> Профиля</a></li>
                    <li><a href="/register/logout"><strong>Выход</strong> До свидания :(</a></li>
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
        <div class="row">
            <div class="span6">
                &copy; <a href="http://romakhin.ru/" rel="nofollow">Дмитрий Ромахин</a>
                2008&ndash;<?php echo date("Y"); ?>
                <br/>
                <a href="/site/help">Справка</a> |
                <a href="/blog?topic=65">Техподдержка</a> |
                <a href="mailto:abuse@<?= appParams()["domain"]; ?>?subj=<?= urlencode($_SERVER["REQUEST_URI"]); ?>">Abuse</a> |
                <a href="mailto:<?= appParams()["adminEmail"]; ?>">E-mail для справок</a> |
            </div>
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
if (count($matches) == 0) : ?>
<script src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML&locale=ru"></script>
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
