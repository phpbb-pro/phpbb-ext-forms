<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\controls\type;

interface type_interface
{
	public function get_name();
	public function init_render($form_inputs);
	public function is_valid_value($input, $value);
	public function after_submit_post();
	public function get_hidden_fields();
	public function get_default_value($input);
	public function set_value($input, $value);
	public function get_value($input);
	public function get_value_for_template($input);
}
