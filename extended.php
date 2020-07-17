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

namespace senky\forms;

class extended
{
    protected $db;
    protected $language;
    protected $template;
    protected $request;
    protected $user;
    protected $forms_table;
    public function __construct()
    {
      global $phpbb_container;

      $this->db = $phpbb_container->get('dbal.conn');
      $this->language = $phpbb_container->get('language');
      $this->template = $phpbb_container->get('template');
      $this->request = $phpbb_container->get('request');
      $this->user = $phpbb_container->get('user');
      $this->forms_table = $phpbb_container->getParameter('tables.senky_forms');
    }

    public function process_acp_add($row, $errors, $form_id)
    {
      $row['form_permalink'] = $this->request->variable('form_permalink', '', true);
      $row['allowed_groups'] = implode(',', $this->request->variable('allowed_groups', [0]));

      if (utf8_strlen($row['form_permalink']) > 255)
      {
        $errors[] = 'FORM_PERMALINK_TOO_LONG';
      }
      if (strlen($row['form_permalink']) > 0 && ctype_digit($row['form_permalink'][0]))
      {
        $errors[] = 'FORM_PERMALINK_STARTS_WITH_NUMBER';
      }
      $sql = 'SELECT form_id, form_name
          FROM ' . $this->forms_table . "
          WHERE form_permalink = '" . $this->db->sql_escape($row['form_permalink']) . "'";
      $result = $this->db->sql_query($sql);
      $permalink_row = $this->db->sql_fetchrow($result);
      $this->db->sql_freeresult($result);
      if ($permalink_row !== false && $permalink_row['form_id'] != $form_id)
      {
        $errors[] = $this->language->lang('FORM_PERMALINK_ALREADY_USED', $permalink_row['form_name']);
      }

      return [$row, $errors];
    }

    public function acp_before_sql($row)
    {
      return [
        'form_permalink'	=> $row['form_permalink'],
        'allowed_groups'	=> $row['allowed_groups'],
      ];
    }

    public function acp_is_group_selected($group_id, $row)
    {
      $selected_groups = explode(',', $row['allowed_groups']);
      return in_array($group_id, $selected_groups);
    }

    public function acp_before_display($row)
    {
      $this->template->assign_vars([
        'FORM_PERMALINK'    => $row['form_permalink'],
        'S_IS_EXTENDED'     => true,
      ]);
    }

    public function get_permalink($row)
    {
      return !empty($row['form_permalink']) ? $row['form_permalink'] : $row['form_id'];
    }

    public function find_by_permalink($keys, $id)
    {
      array_unshift($keys, "sf.form_permalink = '" . $this->db->sql_escape($id) . "'");
      return $keys;
    }

    public function check_groups($row)
    {
      $sql = 'SELECT COUNT(g.group_id) as matched_groups
          FROM ' . GROUPS_TABLE . ' g
          LEFT JOIN ' . USER_GROUP_TABLE . ' ug
              ON g.group_id = ug.group_id
          WHERE ug.user_id = ' . (int) $this->user->data['user_id'] . '
              AND ug.user_pending = 0
              AND g.group_id IN (' . $row['allowed_groups'] . ')';
      $result = $this->db->sql_query($sql);
      $matched_groups = $this->db->sql_fetchfield('matched_groups', $result);
      $this->db->sql_freeresult($result);

      return $matched_groups > 0;
    }
}
