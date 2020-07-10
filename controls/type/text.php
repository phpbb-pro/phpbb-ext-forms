<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\controls\type;

class text extends base
{
	public function get_name()
	{
		return 'text';
	}

	public function is_valid_value($input, $value) {
		if (!empty($input['maxlength']) && utf8_strlen($value) > $input['maxlength'])
		{
			return $this->language->lang('INPUT_TOO_LONG', $input['label'], $input['maxlength']);
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
}
