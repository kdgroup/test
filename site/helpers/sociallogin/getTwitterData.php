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

require_once(JPATH_SITE . DS."components".DS ."com_enmasse".DS."helpers".DS."sociallogin".DS."api".DS."twitter".DS."twitteroauth.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "twconfig.php");

//load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= 1.6){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
     $language->load($extension, $base_dir, 'en-GB', true);
}

session_start();
if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
	// We've got everything we need
	$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	// Let's request the access token
	$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
	// Save it in a session var
	$_SESSION['access_token'] = $access_token;
	// Let's get the user's info
	$userInfo = $twitteroauth->get('account/verify_credentials');
	
	// Print user's info
	//echo '<pre>'; print_r($userInfo); echo '</pre><br/>';die();
	
	if (isset($userInfo->error)) {
		echo 'Twitter Login FAIL!!!'; die();
	} else {
		//redirect to upcomming deal page because there havent deal on today
		$link = JRoute::_('../../../../index.php?option=com_enmasse&controller=authentication&task=twitter&twitterId='.$userInfo->id.'&name='.$userInfo->name, false);
		JFactory::getApplication()->redirect($link, '');
	}
} else {
	//Login fail
	$link = JRoute::_('../../../../index.php', false);
	$msg = JText::_('SOCIAL_LOGIN_FAIL');
	JFactory::getApplication()->redirect($link, $msg);
}
?>