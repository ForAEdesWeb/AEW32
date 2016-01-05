<?php
/**
 * @version $Id: djcatalog2.php 104 2013-01-23 07:37:58Z michal $
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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

class plgSearchDjcatalog2 extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	function onContentSearchAreas() {
		static $areas = array(
			'djcatalog2' => 'PLG_SEARCH_DJCATALOG2_DJCATALOGITEMS'
			);
			return $areas;
	}
	
	function onContentSearch( $text, $phrase='', $ordering='', $areas=null )
	{
		$app = JFactory::getApplication();
		$db		= JFactory::getDBO();
		$searchText = $text;
	
		require_once(JPATH_SITE.DS.'components'.DS.'com_djcatalog2'.DS.'helpers'.DS.'route.php');
	
		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
				return array();
			}
		}
	
		// load plugin params info
	 	$plugin = JPluginHelper::getPlugin('search', 'djcatalog2');
	 	$pluginParams = $this->params;
	
		$limit = $pluginParams->def( 'search_limit', 50 );
	
		$text = trim( $text );
		if ( $text == '' ) {
			return array();
		}
		
		$wheres	= array();
		$where = '';
		switch ($phrase)
		{
			case 'exact':
				$text		= $db->Quote('%'.$db->escape($text, true).'%', false);
				$wheres2	= array();
				$wheres2[]	= 'i.description LIKE '.$text;
				$wheres2[]	= 'i.intro_desc LIKE '.$text;
				$wheres2[]	= 'i.name LIKE '.$text;
				$wheres2[]	= 'i.metadesc LIKE '.$text;
				$wheres2[]	= 'i.metakey LIKE '.$text;
				$wheres2[]	= 'p.name LIKE '.$text;
				$where		= '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words	= explode(' ', $text);
				$wheres = array();
				foreach ($words as $word)
				{
					$word		= $db->Quote('%'.$db->escape($word, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'i.description LIKE '.$word;
					$wheres2[]	= 'i.intro_desc LIKE '.$word;
					$wheres2[]	= 'i.name LIKE '.$word;
					$wheres2[]	= 'i.metadesc LIKE '.$word;
					$wheres2[]	= 'i.metakey LIKE '.$word;
					$wheres2[]	= 'p.name LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres2);
				}
				$where	= '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}
	
		switch ( $ordering ) {
			case 'alpha':
				$order = 'i.name ASC';
				break;
	
			case 'category':
			case 'popular':
			case 'newest':
			case 'oldest':
			default:
				$order = 'i.name DESC';
		}
	
		$query = ' SELECT i.id AS id, i.name AS title, i.intro_desc AS intro, i.created as created, c.id AS ccategory_id, c.name AS category, i.description as text, i.metakey as metakey, i.metadesc as metadesc, p.name as producer_name, '
				. ' CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug, '
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
				. ' "2" AS browsernav '
				. ' FROM #__djc2_items AS i '
				. ' LEFT JOIN #__djc2_categories AS c ON c.id = i.cat_id '
				. ' LEFT JOIN #__djc2_producers AS p ON p.id = i.producer_id '
				. ' WHERE ('.$where.')'
				. ' AND i.published = 1 AND c.published = 1'
				. ' GROUP BY id'
				. ' ORDER BY '. $order
			;
		$db->setQuery( $query, 0, $limit );
		$rows = $db->loadObjectList();
		
		$count = count( $rows );
		for ( $i = 0; $i < $count; $i++ )
		{
			$rows[$i]->href 	= JRoute::_(DJCatalogHelperRoute::getItemRoute($rows[$i]->slug, $rows[$i]->catslug));
			$rows[$i]->section 	= JText::_('PLG_SEARCH_DJCATALOG2_DJCATALOGITEMS').': '.$rows[$i]->category;
		}
		$return = array();
		
		foreach($rows as $key => $section) {
			if(searchHelper::checkNoHTML($section, $searchText, array('title', 'text', 'intro', 'metadesc', 'metakey', 'producer_name'))) {
				$return[] = $section;
			}
		}
		return $return;
	}
}

