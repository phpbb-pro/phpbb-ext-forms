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

namespace senky\forms\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_manage_forums_display_form'	=> 'display_form_information',
		);
	}

	protected $db;
	protected $forms_table;
	protected $admin_path;
	protected $php_ext;
	public function __construct(\phpbb\db\driver\driver_interface $db, $forms_table, $php_ext)
	{
		global $phpbb_admin_path;

		$this->db = $db;
		$this->forms_table = $forms_table;
		$this->admin_path = $phpbb_admin_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Displays information in forum edit form when form is used to
	 * build new topics.
	 */
	public function display_form_information($event)
	{
		if ($event['action'] == 'edit')
		{
			$sql = 'SELECT form_id, form_name
				FROM ' . $this->forms_table . '
				WHERE forum_id = ' . (int) $event['forum_id'];
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				return;
			}

			$event['template_data'] += [
				'DISPLAY_FORM_NOTICE'	=> true,
				'U_FORM'				=> append_sid($this->admin_path . 'index.' . $this->php_ext, 'i=-senky-forms-acp-main_module&amp;mode=manage&amp;action=edit&amp;id=' . $row['form_id']),
				'FORM_NAME'				=> $row['form_name'],
			];
		}
	}
}
