<?php
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");

class EnmasseViewTerm extends JViewLegacy
{
	function display($tpl = null)
	{
		$id =  JRequest::getVar('id');
		$this->item = JModelLegacy::getInstance('Content','enmasseModel')->getById($id);
		$this->_setPath('template', JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
		$this->_layout="term";
		parent::display($tpl);
	}
}
?>