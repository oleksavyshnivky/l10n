<?php

/**
 * Функції
 */

// Адреса сайту з протоколом
function siteURL() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
	$domainName = $_SERVER['HTTP_HOST'];
	return $protocol.$domainName;
}

// Safe file names
function getSafeFileName($file) {
	// Remove anything which isn't a word, whitespace, number
	// or any of the following caracters -_~,;:[]().
	$file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $file);
	// Remove any runs of periods (thanks falstro!)
	$file = preg_replace("([\.]{2,})", '', $file);

	return $file;
}

class microTimer {
	function start() {
		global $starttime;
		$mtime = microtime();
		$mtime = explode( ' ', $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
	}
	function stop() {
		global $starttime;
		$mtime = microtime();
		$mtime = explode( ' ', $mtime );
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round( ($endtime - $starttime), 5 );
		return $totaltime;
	}
}

// Виставлення коржиків
function set_cookie($name, $value, $expires) {
	$expires = $expires ? time() + ($expires * 86400) : false;
	setcookie($name, $value, $expires, '/', $_SERVER['HTTP_HOST'], NULL, TRUE);
}

// Інформаційне повідомлення
function msgbox($title, $message, $style = 'danger') {
	// На вивід піде глобальна змінна GLOBALS['msgbox']
	$GLOBALS['msgbox'][] = ['options'=>['title'=>$title, 'message'=>$message], 'settings'=>['type'=>$style]];
}

function redirect($sublink, $siteURL = true) {
	$link = ($siteURL ? SITE_URL : '') . $sublink;
	if (!isset($_SESSION['msgbox'])) $_SESSION['msgbox'] = [];
	$_SESSION['msgbox'] = array_merge((array)$_SESSION['msgbox'], (array)$GLOBALS['msgbox']); // Збереження інформаційних повідомлень у сесії
	header("location: {$link}");
	exit;
}

function redirect2returnurl($sublink = '', $siteURL = true) {
	$link = ($siteURL ? SITE_URL : '') . $sublink;
	if (!$link) $link = SITE_URL;
	if (!isset($_SESSION['msgbox'])) $_SESSION['msgbox'] = [];
	$_SESSION['msgbox'] = array_merge((array)$_SESSION['msgbox'], (array)$GLOBALS['msgbox']); // Збереження інформаційних повідомлень у сесії
	header("location: {$link}");
	exit;
}

// Перенаправлення на сторінку входу
function redirect2login() {
	global $returnurl;
	msgbox(_('Note'), _('You must be logged in to see this page.'), 'warning');
	redirect('/member/signin/?returnurl=' . $returnurl);
}

function redirectOnError($errorMsg = '', $redirectURL = '') {
	if (!$errorMsg) $errorMsg = _('Something went wrong.');
	
	if (defined('AJAX')) {
		die($errorMsg);
	} else {
		msgbox(_('Error'), $errorMsg);
		redirect($redirectURL);
	}
}

// Очистка телефонного номеру
function clearPhoneNumber($phone) {
	return preg_replace('/[^\d+]/', '', $phone);
}

function own_method($class_name, $method_name) {
	if (method_exists($class_name, $method_name)) {
		$reflection = new ReflectionMethod($class_name, $method_name);
		if (!$reflection->isPublic()) return false;

		$parent_class = get_parent_class($class_name);
		if ($parent_class !== false) return !method_exists($parent_class, $method_name);

		return true;
	}
	else return false;
}

function arrayExclude($array, Array $excludeKeys){
	$return = [];
	foreach ($array as $key => $value){
		if(!in_array($key, $excludeKeys)){
			$return[$key] = $value;
		}
	}
	return $return;
}
