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
jimport( 'joomla.application.application' );

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
class EnmasseViewRss extends JView
{
	function display($tpl=null)
	{
		
		$task = JRequest::getWord('task');
		$mainframe = JFactory::getApplication('site');
		
		if($task == 'today')
		{
		   $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."views". DS ."rss". DS ."today". DS);
		//	$mainframe->redirect('http://'.$_SERVER['SERVER_NAME'].'/components/com_enmasse/views/rss/today');
			///$this->_layout="today";
			     
		}
		else if ($task == 'listdeal')
		{
			   $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."views". DS ."rss". DS ."listdeal". DS);
            
			}
		else if($task == 'location')
		{
		 $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."views". DS ."rss". DS ."location". DS);
        
		}    
		else if($task == 'deal')
		{
		 $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."views". DS ."rss". DS ."deal". DS);
        
		}
                else if($task == 'listexpireddeal')
		{
                    $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."views". DS ."rss". DS ."listexpireddeal". DS);
                }
		parent::display($tpl);
	}
	
	
	

}