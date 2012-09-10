<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Component List Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_media
 * @since       1.5
 */
class MediaModelList extends JModelLegacy
{
	public function getState($property = null, $default = null)
	{
		static $set;

		if (!$set)
		{
			$input  = JFactory::getApplication()->input;
			$folder = $input->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	public function getImages()
	{
		$list = $this->getList();

		return $list['images'];
	}

	public function getFolders()
	{
		$list = $this->getList();

		return $list['folders'];
	}

	public function getDocuments()
	{
		$list = $this->getList();

		return $list['docs'];
	}

	/**
	 * Build imagelist
	 *
	 * @since 1.5
	 */
	public function getList()
	{
		static $list;

		// Only process the list once per request
		if (is_array($list))
		{
			return $list;
		}

		jimport('joomla.filesystem.file');

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined')
		{
			$current = '';
		}

		if (strlen($current) > 0)
		{
			$basePath = COM_MEDIA_BASE . '/' . $current;
		}
		else
		{
			$basePath = COM_MEDIA_BASE;
		}

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE.'/');

		$images  = array();
		$folders = array();
		$docs    = array();

		if (!is_dir($basePath))
		{
			return array('folders' => array(), 'docs' => array(), 'images' => array());
		}

		$iterator = new FilesystemIterator($basePath);
		foreach ($iterator as $path => $file)
		{
			$name = $file->getFilename();
			if ($file->isFile())
			{
				if (substr($name, 0, 1) != '.' && strtolower($name) !== 'index.html')
				{
					$tmp = new stdClass;
					$tmp->name = $name;
					$tmp->title = $name;
					$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($path));
					$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
					$tmp->size = $file->getSize();

					$ext = strtolower(JFile::getExt($name));
					switch ($ext)
					{
						// Image
						case 'jpg':
						case 'png':
						case 'gif':
						case 'xcf':
						case 'odg':
						case 'bmp':
						case 'jpeg':
						case 'ico':
							$info = @getimagesize($tmp->path);
							$tmp->width  = @$info[0];
							$tmp->height = @$info[1];
							$tmp->type   = @$info[2];
							$tmp->mime   = @$info['mime'];

							if (($info[0] > 60) || ($info[1] > 60))
							{
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 60);
								$tmp->width_60 = $dimensions[0];
								$tmp->height_60 = $dimensions[1];
							}
							else
							{
								$tmp->width_60 = $tmp->width;
								$tmp->height_60 = $tmp->height;
							}

							if (($info[0] > 16) || ($info[1] > 16))
							{
								$dimensions = MediaHelper::imageResize($info[0], $info[1], 16);
								$tmp->width_16 = $dimensions[0];
								$tmp->height_16 = $dimensions[1];
							}
							else
							{
								$tmp->width_16 = $tmp->width;
								$tmp->height_16 = $tmp->height;
							}

							$images[] = $tmp;
							break;

						// Non-image document
						default:
							$tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
							$tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
							$docs[] = $tmp;
							break;
					}
				}
			}
			elseif ($file->isDir())
			{
				$tmp = new stdClass;
				$tmp->name = $file->getBasename();
				$tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $name));
				$tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
				$count = MediaHelper::countFiles($tmp->path);
				$tmp->files = $count[0];
				$tmp->folders = $count[1];

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'docs' => $docs, 'images' => $images);

		return $list;
	}
}
