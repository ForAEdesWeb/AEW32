<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  AKHelper
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */

 
// no direct access
defined('_JEXEC') or die;


class AKHelperDb
{
	public static $nested = array();
	
	public static function getSelectList( $tables = array() , $all = true )
	{
		return AKHelper::_('query.getSelectList', $tables , $all);
	}
	
	public static function mergeFilterFields( $filter_fields , $tables = array() )
	{
		return AKHelper::_('query.mergeFilterFields', $filter_fields , $tables);
	}
	
	/*
	 * function nested
	 * @param $controller
	 */
	
	public static function nested($name, $option)
	{
		if(isset(self::$nested[$name])) {
			return self::$nested[$name] ;
		}
		$option = AKHelper::_('path.getOption') ;
		$option = str_replace('com_', '', $option) ;
		
		JTable::addIncludePath( AKHelper::_('path.getAdmin').'/components/'.$opntion.'/tables' ) ;
		$table = JTable::getInstance($name, ucfirst($option).'Table');
		if($table instanceof JTableNested) {
			return self::$nested[$name] = true ;
		}else {
			return self::$nested[$name] = false ;
		}
	}
}
