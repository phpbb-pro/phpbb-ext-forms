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

namespace senky\forms\controller;

class main
{
	protected $language;
	protected $helper;
	protected $template;
	protected $db;
	protected $request;
	protected $user;
	protected $controls;
	protected $config;
	protected $auth;
	protected $forms_table;
	protected $forums_table;
	protected $posts_table;
	protected $root_path;
	protected $php_ext;
	private $extended;
	public function __construct(\phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\user $user, \senky\forms\controls\manager $controls, \phpbb\config\config $config, \phpbb\auth\auth $auth, $forms_table, $forums_table, $posts_table, $root_path, $php_ext)
	{
		$this->language = $language;
		$this->helper = $helper;
		$this->template = $template;
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->controls = $controls;
		$this->config = $config;
		$this->auth = $auth;
		$this->forms_table = $forms_table;
		$this->forums_table = $forums_table;
		$this->posts_table = $posts_table;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		if (class_exists('\senky\forms\extended'))
		{
			$this->extended = new \senky\forms\extended;
		} 
	}

	public function handle($id)
	{
		$post_id = $this->request->variable('post_id', 0);

		$this->language->add_lang('mcp');
		$this->language->add_lang('posting');
		$this->language->add_lang('form', 'senky/forms');

		add_form_key('senky/forms/form');

		$keys = ["sf.form_id = " . (int) $id];
		if (!empty($this->extended))
		{
			$keys = $this->extended->find_by_permalink($keys, $id);
		}
		foreach($keys as $condition)
		{
			$sql_ary = [
				'SELECT'	=> 'sf.*, f.forum_name',
				'FROM'		=> [$this->forms_table => 'sf'],
				'LEFT_JOIN'	=> [
					[
						'FROM'	=> [$this->forums_table => 'f'],
						'ON'	=> 'f.forum_id = sf.forum_id',
					]
				],
				'WHERE'	=> $condition,
			];
	
			if ($post_id)
			{
				$sql_ary['SELECT'] .= ', p.topic_id, p.senky_form_values';
				$sql_ary['FROM'] += [$this->posts_table => 'p'];
				$sql_ary['WHERE'] .= ' AND p.post_id = ' . (int) $post_id;
			}
	
			$result = $this->db->sql_query_limit($this->db->sql_build_query('SELECT', $sql_ary), 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row !== false)
			{
				break;
			}
		}

		if ($row === false)
		{
			return $this->helper->error('NO_FORM', 404);
		}

		if (!empty($this->extended))
		{
			// only allow form to selected groups
			if (!$this->extended->check_groups($row))
			{
				return $this->helper->error('NO_FORM', 404);
			}
		}
		else
		{
			// allow to every registered user
			if (!$this->user->data['is_registered'] || $this->user->data['is_bot'])
			{
				return $this->helper->error('NO_FORM', 404);
			}
		}
		

		// check for permissions only if posting page is replaced
		if ($row['replace_posting'])
		{
			if (!$this->auth->acl_get('f_read', $row['forum_id']))
			{
				return $this->helper->error('SORRY_AUTH_READ', 401);
			}
	
			if (!$post_id)
			{
				if (!$this->auth->acl_get('f_post', $row['forum_id']))
				{
					return $this->helper->error('USER_CANNOT_POST', 401);
				}
			}
			else
			{
				if (!$this->auth->acl_gets('f_edit', 'm_edit', $row['forum_id']))
				{
					return $this->helper->error('USER_CANNOT_EDIT', 401);
				}
			}
		}

		// decode form inputs
		$form_inputs = json_decode(str_replace('&quot;', '"', $row['form_inputs']), true);
		if (empty($form_inputs))
		{
			$form_inputs = [];
		}

		// decode form values when editing
		if ($post_id)
		{
			$form_values = json_decode(str_replace('&quot;', '"', $row['senky_form_values']), true);
			if (empty($form_values))
			{
				$form_values = [];
			}

			foreach ($form_inputs as $key => $input)
			{
				$form_inputs[$key] = $this->controls->set_value($input, $form_values[$input['name']]);
			}
		}

		$preview = $this->request->is_set_post('preview');
		$submit = $this->request->is_set_post('submit');

		$this->controls->init_render($form_inputs);

		// collect data
		$errors = [];
		if ($preview || $submit)
		{
			// make sure the form key is valid
			if (!check_form_key('senky/forms/form'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			foreach ($form_inputs as $key => $input)
			{
				// skip paragraphs
				if (empty($input['name']))
				{
					continue;
				}

				$val = $this->request->variable($input['name'], $this->controls->get_default_value($input), true);
				$form_inputs[$key] = $this->controls->set_value($input, $val);
				
				$valid = $this->controls->is_valid_value($input, $val);
				if ($valid !== true)
				{
					$errors[] = $valid;
				}
			}

			if (empty($errors))
			{
				if (!class_exists('parse_message'))
				{
					include($this->root_path . 'includes/message_parser.' . $this->php_ext);
				}
				$message_parser = new \parse_message();
				// only parse BBcodes from admin
				$message_parser->message = $row['form_template'];
				$message_parser->parse($row['allow_bbcode'], $row['allow_urls'], $row['allow_smilies'], true, false, true, true);
				// then replace with user input, but do not parse it again, it will be displayed as plain text
				$message_parser->message = $this->generate_template($message_parser->message, $form_inputs);

				if ($preview)
				{
					$this->template->assign_vars([
						'PREVIEW_SUBJECT'	=> $this->generate_template($row['form_subject'], $form_inputs),
						'PREVIEW'			=> $message_parser->format_display($row['allow_bbcode'], $row['allow_urls'], $row['allow_smilies'], false),
					]);
				}
				else
				{
					if (!function_exists('submit_post'))
					{
						include($this->root_path . 'includes/functions_posting.' . $this->php_ext);
					}

					// calculate form values
					$form_values = [];
					foreach ($form_inputs as $input)
					{
						$form_values[$input['name']] = $this->controls->get_value($input);
					}

					$poll_data = [];
					$data = [
						'topic_title'		=> $this->generate_template($row['form_subject'], $form_inputs),
						'forum_id'			=> $row['forum_id'],
						'icon_id'			=> 0,
						'poster_id'			=> $this->user->data['user_id'],
						'enable_sig'		=> true,
						'enable_bbcode'		=> true,
						'enable_smilies'	=> true,
						'enable_urls'		=> true,
						'enable_indexing'	=> true,
						'message_md5'		=> md5($message_parser->message),
						'notify'			=> false,
						'notify_set'		=> false,
						'poster_ip'			=> $this->user->ip,
						'post_edit_locked'	=> false,
						'bbcode_bitfield'	=> $message_parser->bbcode_bitfield,
						'bbcode_uid'		=> $message_parser->bbcode_uid,
						'message'			=> $message_parser->message,

						// edit-required fields
						'post_id'					=> $post_id,
						'topic_posts_approved'		=> 0,
						'topic_posts_unapproved'	=> 0,
						'topic_posts_softdeleted'	=> 0,
						'topic_first_post_id'		=> $post_id,
						'post_edit_reason'			=> '',
						'topic_id'					=> $post_id ? $row['topic_id'] : 0,

						// form values for easy edit
						'senky_form_values'	=> json_encode($form_values),

						// fix for Topic Description ext
						'topic_desc'	=> '',
					];
					$redirect_url = \submit_post($post_id ? 'edit' : 'post', $data['topic_title'], $this->user->data['username'], POST_NORMAL, $poll_data, $data, true);

					$this->controls->after_submit_post($form_inputs);

					redirect($redirect_url);
				}
			}
		}

		$this->template->assign_vars([
			'U_ACTION'				=> $this->helper->route('senky_forms_controller', ['id' => $row['form_id']]),
			'FORUM_NAME'			=> $row['forum_name'],
			'FORM_NAME'				=> $row['form_name'],
			'FORM_INPUTS'			=> $form_inputs,
			'POST_ID'					=> $post_id,
			'ERRORS'					=> implode('<br>', $errors),
			'S_HIDDEN_FIELDS'	=> build_hidden_fields($this->controls->build_hidden_fields($form_inputs)),
		]);
		
		return $this->helper->render('form.html', $row['form_name']);
	}

	protected function generate_template($template, $inputs)
	{
		foreach ($inputs as $input)
		{
			// skip paragraphs
			if (empty($input['name']))
			{
				continue;
			}

			$template = str_replace('{' . $input['name'] . '}', $this->controls->get_value_for_template($input), $template);
		}
		return $template;
	}
}
