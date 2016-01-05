<?php
	/*
	 * $JA#COPYRIGHT$
	 */
  // no direct access
defined('_JEXEC') or die('Restricted access');
if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
jimport('joomla.form.formfield');

class JFormFieldlofcolorpicker extends JFormField {
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$type = 'Lofcolorpicker';

	function getInput(){
		
		$uri = $this->getCurrentURL();
		$this->loadjscss( $uri ); 	
		$value = $this->value?$this->value:(string)$this->element['default'];
		$string =  '<input class="color" value="'.$value.'" name="'.$this->name.'" >';
		return $string;	
	}
	
	/**
	 * get current url
	 */
	function getCurrentURL(){

		$uri_base = JURI::base ();
		$uri = str_replace("/administrator", "", $uri_base);
		$uri = $uri.'modules/mod_slicebox_slider/elements';
		return $uri;
	}
	
	/**
	 * load css and js file
	 */
	function loadjscss( $uri ){

		if (!defined ('_JA_PARAM_HELPER_RAINBOW_')) {
			define ('_JA_PARAM_HELPER_RAINBOW_', 1);
			JHTML::script($uri."/".'lofcolorpicker/jscolor.js');
		}
	
	} 
}
?>