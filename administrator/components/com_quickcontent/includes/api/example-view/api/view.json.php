<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_quickcontent
 *
 * @copyright   Copyright (C) 2012 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Generated by AKHelper - http://asikart.com
 */

// no direct access
defined('_JEXEC') or die;


jimport('joomla.application.component.view');

if(!class_exists('AKViewApi')){
	quickcontentLoader('admin://includes/api/api.init');
}

/**
 * View to edit
 */
class QuickcontentViewApi extends AKViewApi
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		parent::display($tpl);
	}


}
