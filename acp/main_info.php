<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Jakub Senko
 *
 */

namespace senky\forms\acp;

class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\senky\forms\acp\main_module',
			'title'		=> 'ACP_FORMS_TITLE',
			'modes'		=> array(
				'manage'	=> array(
					'title'	=> 'ACP_FORMS_MANAGE',
					'auth'	=> 'ext_senky/forms && acl_a_board',
					'cat'	=> array('ACP_FORMS_TITLE')
				),
			),
		);
	}
}
