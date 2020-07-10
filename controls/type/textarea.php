<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\controls\type;

class textarea extends base
{
	public function get_name()
	{
		return 'textarea';
	}

	public function is_valid_value($input, $value) {
		if (!empty($input['maxlength']) && utf8_strlen($value) > $input['maxlength'])
		{
			return $this->language->lang('INPUT_TOO_LONG', $input['label'], $input['maxlength']);
		}

		return true;
	}
}
