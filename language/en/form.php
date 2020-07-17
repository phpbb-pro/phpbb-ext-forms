<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 * @license Proprietary/All rights reserved
 * @licensee {{LICENSEE}}
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'NO_FORM'				=> 'There is no such form.',
	'INPUT_REQUIRED'		=> '%s is required.',
	'INPUT_TOO_LONG'		=> '%s is too long. Maximum length is %d',
	'SELECT_VALUE_INVALID'	=> 'You did not select a valid value for %s',
	'EMAIL_INVALID'			=> '%s must be an email',
	'NUMBER_INVALID'		=> '%s must be a number',
	'URL_INVALID'			=> '%s must be an URL',
));
