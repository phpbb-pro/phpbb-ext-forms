<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Jakub Senko
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
	'ACP_FORMS_TITLE'	=> 'Forms',
	'ACP_FORMS_MANAGE'	=> 'Manage',
	'FORUM_FORM_INFO'	=> 'This forum uses <a href="%s">%s</a> form.',
));
