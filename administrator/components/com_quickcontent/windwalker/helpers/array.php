<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  AKHelper
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */


// No direct access
defined('_JEXEC') or die;


class AKHelperArray {
	
	
	/*
	 * Pivot Array, separate by key.
	 * 
	 * From:
	 * 
	 * 		[value] => Array
	 * 			(
	 * 				[0] => aaa
	 * 				[1] => bbb
	 * 			)
	 * 	
	 * 		[text] => Array
	 * 			(
	 * 				[0] => aaa
	 * 				[1] => bbb
	 * 			)
	 * 	
	 *
	 *  To:
	 *
	 * 		[0] => Array
	 * 			(
	 * 				[value] => aaa
	 * 				[text] => aaa
	 * 			)
	 * 	
	 * 		[1] => Array
	 * 			(
	 * 				[value] => bbb
	 * 				[text] => bbb
	 * 			)	
	 * @param $array
	 */
	
	public static function pivotByKey($array)
	{
		$array 	= (array) $array ;
		$new	= array();
		$keys 	= array_keys($array) ;
		
		foreach( $keys as $k => $val ):
			
			foreach( (array) $array[$val] as $k2 => $v2 ):
				
				$new[$k2][$val] = $v2 ;
				
			endforeach;
			 
		endforeach;
		
		return $new ;
	}
	
	
	
	/*
	 * function combineByKey
	 *
	 * 
	 * From:
	 * 	 	[0] => Array
	 * 			(
	 * 				[value] => aaa
	 * 				[text] => aaa
	 * 			)
	 * 	
	 * 		[1] => Array
	 * 			(
	 * 				[value] => bbb
	 * 				[text] => bbb
	 * 			)
	 * 
	 * To:
	 *
	 * 		[value] => Array
	 * 			(
	 * 				[0] => aaa
	 * 				[1] => bbb
	 * 			)
	 * 	
	 * 		[text] => Array
	 * 			(
	 * 				[0] => aaa
	 * 				[1] => bbb
	 * 			)
	 * 
	 * @param $array
	 */
	
	public static function pivotBySort($array)
	{
		$array 	= (array) $array ;
		$new	= array();
		
		$array2 = $array ;
		$first	= array_shift($array2) ;

		foreach( $array as $k => $v ):
			
			foreach( (array) $first as $k2 => $v2 ):
				
				$new[$k2][$k] = $array[$k][$k2] ;
				
			endforeach;
			
		endforeach;
		
		return $new ;
	}
	
	
	
	/*
	 * function pivotParams
	 * @param $params
	 */
	
	public static function pivotFromPrefix( $prefix ,$origin, $target = null)
	{
		
		foreach( $origin as $key => $row ):
			if( strpos( $key, $prefix ) === 0 && isset($row)){
				$key2 = substr($key, JString::strlen($prefix)) ;
				self::setValue($target, $key2, $row) ;
			}
		endforeach;
		
		return $target;
	}
	
	
	/*
	 * function pivotToPrefix
	 * @param $array
	 */
	
	public static function pivotToPrefix( $prefix ,$origin, $target = null)
	{
		foreach( $origin as $key => $val ):
		
			$key = $prefix.$key ;
			
			if(!self::getValue($target, $key)){
				self::setValue($target, $key, $val) ;
			}
			
		endforeach;
		
		return $target;
	}
	
	
	/*
	 * function query
	 * @param $query
	 */
	
	public static function query($array, $queries = array(), $keepKey = false)
	{
		$results = array();
		
		// Visit Array
		foreach( $array as $k => $v ):
			
			$data = (array) $v ;
			
			// Visit Query Rules
			foreach( $queries as $key => $val ):
				
				// Key: is query key
				// Val: is query value
				// Data: is array element
				$value = null ;
				
				if( substr($val, 0, 2) == '>=' ) {
					
					if( JArrayHelper::getValue( $data, $key ) >= substr($val, 2) ){
						$value = $v ;
					}
					
				}elseif( substr($val, 0, 2) == '<=' ) {
					
					if( JArrayHelper::getValue( $data, $key ) <= substr($val, 2) ){
						$value = $v ;
					}
					
				}elseif( substr($val, 0, 1) == '>' ) {
					
					if( JArrayHelper::getValue( $data, $key ) > substr($val, 1) ){
						$value = $v ;
					}
					
				}elseif( substr($val, 0, 1) == '<' ) {
					
					if( JArrayHelper::getValue( $data, $key ) < substr($val, 1) ){
						$value = $v ;
					}
					
				}else{
					
					if( JArrayHelper::getValue( $data, $key ) == $val ){
						$value = $v ;
					}
					
				}
				
				
				 // Set Query results
				if($value) {
					if($keepKey) {
						$results[$k] = $value ;
					}else{
						$results[] = $value ;
					}
				}
				
			endforeach;
			
		endforeach;
		
		return $results ;
	}
	
	
	
	/*
	 * function setValue
	 * @param $key
	 */
	
	public static function setValue(&$array, $key, $value)
	{
		if( is_array($array) ) {
			return $array[$key] = $value ;
		}else{
			return $array->$key = $value;
		}
	}
	
	
	
	/*
	 * function setValue
	 * @param $key
	 */
	
	public static function getValue(&$array, $key, $default = null)
	{
		if( is_array($array) ) {
			return JArrayHelper::getValue( $array, $key, $default );
		}
		
		// if not Array, we do not detect it for warning not Object
		$result = null ;
		if( isset($array->$key) ) {
			$result = $array->$key ;
		}
		
		if(is_null($result)){
			$result = $default ;
		}
		
		return $result ;
	}
}


