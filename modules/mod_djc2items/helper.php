<?php
/**
 * @version $Id: helper.php 104 2013-01-23 07:37:58Z michal $
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

class modDjc2ItemsHelper {
	var $_data = null;
	var $_cparams = null;
	var $_mparams = null;
	var $_categoryparams = array();
	
	function __construct( $params=array() )
	{
		$app = JFactory::getApplication();
		$this->_cparams = $app->getParams('com_djcatalog2');
		$this->_mparams = $params;
	}
	function getData() {
		if (!$this->_data){
			//$db					= JFactory::getDBO();
			//$db->setQuery($this->_buildQuery());
			//$this->_data = $db->loadObjectList();
			
			JModelLegacy::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_djcatalog2'.DS.'models');
			$model = JModelLegacy::getInstance('Items', 'Djcatalog2Model', array('ignore_request'=>true));
			
			$order		= $this->_mparams->get('orderby','i.ordering');
			$order_Dir	= $this->_mparams->get('orderdir','asc');
			$order_featured	= $this->_mparams->get('featured_first', 0);
			$filter_catid		= $this->_mparams->get('catid', array());
			
			$filter_featured	= $this->_mparams->get('featured_only', 0);
			$limit = $this->_mparams->get('items_limit',0);
			
			$state = $model->getState();
			
			$this->_cparams->set('product_catalogue', 0);
			$model->setState('params', $this->_cparams);
			
			$model->setState('list.start', 0);
			$model->setState('list.limit', $limit);
			
			$model->setState('filter.category',$filter_catid);
			$model->setState('filter.catalogue',false);
			$model->setState('filter.featured',$filter_featured);
			$model->setState('list.ordering_featured',$order_featured);
			$model->setState('list.ordering',$order);
			$model->setState('list.direction',$order_Dir);
			
			$this->_data = $model->getItems();
			
			foreach ($this->_data as $key => $item) {
				if ($this->_mparams->get('show_price') == 2 || ( $this->_mparams->get('show_price') == 1 && $item->price > 0.0)) {
					$catParams = $this->getCategoryParams($item->cat_id);
					$this->_data[$key]->price = DJCatalog2HtmlHelper::formatPrice($item->price, $catParams);
				}
				else {
					$this->_data[$key]->price = null;
				}
			}
		}
		return $this->_data;
	}
	function getCategoryParams($catid) {
		if (!isset($this->_categoryparams[$catid])) {
			$categories = Djc2Categories::getInstance(array('state'=>'1'));
			$category = $categories->get($catid);
			$this->_categoryparams[$catid] = $this->_cparams;
			if (!empty($category)) {
				$catpath = array_reverse($category->getPath());
				foreach($catpath as $k=>$v) {
					$parentCat = $categories->get((int)$v);
					if (!empty($parentCat) && !empty($category->params)) {
						$catparams = new JRegistry($parentCat->params); 
						$this->_categoryparams[$catid]->merge($catparams);
					}
				}
			}
		}		
		return $this->_categoryparams[$catid];
	}
	/*
	function _buildQuery()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT i.*, c.id AS ccategory_id, p.id AS pproducer_id, c.name AS category, p.name AS producer, p.published as publish_producer, img.fullname AS item_image, img.caption AS image_caption, '
			. ' CASE WHEN CHAR_LENGTH(i.alias) THEN CONCAT_WS(":", i.id, i.alias) ELSE i.id END as slug, '
			. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug, '
			. ' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(":", p.id, p.alias) ELSE p.id END as prodslug '
			. ' FROM #__djc2_items AS i '
			. ' LEFT JOIN #__djc2_categories AS c ON c.id = i.cat_id '
			. ' LEFT JOIN #__djc2_producers AS p ON p.id = i.producer_id '
			. ' LEFT JOIN (select im1.fullname, im1.caption, im1.type, im1.item_id from #__djc2_images as im1, (select item_id, type, min(ordering) as lowest_order from #__djc2_images group by item_id, type) as im2 where im1.item_id = im2.item_id and im1.type=im2.type and im1.ordering = im2.lowest_order) AS img ON img.item_id = i.id AND img.type=\'item\' '
			. $where
			. $orderby
		;
		//echo str_replace('#_', 'jos', $query);
		return $query;
	}

	function _buildContentOrderBy()
	{
		$filter_order		= $this->_mparams->get('orderby','i.ordering');
		$filter_order_Dir	= $this->_mparams->get('orderdir','asc');
		$filter_featured	= $this->_mparams->get('featured_first', 0);
		$limit = ($this->_mparams->get('items_limit',0));
		if ($filter_order != 'i.ordering' && $filter_order != 'category' && $filter_order != 'producer' && $filter_order != 'i.price' && $filter_order != 'i.name' && $filter_order != 'rand()') {
			$filter_order = 'i.ordering';
		}
		if ($filter_order_Dir != 'asc' && $filter_order_Dir != 'desc') {
			$filter_order_Dir = 'asc';
		}
		
		if ($filter_featured) {
			$orderby 	= ' ORDER BY i.featured DESC, '.$filter_order.' '.$filter_order_Dir.' , i.ordering, c.ordering ';
		}
		else if ($filter_order == 'i.ordering'){
			$orderby 	= ' ORDER BY i.ordering '.$filter_order_Dir.', c.ordering '.$filter_order_Dir;
		} 
		else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , i.ordering, c.ordering ';
		}
		if ($limit > 0) {
			$orderby .= ' LIMIT '.$limit;
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
		$db					= JFactory::getDBO();
		
		$filter_catid		= $this->_mparams->get('catid', array());
		$filter_featured	= $this->_mparams->get('featured_only', 0);
		$where = array();
		if ($filter_featured > 0) {
			$where[] = 'i.featured = 1';
		}
		if (count($filter_catid)) {
			$db->setQuery('SELECT item_id 
						   FROM #__djc2_items_categories AS ic
						   INNER JOIN #__djc2_categories AS c ON c.id=ic.category_id 
						   WHERE category_id IN ('.implode(',',$filter_catid).') AND c.published = 1');
			$items = $db->loadColumn();
			if (count ($items)) {
				$items = array_unique($items);
				$where[] = 'i.id IN ('.implode(',',$items).')';
			}
		}
		$where[] = 'i.published = 1';
		$where[] = 'c.published = 1';
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}*/
}

?>
