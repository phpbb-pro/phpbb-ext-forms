<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Jakub Senko
 *
 */

namespace senky\forms;

class permalink
{
    protected $db;
    protected $language;
    protected $forms_table;
    public function __construct()
    {
      global $phpbb_container;

      $this->db = $phpbb_container->get('dbal.conn');
      $this->language = $phpbb_container->get('language');
      $this->forms_table = $phpbb_container->getParameter('tables.senky_forms');
    }

    public function check_errors($row, $form_id)
    {
      $errors = [];

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
          WHERE form_permalink = '" . $this->db->sql_escape($row['form_permalink']) ."'";
      $result = $this->db->sql_query($sql);
      $permalink_row = $this->db->sql_fetchrow($result);
      $this->db->sql_freeresult($result);
      if ($permalink_row !== false && $permalink_row['form_id'] != $form_id)
      {
        $errors[] = $this->language->lang('FORM_PERMALINK_ALREADY_USED', $permalink_row['form_name']);
      }

      return $errors;
    }

    public function get_acp_html($row)
    {
      return '<dl>
        <dt><label for="form_permalink">' . $this->language->lang('FORM_PERMALINK') . $this->language->lang('COLON') . '</label><br><span>' . $this->language->lang('FORM_PERMALINK_EXPLAIN') . '</span></dt>
        <dd>' . generate_board_url() . '/form/' . '<input type="text" id="form_permalink" name="form_permalink" size="20" maxlength="255" value="' . htmlspecialchars($row['form_permalink']) . '" /></dd>
      </dl>';
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
}
