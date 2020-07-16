<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\migrations;

class m2_forms_table extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'senky_forms');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'senky_forms'	=> array(
					'COLUMNS'		=> array(
						'form_id'			=> array('UINT', null, 'auto_increment'),
						'forum_id'			=> array('UINT', 0),
						'form_name'			=> array('VCHAR', ''),
						'form_inputs'		=> array('TEXT', ''),
						'form_template'		=> array('TEXT', ''),
						'replace_posting'	=> array('BOOL', 0),
						'form_subject'		=> array('VCHAR', ''),
					),
					'PRIMARY_KEY'	=> 'form_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'senky_forms',
			),
		);
	}
}
