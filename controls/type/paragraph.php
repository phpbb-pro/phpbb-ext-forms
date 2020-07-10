<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\controls\type;

class paragraph extends base
{
	public function get_name()
	{
		return 'paragraph';
	}

	public function is_valid_value($input, $value) {
		return true;
	}
}
