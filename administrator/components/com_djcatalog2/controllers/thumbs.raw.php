<?php
/**
 * @version $Id: thumbs.raw.php 105 2013-01-23 14:05:57Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controlleradmin');


class Djcatalog2ControllerThumbs extends JControllerLegacy
{
public function go() {
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		if (!$user->authorise('core.admin', 'com_djcatalog2')){
			echo 'end';
			exit(0);
		}
		
		$id = JRequest::getVar('image_id',0,'default','int');
		
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams( 'com_djcatalog2' );
		
		$query = 'select count(*) from #__djc2_images';
		$db->setQuery($query);
		$total = $db->loadResult();
		$query = 'select count(*) from #__djc2_images where id > '.$id;
		$db->setQuery($query);
		$left = $db->loadResult();
		$query = 'select id, type, fullname from #__djc2_images where id > '.$id.' order by id asc limit 1';
		$db->setQuery($query);
		$image = $db->loadObject();
		if ($image) {
			$return = array();
			$return['id'] = $image->id;
			$return['type'] = $image->type;
			$return['name'] = $image->fullname;
			$return['total'] = $total;
			$return['left'] = $left;
			
			if (DJCatalog2ImageHelper::processImage(DJCATIMGFOLDER, $image->fullname, $image->type, $params)){
				$document->setMimeEncoding('application/json');
				echo json_encode($return);
			} else {
				echo 'error';
			}
			
		} else {
			echo 'end';	
		}
		exit(0);
	}
	public function purge() {
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_djcatalog2')){
			echo JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN');
			exit(0);
		}
		
		$files = JFolder::files(DJCATIMGFOLDER.DS.'custom', '.', false, false, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX')); 
		$errors = array();
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (!JFile::delete(DJCATIMGFOLDER.DS.'custom'.DS.$file)){
					$errors[] = $file;
				}
			}
		}
		if (count($errors) > 0) {
			echo JText::_('COM_DJCATALOG2_SOME_IMAGES_WERE_NOT_DELETED');
		} else {
			echo JText::_('COM_DJCATALOG2_ALL_IMAGES_HAVE_BEEN_DELETED');
		}
	}
}