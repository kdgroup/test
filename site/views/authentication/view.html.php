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
 
class EnmasseViewAuthentication extends JView
{
    function display($tpl = null)
    {
		$returnURL = JRequest::getVar('return', 'Lw');
		$this->assignRef( 'returnURL', $returnURL );
		
		$twitterId = JRequest::getVar('twitterId');
		$this->assignRef( 'twitterId', $twitterId );
		
		$name = JRequest::getVar('name');
		$this->assignRef( 'name', $name );
		
		parent::display($tpl);
    }

}
?>