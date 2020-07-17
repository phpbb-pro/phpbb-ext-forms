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
	'REPLACE_POSTING_EXPLAIN'	=> 'If set to Yes, users will be forced to use this form to post in the forum. Forum permissions will be honored. If set to No, the form will be only available at the form link - posting page will remain the same. Forum permissions will be ignored - all members can submit the form.',
	'FORM_PERMALINK'		=> 'Form link',
	'FORM_PERMALINK_EXPLAIN'	=> 'You can type in the nice URL for this form to give it more credibility. The link cannot start with a number. Leave empty to use auto-generated ID.',
	'ALLOWED_GROUPS'	=> 'Who can submit the form',
	'ALLOWED_GROUPS_EXPLAIN'	=> 'Select which groups can see and submit the form. This extends the forum permissions if <em>Replace posting page</em> is set to Yes (meaning user must have adequate permissions plus be member of at least one of the selected groups). Make sure to select at least one, otherwise no one will be able to submit it. Yes, even guests can be allowed (do not forget to add CAPTCHA input below in that case).',
	'FORM_INPUTS'			=> 'Form inputs',
	'FORM_TEMPLATE'			=> 'Form template',
	'FORM_TEMPLATE_EXPLAIN'	=> 'Use {INPUT_NAME} where INPUT_NAME is Name of the input to replace it with the value user submitted.',

	'FORM_NAME_EMPTY'		=> 'Form name cannot be empty.',
	'FORM_NAME_TOO_LONG'	=> 'Form name must not be longer than 255 characters.',
	'FORUM_REQUIRED'		=> 'You must select forum.',
	'FORUM_ALREADY_USED'	=> 'This forum is already used in different form.',
	'FORM_SUBJECT_EMPTY'	=> 'Form subject cannot be empty.',
	'FORM_SUBJECT_TOO_LONG'	=> 'Form subject must not be longer than 255 characters.',
	'FORM_PERMALINK_TOO_LONG'	=> 'Form permalink must not be longer than 255 characters.',
	'FORM_PERMALINK_STARTS_WITH_NUMBER'	=> 'Form permalink must not start with a number.',
	'FORM_PERMALINK_ALREADY_USED'	=> 'Form permalink is already used in `%s` form',
	'FORM_INPUTS_EMPTY'		=> 'There must be at least one input in the form.',

	'CONFIRM_FORM_DELETE'	=> 'Are you sure you want to delete the form?',

	'FORM_SUBMITTED_EDIT'	=> 'Form updated',
	'FORM_SUBMITTED_ADD'	=> 'Form added',
	'FORM_SUBMITTED_DELETE'	=> 'Form deleted',
));
