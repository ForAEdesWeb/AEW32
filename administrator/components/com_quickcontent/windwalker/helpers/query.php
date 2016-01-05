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


class AKHelperQuery
{
	static public $columns ;
	
	
	public static function getSelectList( $tables = array() , $all = true )
	{
		$db = JFactory::getDbo();
		
		$select = array() ;
		$fields = array() ;
		$i = 'a' ;
		
		foreach( $tables as $k => $table ){
			
			if(empty(self::$columns[$table])){
				self::$columns[$table] = $db->getTableColumns( $table );
			}
			
			$columns = self::$columns[$table] ;
			
			if($all){
				$select[] = "`{$k}`.*" ;
			}
			
			foreach( $columns as $key=>$var ){
				$fields[] = $db->qn("{$k}.{$key}", "{$k}_{$key}") ;
			}
			
			$i = ord($i);
			$i ++ ;
			$i = chr($i) ;
		}
		
		return $final = implode( "," , $select ).",\n".implode( ",\n" , $fields );
	}
	
	public static function mergeFilterFields( $filter_fields , $tables = array() , $option = array())
	{
		$db = JFactory::getDbo();
		$fields = array() ;
		$i = 'a' ;
		
		$ignore = array(
			'params'
		) ;
		
		if( !empty($option['ignore']) ) {
			$ignore = array_merge($ignore, $option['ignore']);
		}
		
		foreach( $tables as $k => $table ){
			
			if(empty(self::$columns[$table])){
				self::$columns[$table] = $db->getTableColumns( $table );
			}
			
			$columns = self::$columns[$table] ;
			
			foreach( $columns as $key=>$var ){
				if( in_array($key, $ignore) ){
					continue;
				}
				
				$fields[] = "{$k}.{$key}" ;
				//$fields[] = $key ;
			}
			
			$i = ord($i);
			$i ++ ;
			$i = chr($i) ;
		}
		
		return array_merge( $filter_fields , $fields );
	}
	
	
	public static function publishingPeriod ( $prefix = '' ) {
		$db = JFactory::getDbo();
		$nowDate 	= $date = JFactory::getDate( 'now' , JFactory::getConfig()->get('offset') )->toSQL() ;
        $nullDate	= $db->getNullDate();
        
        $date_where = " ( {$prefix}publish_up < '{$nowDate}' OR  {$prefix}publish_up = '{$nullDate}') AND ".
        			  "( {$prefix}publish_down > '{$nowDate}' OR  {$prefix}publish_down = '{$nullDate}') " ;
        			  
        return $date_where ;
	}
	
	public static function publishingItems ( $prefix = '', $published_col = 'published' ) {
		return self::publishingPeriod($prefix)." AND {$prefix}{$published_col} >= '1' ";
	}
	
}
