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

namespace senky\forms\controls\type;

class captcha extends base
{
	protected $captcha_factory;
	protected $config;
	protected $template;
	protected $request;
	private $captcha;
	public function __construct(\phpbb\language\language $language, \phpbb\captcha\factory $captcha_factory, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\request\request $request)
	{
		parent::__construct($language);

		$this->captcha_factory = $captcha_factory;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
	}

	public function get_name()
	{
		return 'captcha';
	}

	public function init_render($form_inputs)
	{
		$this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
		$this->captcha->init(CONFIRM_POST);
	}

	public function is_valid_value($input, $value)
	{
		if ($this->request->is_set_post('preview'))
		{
			return true;
		}
		$response = $this->captcha->validate($captcha_data);
		return $response ? $response : true;
	}

	public function after_submit_post()
	{
		$this->captcha->reset();
	}

	public function get_hidden_fields()
	{
		$this->template->assign_var('CAPTCHA_TEMPLATE', $this->captcha->get_template());

		return $this->captcha->get_hidden_fields();
	}
}
