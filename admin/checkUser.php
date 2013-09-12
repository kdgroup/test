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

// No direct access 
defined( '_JEXEC' ) or die( 'Restricted access' ); 

//define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__).'/../../../');
define( 'DS', DIRECTORY_SEPARATOR );
//
require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
$mainframe = JFactory::getApplication('site');
//
//
$username =  JRequest::getVar( 'username', null, 'post');

	if($username != '')
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__users WHERE username = '$username';";
		$db->setQuery( $query );
		$user = $db->loadObject();
		if(!empty($user))
		 	echo 'valid';
		else
			echo 'invalid';
		  
	}
//		
	
?>