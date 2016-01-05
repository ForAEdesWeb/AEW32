<?php
/**
 * ------------------------------------------------------------------------
 * JA Login module for J25 & J31
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the syndicate functions only once
require_once (dirname(__FILE__) . '/helper.php');

JHTML::_('behavior.tooltip');
include_once(dirname(__FILE__).'/assets/asset.php');

$params->def('greeting', 1);
$type = modJALoginHelper::getType();
$return = modJALoginHelper::getReturnURL($params, $type);

$user = JFactory::getUser();

$captchatext = "";
if(JRequest::getCmd('option') !== "com_users" || JRequest::getCmd('view') !== "registration"){
	JForm::addFormPath(JPATH_SITE."/components/com_users/models/forms");
	$form = JForm::getInstance('com_users.registration',"registration", array('control' => 'jform'), false, false);
	$captcha = $form->getField("captcha");
	$captchatext = $captcha->input;

}

//DISPLAYING
require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));