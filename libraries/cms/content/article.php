<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class JContentArticle extends JContent
{
	/**
	 * Method to load a content object.
	 *
	 * @param   integer  $contentId  The content id.
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function load($contentId)
	{
		$object = parent::load($contentId);

		$object->articletext = trim($object->fulltext) != '' ? $object->body . "<hr id=\"system-readmore\" />" . $object->fulltext : $object->body;

		return $object;
	}

	/**
	 * Method to bind the object properties.
	 *
	 * @param   mixed  $properties  The object properties.
	 *
	 * @return  JDatatbaseObject  The database object.
	 *
	 * @since   3.0
	 * @throws  InvalidArgumentException
	 */
	 public function bind($properties)
	 {
		// Convert properties to an array.
		$properties = (array) $properties;

		// Search for the {readmore} tag and split the text up accordingly.
		if (isset($properties['articletext']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $properties['articletext']);

			if ($tagPos == 0)
			{
				$properties['body'] = $properties['articletext'];
				$properties['fulltext'] = '';
			}
			else
			{
				list ($properties['body'], $properties['fulltext']) = preg_split($pattern, $properties['articletext'], 2);
			}
		}
		return parent::bind($properties);
	 }
}
