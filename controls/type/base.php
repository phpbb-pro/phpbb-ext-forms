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

abstract class base implements type_interface
{
  protected $language;
	public function __construct(\phpbb\language\language $language)
	{
		$this->language = $language;
  }

  public function init_render($form_inputs) {}

  public function is_valid_value($input, $value) {
		return true;
	}

	public function after_submit_post() {}

	public function get_hidden_fields()
	{
		return [];
	}

	public function get_default_value($input)
	{
		return '';
	}

	public function set_value($input, $value)
	{
		$input['value'] = $value;
		return $input;
	}

	public function get_value($input)
	{
		return $input['value'];
	}

	public function get_value_for_template($input)
	{
		return $input['value'];
	}
}
