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

class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.modify_posting_auth'			=> [['redirect', -PHP_INT_MAX]],
			'core.submit_post_modify_sql_data'	=> 'save_form_values',
		);
	}

	protected $helper;
	protected $db;
	protected $forms_table;
	public function __construct(\phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db, $forms_table)
	{
		$this->helper = $helper;
		$this->db = $db;
		$this->forms_table = $forms_table;
	}

	public function redirect($event)
	{
		if ($event['is_authed'] && ($event['mode'] === 'post' || ($event['mode'] == 'edit' && $event['post_data']['topic_first_post_id'] == $event['post_id'])))
		{
			$sql = 'SELECT form_id, replace_posting, form_permalink
				FROM ' . $this->forms_table . '
				WHERE forum_id = ' . (int) $event['forum_id'];
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row && $row['replace_posting'])
			{
				if (class_exists('\senky\forms\extended'))
				{
					$extended = new \senky\forms\extended;
				}

				redirect($this->helper->route('senky_forms_controller', [
					'id' 		=> !empty($extended) ? $extended->get_permalink($row) : $row['form_id'],
					'post_id'	=> $event['post_id'],
				]));
			}
		}
	}

	public function save_form_values($event)
	{
		$sql_data = $event['sql_data'];
		$sql_data[POSTS_TABLE]['sql']['senky_form_values'] = $event['data']['senky_form_values'] ?: '';
		$event['sql_data'] = $sql_data;
	}
}
