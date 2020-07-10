<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\controls;

class manager
{
	protected $language;
	protected $form_controls;
	public function __construct(\phpbb\language\language $language, $form_controls)
	{
		$this->language = $language;

		$this->register_form_controls($form_controls);
	}

	public function get_types()
	{
		return array_keys($this->form_controls);
	}

	public function init_render($form_inputs)
	{
		foreach ($this->form_controls as $type => $form_control)
		{
			if (in_array($type, array_column($form_inputs, 'type')))
			{
				$form_control->init_render($form_inputs);
			}
		}
	}

	public function is_valid_value($input, $value)
	{
		if (!empty($input['required']) && empty($value))
		{
			return $this->language->lang('INPUT_REQUIRED', $input['label']);
		}

		return $this->form_controls[$input['type']]->is_valid_value($input, $value);
	}

	public function after_submit_post($form_inputs)
	{
		foreach ($this->form_controls as $type => $form_control)
		{
			if (in_array($type, array_column($form_inputs, 'type')))
			{
				$form_control->after_submit_post();
			}
		}
	}

	public function build_hidden_fields($form_inputs)
	{
		$hidden_fields = [];
		foreach ($this->form_controls as $type => $form_control)
		{
			if (in_array($type, array_column($form_inputs, 'type')))
			{
				$hidden_fields = array_merge($hidden_fields, $form_control->get_hidden_fields());
			}
		}
		return $hidden_fields;
	}

	public function get_default_value($input)
	{
		return $this->form_controls[$input['type']]->get_default_value($input);
	}

	public function set_value($input, $value)
	{
		return $this->form_controls[$input['type']]->set_value($input, $value);
	}

	public function get_value($input)
	{
		return $this->form_controls[$input['type']]->get_value($input);
	}

	public function get_value_for_template($input)
	{
		return $this->form_controls[$input['type']]->get_value_for_template($input);
	}

	protected function register_form_controls($form_controls)
	{
		if (!empty($form_controls))
		{
			foreach ($form_controls as $control)
			{
				$this->form_controls[$control->get_name()] = $control;
			}
		}
	}
}
