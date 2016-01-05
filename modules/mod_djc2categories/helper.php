<?php
/**
 * @version $Id: helper.php 106 2013-01-24 08:54:34Z michal $
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

defined ('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE.DS.'components'.DS.'com_djcatalog2'.DS.'helpers'.DS.'route.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djcatalog2'.DS.'lib'.DS.'categories.php');

class DJC2CategoriesModuleHelper {
  public static function getHtml($beginId, $cid, $expand) {
		$categories = Djc2Categories::getInstance(array('state'=>'1'));
		$root = $categories->get($beginId);
		$current = $categories->get($cid);

		$path = array();
		if (!empty($current)) {
			foreach ($current->getPath() as $item) {
				$path[] = (int)$item;
			}
		}

		$html = '<ul class="menu nav">';
		self::makeList($html, $root, $path, $expand, $cid);
		$html .= '</ul>';
		return $html;
	}
	private static function makeList(&$html, &$root, $path, $expand, $cid, $level = 0) {
		$children = $root->getChildren();
		foreach($children as $child) {
			$current = (($child->id == $cid)) ? true:false;
			$parent = (count($child->getChildren())) ? true:false;
			$active = (($current || in_array($child->id, $path))) ? true:false;
			$deeper = ($parent && $expand) ? true:false;

			$class = 'level'.$level;
			$class .= ( $current ) ? ' current':'';
			$class .= ( $active ) ? ' active':'';
			$class .= ( $parent ) ? ' parent':'';
			$class .= ( $deeper ) ? ' deeper':'';

			$html.= '<li class="'.$class.'"><a href="'.JRoute::_(DJCatalogHelperRoute::getCategoryRoute($child->catslug), true).'">'.$child->name.'</a>';
			if (($active || $expamoduleclass_sfxnd) && count($child->getChildren())) {
				$html .= '<ul>';
				$level++;
				self::makeList($html, $child, $path, $expand, $cid, $level);
				$level--;
				$html .= '</ul>';
			}
			$html .= '</li>';
		}
	}
}
?>