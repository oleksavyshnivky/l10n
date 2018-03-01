<?php 
/**
 * Мовна конфігурація сайту
 */

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
