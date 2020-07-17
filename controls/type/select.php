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

class select extends base
{
	public function get_name()
	{
		return 'select';
	}

	public function is_valid_value($input, $value) {
		if (
			(!empty($input['multiple']) && !empty(array_diff($value, array_column($input['values'], 'value'))))
			||
			(empty($input['multiple']) && !in_array($value, array_column($input['values'], 'value')))
		)
		{
			return $this->language->lang('SELECT_VALUE_INVALID', $input['label']);
		}

		return true;
	}

	public function get_default_value($input)
	{
		return !empty($input['multiple']) ? [''] : '';
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
		$selected_options = array_filter($input['values'], function($value) {
			return !empty($value['selected']);
		});

		$selected_values = array_map(function($value) {
			return $value['value'];
		}, $selected_options);

		return $selected_values;
	}

	public function get_value_for_template($input)
	{
		return implode(', ', $this->get_value($input));
	}
}
