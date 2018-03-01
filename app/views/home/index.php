<h1>Локалізація інтерфейсу (gettext)</h1>
<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link" id="nav-language-tab" data-toggle="tab" href="#nav-language" role="tab" aria-controls="nav-language" aria-selected="false">
			app/language/*
		</a>
		<a class="nav-item nav-link" id="nav-config-tab" data-toggle="tab" href="#nav-config" role="tab" aria-controls="nav-config" aria-selected="false">
			app/config/config-lang.php
		</a>
		<a class="nav-item nav-link" id="nav-controller-tab" data-toggle="tab" href="#nav-controller" role="tab" aria-controls="nav-controller" aria-selected="false">
			app/core/Controller.php
		</a>
		<a class="nav-item nav-link" id="nav-html-tab" data-toggle="tab" href="#nav-html" role="tab" aria-controls="nav-html" aria-selected="false">
			[View]
		</a>
		<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
			[Результат]
		</a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade" id="nav-language" role="tabpanel" aria-labelledby="nav-language-tab">
		<code><pre>
\en_US\LC_MESSAGES\
	en_US.mo
	en_US.po
\uk_UA\LC_MESSAGES\
	uk_UA.mo
	uk_UA.po
\website.pot

		</pre></code>
	</div>
	
	<div class="tab-pane fade" id="nav-config" role="tabpanel" aria-labelledby="nav-config-tab">
		<code><pre>
[...]
// Мови сайту
const SITELANGS = [
	'uk' => [
		'name'		=>	'Українська',
		'locale'	=>	'uk_UA', // putenv
		'setlocale'	=>	'uk_UA.UTF8',
	],
	'en' => [
		'name'		=>	'English',
		'locale'	=>	'en_US',
		'setlocale'	=>	'en_US.UTF8',
	],
];

// Залежність "Мова браузера" — "Мова сайту"
const LANG_REDIRECTION = [
	'uk'		=>	'uk',
	'uk-UA'		=>	'uk',
	'ru'		=>	'uk',
	'ru-RU'		=>	'uk',
	'ru-MO'		=>	'uk',
	'en'		=>	'en',
	'en-GB'		=>	'en',
	'en-US'		=>	'en',
	'default'	=>	'uk',
];
		</pre></code>
	</div>
	<div class="tab-pane fade" id="nav-controller" role="tabpanel" aria-labelledby="nav-controller-tab">
		<code><pre>
// Головна змінна — $_SESSION['language']

class Controller {
	protected $db;
	protected $member;

	public function __construct() {
		[...]

		// Десь тут визначається "користувач сайту" і вибрана ним "мова інтерфейсу" ($this->member->language)
		[...]

		// Мова користувача
		$this->setSessionLanguage();

		// Текстівки загального призначення вибраною мовою
		include '../app/config/config-options.php';
	}

	[...]

	// ————————————————————————————————————————————————————————————————————————————————
	// Виставлення $_SESSION['language']
	protected function setSessionLanguage() {
		// Мова береться з URL
		$lang = filter_input(INPUT_GET, 'hl');
		// Якщо відсутня чи неправильна, береться з інших можливих місць
		if (!$lang or !array_key_exists($lang, SITELANGS)) {
			if (isset($_COOKIE['language'])) {
				$lang = $_COOKIE['language'];
			} elseif (isset($_SESSION['language'])) {
				$lang = $_SESSION['language'];
			} elseif (isset($this->member->language) and $this->member->language) {
				$lang = $this->member->language;
			} else {
				$lang = $this->getBestLanguageForUser();
			}
		}
		if (!$lang or !array_key_exists($lang, SITELANGS)) $lang = CONFIG['default_language'];

		$_SESSION['language'] = $lang;
		$this->setLanguage($_SESSION['language']);
		set_cookie('language', $lang, 365);

		//
	}

	// ————————————————————————————————————————————————————————————————————————————————
	// Визначення серед доступних на сайті мов найкращої для користувача
	private function getBestLanguageForUser() {
		$langs = [];
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// розбиття рядка на частини (мови і значення q-фактора)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
			if (count($lang_parse[1])) {
				// створення списку на зразок 'en' => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);
				// виставлення значення за замовчуванням 1 усім мовам, для яких не вказаний q-фактор
				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}
				// сортування масиву мов за значенням q-фактора 
				arsort($langs, SORT_NUMERIC);
			}
		}
		// перебір мов користувача, щоб знайти серед них ту, що доступна на сайті
		foreach ($langs as $lang => $val) {
			if (array_key_exists($lang, LANG_REDIRECTION) and LANG_REDIRECTION[$lang]) {
				return LANG_REDIRECTION[$lang];
				break;
			}
		}
		// Повернення мови за замовчуванням
		return false;
	}

	// ————————————————————————————————————————————————————————————————————————————————
	// Налаштування середовища — мова локалі
	public function setLanguage($language) {
		putenv('LC_ALL=' . SITELANGS[$language]['locale']);
		setlocale(LC_ALL, SITELANGS[$language]['locale'] . '.UTF-8');
		
		// Ім’я файлів з текстівками
		// $domain_name = 'messages';
		$domain_name = SITELANGS[$language]['locale'];

		// Верхня директорія з перекладами
		bindtextdomain($domain_name, '../app/language');
		
		// domain
		textdomain($domain_name);
		bind_textdomain_codeset($domain_name, 'UTF-8');
	}

	[...]
}
		</pre></code>
	</div>

	<div class="tab-pane fade" id="nav-html" role="tabpanel" aria-labelledby="nav-html-tab">
		<code><pre>
<?=html_escape("<h2><?=_('My header')?></h2>\n<p><?=_('My English text.')?></p>\n<p><?=_('Мій український текст.')?></p>")?>
		</pre></code>
	</div>

	<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
		<h2><?=_('My header')?></h2>
		<p><?=_('My English text.')?></p>
		<p><?=_('Мій український текст.')?></p>
	</div>
</div>

<!-- ///////////////////////////////////////////////////////////////////////////////////////// -->
<hr>
<div class="container-fluid" data-oa-main id="dbexample-wrapper">
	<h1>Локалізація даних у БД</h1>
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<a class="nav-item nav-link" id="nav-db-tab" data-toggle="tab" href="#nav-db" role="tab" aria-controls="nav-db" aria-selected="false">
				[БД]
			</a>
			<a class="nav-item nav-link" id="nav-controller1-tab" data-toggle="tab" href="#nav-controller1" role="tab" aria-controls="nav-controller1" aria-selected="false">
				[Controller]
			</a>
			<a class="nav-item nav-link" id="nav-itemlisttpl-tab" data-toggle="tab" href="#nav-itemlisttpl" role="tab" aria-controls="nav-itemlisttpl" aria-selected="false">
				[View]
			</a>
			<a class="nav-item nav-link active" id="nav-itemlist-tab" data-toggle="tab" href="#nav-itemlist" role="tab" aria-controls="nav-itemlist" aria-selected="true">
				[Результат]
			</a>
			<a class="nav-item nav-link" id="nav-dbdatalang-tab" data-toggle="tab" href="#nav-dbdatalang" role="tab" aria-controls="nav-dbdatalang" aria-selected="false">
				[Вибрати іншу мову, ніж мова інтерфейсу]
			</a>
		</div>
	</nav>

	<div class="tab-content" id="nav-tabContent1">
		<div class="tab-pane fade" id="nav-db" role="tabpanel" aria-labelledby="nav-db-tab">
			<code><pre>
	-- Таблиці

	CREATE TABLE `example_l10n_items` (
	  `item_id` int(10) UNSIGNED NOT NULL,
	  `itemname` varchar(255) COLLATE utf8_unicode_ci NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	CREATE TABLE `example_l10n_items_t` (
	  `item_id` int(10) UNSIGNED NOT NULL,
	  `lang` char(3) COLLATE utf8_unicode_ci NOT NULL,
	  `itemname` varchar(255) COLLATE utf8_unicode_ci NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	-- Дані 

	INSERT INTO `example_l10n_items` (`item_id`, `itemname`) VALUES
	(26, 'Кам’янське'),
	[...]
	(217, 'Київ, Солом’янський район');

	INSERT INTO `example_l10n_items_t` (`item_id`, `lang`, `itemname`) VALUES
	(26, 'en', 'Kamianske'),
	(26, 'ru', 'Камьянское'),
	(26, 'uk', 'Кам’янське'),
	[...]
	(217, 'en', 'Kyiv, Solomyanskyi district');

	-- Індекси 

	ALTER TABLE `example_l10n_items`
	  ADD PRIMARY KEY (`item_id`);

	ALTER TABLE `example_l10n_items_t`
	  ADD PRIMARY KEY (`item_id`,`lang`);

	ALTER TABLE `example_l10n_items`
	  MODIFY `item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

	ALTER TABLE `example_l10n_items_t`
	  ADD CONSTRAINT `example_l10n_items_t_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `example_l10n_items` (`item_id`) ON DELETE CASCADE;
			</pre></code>
		</div>
		<div class="tab-pane fade" id="nav-controller1" role="tabpanel" aria-labelledby="nav-controller-tab">
			<code><pre>
	[...]
	// Просто інший набір мов, ніж SITELANGS (масив мов сесії/інтерфейсу, в якому у даному випадку відсутня "ru")
	define('DBDATALANGS', ['uk', 'ru', 'en']);

	// Ускладнений варіант — друга мова з GET (інша, ніж мова сесії)
	$dbdatalang = filter_input(INPUT_GET, 'dbdatalang');
	if (!in_array($dbdatalang, DBDATALANGS)) {
		// Fallback: Простий варіант — мова сесії
		$lang = $_SESSION['language'];
	} else {
		$lang = $dbdatalang;
	}

	// 
	$items = $this->db->toarray("SELECT 
			x.item_id
			, COALESCE(NULLIF(xt.itemname, ''), x.itemname) itemname
		FROM example_l10n_items x
		LEFT JOIN example_l10n_items_t xt ON xt.item_id = x.item_id AND xt.lang = '{$lang}'
		ORDER BY RAND()
		LIMIT 10
	", true);

	// передати у view: $data['items'] = $items;
	[...]
			</pre></code>
		</div>

		<div class="tab-pane fade" id="nav-itemlisttpl" role="tabpanel" aria-labelledby="nav-itemlisttpl-tab">
			<code><pre>
	[...]
	print_r($data['items'])
	[...]
			</pre></code>
		</div>

		<div class="tab-pane fade show active" id="nav-itemlist" role="tabpanel" aria-labelledby="nav-itemlist-tab">
			<code><pre>
	<?php print_r($data['items']) ?>
			</pre></code>
		</div>

		<div class="tab-pane fade" id="nav-dbdatalang" role="tabpanel" aria-labelledby="nav-dbdatalang-tab">
			<?php foreach (DBDATALANGS as $dblang): ?>
			<div>
				<p>
					<a href="?dbdatalang=<?=$dblang?>" data-oa data-oa-scroll><?=$dblang?></a>
				</p>
			</div>
			<?php endforeach ?>
		</div>
	</div>
</div>

<!-- ///////////////////////////////////////////////////////////////////////////////////////// -->
<hr>
<div class="container-fluid" >
	<h1>Показ резюме відмінною від мови інтерфейсу мовою</h1>
	<nav>
		<div class="nav nav-tabs" id="nav-tab" role="tablist">
			<a class="nav-item nav-link" id="nav-cvback-tab" data-toggle="tab" href="#nav-cvback" role="tab" aria-controls="nav-cvback" aria-selected="false">
				[Контролер]
			</a>
			<a class="nav-item nav-link" id="nav-cvview2-tab" data-toggle="tab" href="#nav-cvview2" role="tab" aria-controls="nav-cvview2" aria-selected="false">
				[View]
			</a>
			<a class="nav-item nav-link active" id="nav-cvfront-tab" data-toggle="tab" href="#nav-cvfront" role="tab" aria-controls="nav-cvfront" aria-selected="false">
				[Результат]
			</a>
		</div>
	</nav>

	<div class="tab-content" id="nav-tabContent1">
		<div class="tab-pane fade" id="nav-cvback" role="tabpanel" aria-labelledby="nav-cvback-tab">
			<code><pre>
class [...] extends Controller {
	public function [...]() {
		[...]
		// $lang — мова резюме
		$this->setLanguage($lang);
		// $cvtext = renderView([Вигляд резюме], [Дані резюме]);
		$cvtext = _('Text from PO-file, used in CV') . ': ' . $items[0]['itemname'];
		$this->setLanguage($_SESSION['language']);
		[...]
	}
}
			</pre></code>
		</div>

		<div class="tab-pane fade" id="nav-cvview2" role="tabpanel" aria-labelledby="nav-cvview2-tab">
			<code><pre>
<?=html_escape("<!-- Сторінка до резюме -->\n")?>
<?=html_escape("<p><?=_('Text from PO-file number 1')?></p>\n")?>
<?=html_escape("<!-- Резюме -->\n")?>
<?=html_escape("<p><?=html_escape(\$data['cvtext'])?></p>\n")?>
<?=html_escape("<!-- Сторінка після резюме -->\n")?>
<?=html_escape("<p><?=_('Text from PO-file number 2')?></p>\n")?>
			</pre></code>
		</div>

		<div class="tab-pane fade show active" id="nav-cvfront" role="tabpanel" aria-labelledby="nav-cvfront-tab">
			<p><i>
				[Мова інтерфейсу]: <?=$_SESSION['language']?>;
				[Мова резюме]: <?=$data['lang']?> (вибрати іншу — вище на закладці "[Вибрати іншу мову, ніж мова інтерфейсу]")
			</i></p>
			<!-- Сторінка до резюме -->
			<p><?=_('Text from PO-file number 1')?></p>
			<!-- Резюме -->
			<p><?=html_escape($data['cvtext'])?></p>
			<!-- Сторінка після резюме -->
			<p><?=_('Text from PO-file number 2')?></p>
		</div>
	</div>
</div>
