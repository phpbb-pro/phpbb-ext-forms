<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Jakub Senko
 *
 */

namespace senky\forms\acp;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	protected $language;
	protected $request;
	protected $template;
	protected $db;
	protected $helper;
	protected $forms_table;
	protected $forums_table;
	protected $root_path;
	protected $php_ext;
	public function __construct()
	{
		global $phpbb_container;

		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');
		$this->db = $phpbb_container->get('dbal.conn');
		$this->helper = $phpbb_container->get('controller.helper');
		$this->forms_table = $phpbb_container->getParameter('tables.senky_forms');
		$this->forums_table = $phpbb_container->getParameter('tables.forums');
		$this->root_path = $phpbb_container->getParameter('core.root_path');
		$this->php_ext = $phpbb_container->getParameter('core.php_ext');
	}

	public function main($id, $mode)
	{
		$this->language->add_lang('acp', 'senky/forms');
		$this->page_title = $this->language->lang('ACP_FORMS_MANAGE_TITLE');
		add_form_key('senky/forms');

		$action = $this->request->variable('action', '');

		switch ($action)
		{
			case 'edit':
			case 'clone':

				$form_id = $this->request->variable('id', 0);

				$sql = 'SELECT *
					FROM ' . $this->forms_table . '
					WHERE form_id = ' . (int) $form_id;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if ($row === false)
				{
					trigger_error($this->language->lang('FORM_NOT_EXIST') . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if ($this->request->is_set_post('delete'))
				{
					if (confirm_box(true))
					{
						$sql = 'DELETE FROM ' . $this->forms_table . '
							WHERE form_id = ' . (int) $form_id;
						$this->db->sql_query($sql);

						trigger_error($this->language->lang('FORM_SUBMITTED_DELETE') . adm_back_link($this->u_action));
					}
					else
					{
						confirm_box(false, $this->language->lang('CONFIRM_FORM_DELETE'), build_hidden_fields([
							'i'			=> $id,
							'mode'		=> $mode,
							'action'	=> 'edit',
							'delete'	=> true,
							'id'		=> $form_id,
						]));
					}
				}

				// reset form and forum ID
				if ($action == 'clone')
				{
					unset($row['form_id']);
					$row['forum_id'] = 0;
				}

			// no break
			case 'add':

				if (empty($row))
				{
					$row = [
						'form_name'			=> '',
						'forum_id'			=> 0,
						'form_subject'		=> '',
						'replace_posting'	=> 0,
						'form_inputs'		=> '',
						'form_template'		=> '',
					];
				}

				// disable selection of already used forums
				$sql = 'SELECT forum_id
					FROM ' . $this->forms_table;
				if (!empty($row))
				{
					$sql .= ' WHERE forum_id <> ' . $row['forum_id'];
				}
				$result = $this->db->sql_query($sql);
				$ignore_ids = array_column($this->db->sql_fetchrowset($result), 'forum_id');
				$this->db->sql_freeresult($result);

				// process submit
				$errors = [];
				$preview = $this->request->is_set_post('preview');
				$submit = $this->request->is_set_post('submit');
				if ($preview || $submit)
				{
					$row['form_name'] = $this->request->variable('form_name', '', true);
					if (empty($row['form_name']))
					{
						$errors[] = 'FORM_NAME_EMPTY';
					}
					if (utf8_strlen($row['form_name']) > 255)
					{
						$errors[] = 'FORM_NAME_TOO_LONG';
					}

					$row['forum_id'] = $this->request->variable('forum_id', 0);
					if (empty($row['forum_id']))
					{
						$errors[] = 'FORUM_REQUIRED';
					}
					if (in_array($row['forum_id'], $ignore_ids))
					{
						$errors[] = 'FORUM_ALREADY_USED';
					}

					$row['form_subject'] = $this->request->variable('form_subject', '', true);
					if (empty($row['form_subject']))
					{
						$errors[] = 'FORM_SUBJECT_EMPTY';
					}
					if (utf8_strlen($row['form_subject']) > 255)
					{
						$errors[] = 'FORM_SUBJECT_TOO_LONG';
					}

					$row['form_inputs'] = $this->request->variable('form_inputs', '', true);
					if (empty($row['form_inputs']) || $row['form_inputs'] === '[]')
					{
						$errors[] = 'FORM_INPUTS_EMPTY';
					}

					$row['replace_posting'] = $this->request->variable('replace_posting', 0);
					$row['form_template'] = $this->request->variable('form_template', '', true);

					if (empty($errors))
					{
						if ($submit)
						{
							$sql_ary = [
								'form_name'			=> $row['form_name'],
								'forum_id'			=> (int) $row['forum_id'],
								'replace_posting'	=> (int) $row['replace_posting'],
								'form_inputs'		=> $row['form_inputs'],
								'form_template'		=> $row['form_template'],
								'form_subject'		=> $row['form_subject'],
							];
	
							$sql = 'INSERT INTO ' . $this->forms_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
							if ($action == 'edit')
							{
								$sql = 'UPDATE ' . $this->forms_table . '
									SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
									WHERE form_id = ' . (int) $row['form_id'];
							}
							$this->db->sql_query($sql);
	
							trigger_error($this->language->lang('FORM_SUBMITTED_' . strtoupper($action)) . adm_back_link($this->u_action));
						}
						else // preview
						{
							if (!class_exists('parse_message'))
							{
								include($this->root_path . 'includes/message_parser.' . $this->php_ext);
							}
							$message_parser = new \parse_message();
							$message_parser->message = $row['form_template'];
							$message_parser->parse(true, true, true, true, false, true, true);
							$this->template->assign_vars([
								'PREVIEW_SUBJECT'	=> $row['form_subject'],
								'PREVIEW'			=> $message_parser->format_display(true, true, true, false)
							]);
						}
					}

					$this->template->assign_var('ERRORS', implode('<br>', array_map(function($error) {
						return $this->language->lang($error);
					}, $errors)));
				}

				$inputs = str_replace('&quot;', '"', $row['form_inputs']);
				$this->template->assign_vars([
					'U_ACTION'			=> $this->u_action,
					'S_FORUMS'			=> make_forum_select(!empty($row) ? $row['forum_id'] : false, $ignore_ids, true, true, true),
					'FORM_ID'			=> !empty($row['form_id']) ? $row['form_id'] : false,
					'FORUM_ID'			=> $row['forum_id'],
					'FORM_NAME'			=> $row['form_name'],
					'FORM_SUBJECT'		=> $row['form_subject'],
					'FORM_INPUTS'		=> $inputs ?: '[]',
					'FORM_TEMPLATE'		=> $row['form_template'],
					'REPLACE_POSTING'	=> $row['replace_posting'],
				]);

				$this->tpl_name = 'forms_add_edit';

			break;

			default:

				$sql = 'SELECT sf.form_id, f.forum_name, sf.form_name
					FROM ' . $this->forms_table . ' sf
					LEFT JOIN ' . $this->forums_table . ' f
						ON (f.forum_id = sf.forum_id)';
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->template->assign_block_vars('forms', [
						'NAME'		=> $row['form_name'],
						'FORUM'		=> $row['forum_name'],
						'URL'		=> $this->helper->route('senky_forms_controller', ['id' => $row['form_id']], false, '', UrlGeneratorInterface::ABSOLUTE_URL),
						'U_EDIT'	=> $this->u_action . '&action=edit&id=' . $row['form_id'],
						'U_CLONE'	=> $this->u_action . '&action=clone&id=' . $row['form_id'],
					]);
				}
				$this->db->sql_freeresult($result);

				$this->template->assign_var('U_ADD_FORM', $this->u_action . '&action=add');

				$this->tpl_name = 'forms_list';

			break;
		}
	}
}
