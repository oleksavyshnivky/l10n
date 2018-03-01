<?php

class Home extends Controller {
	public function index() {

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

		// Резюме мовою $dbdatalang (чи просто іншою, ніж мова сесії)
		$lang = $dbdatalang ? $dbdatalang : $_SESSION['language'];
		$this->setLanguage($lang);
		$cvtext = _('Text from PO-file, used in CV') . ': ' . $items[0]['itemname'];
		$this->setLanguage($_SESSION['language']);

		// ————————————————————————————————————————————————————————————————————————————————

		$this->wrapView('home/index', [
			'dbdatalang'	=>	$dbdatalang,
			'lang'		=>	$lang,
			'items'		=>	$items,
			'cvtext'	=>	$cvtext,
		]);
	}
}
