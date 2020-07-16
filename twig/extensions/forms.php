<?php
/**
 *
 * Forms. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, Jakub Senko
 *
 */

namespace senky\forms\twig\extensions;

class forms extends \Twig_Extension
{
	public function getName()
	{
		return 'senky.forms';
	}

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('unescape', array($this, 'unescape')),
		);
	}

	public function unescape($value)
	{
		return html_entity_decode($value);
	}
}
