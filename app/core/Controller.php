<?php

class Controller {
	protected $db;
	protected $member;

	public function __construct() {
		session_start();

		// Підключення до бази даних
		require_once '../app/classes/mysqli.class.php';
		require_once '../app/config/config-db.php';
		$this->db = $db;
		
		// Адреса сайту з протоколом
		define('SITE_URL', siteURL());

		// Мова користувача
		$this->setSessionLanguage();

		// Мовозалежні опції
		include '../app/config/config-options.php';
	}

	public function model($model, $id = false) {
		require_once '../app/models/' . $model . '.php';
		return new $model($this->db, $id);
	}

	public function view($view, $data = []) {
		require_once '../app/views/' . $view . '.php';
	}
	public function renderView($view, $data = []) {
		ob_start();
		require_once '../app/views/' . $view . '.php';
		return ob_get_clean(); 
	}

	protected function wrapView($mainview = 'home/index', $maindata = []) {
		// Alerts
		if (isset($_SESSION['msgbox']) and !empty($_SESSION['msgbox'])) {
			$GLOBALS['msgbox'] = array_merge($_SESSION['msgbox'], $GLOBALS['msgbox']);
			unset($_SESSION['msgbox']);
		}
		// Неочікуваний вивід
		$unplannedContent = ob_get_clean();
		// Відповідь сервера
		if (defined('AJAX') and AJAX) {
			$response = [
				'url'	=>	SITE_URL . $_SERVER['REQUEST_URI'],
				'title'	=>	$this->microformat['title'],
				'html'	=>	'<div class="animated fadeIn" style="animation-duration: 0.2s;">'.$unplannedContent.($mainview ? $this->renderView($mainview, $maindata): '').'</div>',
				'alerts'=>	$GLOBALS['msgbox']
			];
			header('Content-Type: application/json');
			echo json_encode($response);
		} else {
			$content = $mainview ? $this->renderView($mainview, $maindata) : '';

			$this->view('templates/index', [
				'content'	=>	$unplannedContent.$content,
			]);
		}
	}

	// ————————————————————————————————————————————————————————————————————————————————
	// Виставлення $_SESSION['language']
	protected function setSessionLanguage() {
		// Мова береться з URL
		$lang = filter_input(INPUT_GET, 'hl');
		// Якщо відсутня чи неправильна, береться з сесії
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
}
