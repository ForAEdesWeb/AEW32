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


class AKHelperSystem
{
	static $config 		= array();
	
	static $version  	= array();
	
	static $profiler 	= array() ;
	
		/*
	 * function getParams
	 * @param $option
	 */
	
	public static function getParams($option = null)
	{
		if(!$option) {
			$option = AKHelper::_('path.getOption') ;
		}
		
		if($option) {
			return JComponentHelper::getParams($option);
		}
	}
	
	
	/*
	 * function getConfig
	 * @param $key
	 */
	
	public static function getConfig($key, $default = null, $option = null)
	{
		if(!$option){
			$option = AKHelper::_('path.getOption') ;
		}
		
		if(isset(self::$config[$option])) {
			return self::$config[$option]->get($key, $default) ;
		}
		
		// Init Config
		self::$config[$option] = new JRegistry();
		self::$config[$option]->loadFile( AKHelper::_('path.getAdmin', $option).'/includes/config.json' );
		
		return self::$config[$option]->get($key, $default) ;
	}
	
	
	/*
	 * function getVersion
	 * @param 
	 */
	
	public static function getVersion($option = null)
	{
		if(!$option){
			$option = AKHelper::_('path.getOption') ;
		}
		
		if(isset(self::$version[$option])) {
			return self::$version[$option] ;
		}
		
		$xml = AKHelper::_('path.getAdmin').'/'.substr(AKHelper::_('path.getOption'), 4).'.xml' ;
		$xml = JFactory::getXML($xml, true) ;
		
		return self::$version[$option] = $xml->version ;
	}
	
	
	
	/*
	 * function profiler
	 * @param $text
	 */
	
	public static function mark($text, $namespace = null)
	{
		
		if(!$namespace) {
			$namespace = 'Application' ;
		}
		
		if( !(JDEBUG && $namespace == 'Application') && !AKDEBUG) {
			return ;
		}
		
		if(isset(self::$profiler[$namespace])) {
			self::$profiler[$namespace]->mark($text) ;
		}
		
		// System profiler.
		jimport('joomla.error.profiler');
		self::$profiler[$namespace] = JProfiler::getInstance($namespace);
		
		self::$profiler[$namespace]->mark($text) ;
	}
	
	
	/*
	 * function getProfiler
	 * @param 
	 */
	
	public static function renderProfiler($namespace = null)
	{
		if(!$namespace) {
			$namespace = 'Application' ;
		}
		
		if(isset(self::$profiler[$namespace])) {
			$_PROFILER = self::$profiler[$namespace] ;
			
			$buffer = $_PROFILER->getBuffer();
			$buffer = implode("\n<br />\n", $buffer) ;
			
		}else{
			$buffer = 'No Profiler Data.' ;
		}
		
		$buffer = '<pre><h3>WindWalker Debug: </h3>'.$buffer.'</pre>' ;
		
		echo $buffer ;
	}
}