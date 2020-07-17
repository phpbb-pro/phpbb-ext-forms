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
					'auth'	=> 'ext_senky/forms && acl_a_forms',
					'cat'	=> array('ACP_FORMS_TITLE')
				),
			),
		);
	}
}
