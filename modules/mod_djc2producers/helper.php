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

class DJCatalog2ModProducer{
	var $_producers;
	
	public static function getProducers(){
		
		$db = JFactory::getDBO();
		$query = 'SELECT *, '
				.' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(":", id, alias) ELSE id END as prodslug '
				.' FROM #__djc2_producers ORDER BY name';
		$db->setQuery($query);
		$_producers = $db->loadAssocList();
		return $_producers;
	}
} ?>