<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\migrations;

class m4_form_permalink extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'senky_forms', 'form_permalink');
	}

	static public function depends_on()
	{
		return ['\senky\forms\migrations\m2_forms_table'];
	}

	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'senky_forms'	=> [
					'form_permalink'	=> ['VCHAR', ''],
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'senky_forms'	=> [
					'form_permalink'
				],
			],
		];
	}
}
