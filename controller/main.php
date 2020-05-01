<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Jakub Senko
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
	protected $forms_table;
	protected $forums_table;
	protected $posts_table;
	protected $root_path;
	protected $php_ext;
	public function __construct(\phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\user $user, $forms_table, $forums_table, $posts_table, $root_path, $php_ext)
	{
		$this->language = $language;
		$this->helper = $helper;
		$this->template = $template;
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->forms_table = $forms_table;
		$this->forums_table = $forums_table;
		$this->posts_table = $posts_table;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	public function handle($id)
	{
		$post_id = $this->request->variable('post_id', 0);

		$this->language->add_lang('form', 'senky/forms');
		if (!$this->user->data['is_registered'] || $this->user->data['is_bot'])
		{
			return $this->helper->error('NO_FORM', 404);
		}

		$sql_ary = [
			'SELECT'	=> 'sf.*, f.forum_name',
			'FROM'		=> [$this->forms_table => 'sf'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [$this->forums_table => 'f'],
					'ON'	=> 'f.forum_id = sf.forum_id',
				]
			],
			'WHERE'	=> 'sf.form_id = ' . (int) $id,
		];

		if ($post_id)
		{
			$sql_ary['SELECT'] .= ', p.topic_id, p.senky_form_values';
			$sql_ary['FROM'] += [$this->posts_table => 'p'];
			$sql_ary['WHERE'] .= ' AND p.post_id = ' . (int) $post_id;
		}

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row === false)
		{
			return $this->helper->error('NO_FORM', 404);
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
				$form_inputs[$key]['value'] = $form_values[$input['name']];
			}
		}

		$preview = $this->request->is_set_post('preview');
		$submit = $this->request->is_set_post('submit');

		// collect data
		$errors = [];
		if ($preview || $submit)
		{
			foreach ($form_inputs as $key => $input)
			{
				// skip paragraphs
				if (empty($input['name']))
				{
					continue;
				}

				$val = $this->request->variable($input['name'], '', true);
				$form_inputs[$key]['value'] = $val;
				
				$valid = $this->is_valid_value($input, $val);
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
				$message_parser->message = $this->generate_template($row['form_template'], $form_inputs);
				$message_parser->parse(true, true, true, true, false, true, true);

				if ($preview)
				{
					$this->template->assign_vars([
						'PREVIEW_SUBJECT'	=> $this->generate_template($row['form_subject'], $form_inputs),
						'PREVIEW'			=> $message_parser->format_display(true, true, true, false),
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
						$form_values[$input['name']] = $input['value'];
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

					redirect($redirect_url);
				}
			}

			// fix selectbox selected property
			foreach ($form_inputs as $key => $input)
			{
				if ($input['type'] != 'select')
				{
					continue;
				}

				foreach ($input['values'] as $vkey => $value)
				{
					$form_inputs[$key]['values'][$vkey]['selected'] = $value['value'] == $input['value'];
				}
			}
		}

		$this->template->assign_vars([
			'U_ACTION'		=> $this->helper->route('senky_forms_controller', ['id' => $row['form_id']]),
			'FORUM_NAME'	=> $row['forum_name'],
			'FORM_NAME'		=> $row['form_name'],
			'FORM_INPUTS'	=> $form_inputs,
			'POST_ID'		=> $post_id,
			'ERRORS'		=> implode('<br>', $errors),
		]);
		
		return $this->helper->render('form.html', $row['form_name']);
	}

	protected function is_valid_value($input, $value)
	{
		if (!empty($input['required']) && empty($value))
		{
			return $this->language->lang('INPUT_REQUIRED', $input['label']);
		}

		if (!empty($input['maxlength']) && utf8_strlen($value) > $input['maxlength'])
		{
			return $this->language->lang('INPUT_TOO_LONG', $input['label'], $input['maxlength']);
		}

		if ($input['type'] == 'select' && !in_array($value, array_column($input['values'], 'value')))
		{
			return $this->language->lang('SELECT_VALUE_INVALID', $input['label']);
		}

		if (!empty($input['subtype']))
		{
			switch ($input['subtype'])
			{
				case 'email':
					if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL))
					{
						return $this->language->lang('EMAIL_INVALID', $input['label']);
					}
				break;

				case 'number':
					if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT))
					{
						return $this->language->lang('NUMBER_INVALID', $input['label']);
					}
				break;

				case 'url':
					if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL))
					{
						return $this->language->lang('URL_INVALID', $input['label']);
					}
				break;
			}
		}

		return true;
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

			$template = str_replace('{' . $input['name'] . '}', $input['value'], $template);
		}
		return $template;
	}

	protected function get_subject($inputs, $form_name)
	{
		foreach ($inputs as $input)
		{
			if (!empty($input['subject']))
			{
				return $input['value'];
			}
		}
		return $form_name;
	}
}
