<?php 
/**
 * Мовна конфігурація сайту
 */

// Мови сайту
const SITELANGS = [
	// 'uk' => [
	// 	'name'		=>	'Українська',
	// 	'locale'	=>	'uk_UA', // putenv
	// 	'setlocale'	=>	'uk_UA.UTF8',
	// ],
	'ko' => [
		'name'		=>	'조선말/한국어',
		'locale'	=>	'ko_KR',
		'setlocale'	=>	'ko_KR.UTF8',
	],
	'en' => [
		'name'		=>	'English',
		'locale'	=>	'en_US',
		'setlocale'	=>	'en_US.UTF8',
	],
];

// Залежність "Мова браузера" — "Мова сайту"
// const LANG_REDIRECTION = [
// 	'uk'		=>	'uk',
// 	'uk-UA'		=>	'uk',
// 	'ru'		=>	'uk',
// 	'ru-RU'		=>	'uk',
// 	'ru-MO'		=>	'uk',
// 	'en'		=>	'en',
// 	'en-GB'		=>	'en',
// 	'en-US'		=>	'en',
// 	'default'	=>	'uk',
// ];

const LANG_REDIRECTION = [
	'uk'		=>	'ko',
	'uk-UA'		=>	'ko',
	'ru'		=>	'ko',
	'ru-RU'		=>	'ko',
	'ru-MO'		=>	'ko',
	'en'		=>	'en',
	'en-GB'		=>	'en',
	'en-US'		=>	'en',
	'default'	=>	'ko',
];
