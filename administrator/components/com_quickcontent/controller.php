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

class QuickcontentController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Load the submenu.
		QuickcontentHelper::addSubmenu(JRequest::getCmd('view', 'lists'));

		$view = JRequest::getCmd('view', 'lists');
        JRequest::setVar('view', $view);

		parent::display();

		return $this;
	}
}
