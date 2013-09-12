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

// Get Joomla! framework
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../') );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

require_once( JPATH_SITE . DS . "components" . DS . "com_enmasse" . DS. "helpers" . DS . "sociallogin" . DS . "SocialLoginHelper.php");

if (!empty($_GET['openid_ext1_value_firstname']) && !empty($_GET['openid_ext1_value_lastname']) && !empty($_GET['openid_ext1_value_email'])) {
	$user_profile = array();
	$user_profile['name']	= $_GET['openid_ext1_value_firstname'] . ' ' . $_GET['openid_ext1_value_lastname'];
	$user_profile['email']	= $_GET['openid_ext1_value_email'];
	
   //insert a new user
	SocialLoginHelper::checkUser($user_profile, $_SESSION['returnURL']);
}
?>
