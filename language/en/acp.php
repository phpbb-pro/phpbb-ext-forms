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
	'ACP_FORMS_MANAGE_TITLE'	=> 'Manage Forms',
	'FORM_NAME'					=> 'Form name',
	'FORM_FORUM'				=> 'Forum',
	'FORM_URL'					=> 'URL',
	'NO_FORMS'					=> 'There are no forms yet.',
	'EDIT_FORM'					=> 'Edit form',
	'CLONE_FORM'				=> 'Clone form',
	'ADD_FORM'					=> 'Add a form',

	'FORM_NOT_EXIST'		=> 'The selected form does not exist.',
	'FORM_SUBJECT'			=> 'Form subject',
	'FORM_SUBJECT_EXPLAIN'	=> 'New topic will have this subject. You can use placeholders just like in Form template. Keep in mind that the final maximum allowed length is 120 characters.',
	'REPLACE_POSTING'		=> 'Replace posting page',
	'FORM_INPUTS'			=> 'Form inputs',
	'FORM_TEMPLATE'			=> 'Form template',
	'FORM_TEMPLATE_EXPLAIN'	=> 'Use {INPUT_NAME} where INPUT_NAME is Name of the input to replace it with the value user submitted.',

	'FORM_NAME_EMPTY'		=> 'Form name cannot be empty.',
	'FORM_NAME_TOO_LONG'	=> 'Form name must not be longer than 255 characters.',
	'FORUM_REQUIRED'		=> 'You must select forum.',
	'FORUM_ALREADY_USED'	=> 'This forum is already used in different form.',
	'FORM_SUBJECT_EMPTY'	=> 'Form subject cannot be empty.',
	'FORM_SUBJECT_TOO_LONG'	=> 'Form subject must not be longer than 255 characters.',
	'FORM_INPUTS_EMPTY'		=> 'There must be at least one input in the form.',

	'CONFIRM_FORM_DELETE'	=> 'Are you sure you want to delete the form?',

	'FORM_SUBMITTED_EDIT'	=> 'Form updated',
	'FORM_SUBMITTED_ADD'	=> 'Form added',
	'FORM_SUBMITTED_DELETE'	=> 'Form deleted',
));
