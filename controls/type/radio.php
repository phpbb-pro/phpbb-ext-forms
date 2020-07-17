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

class radio extends base
{
	public function get_name()
	{
		return 'radio-group';
	}

	public function is_valid_value($input, $value) {
		if (!in_array($value, array_column($input['values'], 'value')))
		{
			return $this->language->lang('RADIO_VALUE_INVALID', $input['label']);
		}

		return true;
	}

	public function set_value($input, $value)
	{
		foreach ($input['values'] as $vkey => $option)
		{
			$input['values'][$vkey]['selected'] = is_array($value)
				? in_array($option['value'], $value)
				: $option['value'] == $value;
		}

		return $input;
	}

	public function get_value($input)
	{
		$selected_value =  array_filter($input['values'], function($value) {
			return !empty($value['selected']);
		});

		return array_shift($selected_value)['value'];
	}

	public function get_value_for_template($input)
	{
		return $this->get_value($input);
	}
}
