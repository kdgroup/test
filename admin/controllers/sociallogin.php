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
jimport('joomla.application.component.controller');

class EnmasseControllerSocialLogin extends JController
{
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'sociallogin');
		parent::display();
	}
	
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
	
	function save()
	{
		$data = JRequest::get( 'post' );
		
		$fbconfigFile = JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "fbconfig.php";
		$twconfigFile = JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "twconfig.php";
	
		$fbconfig	=	"<?php\ndefine('FB_APP_ID', '"			. $data['fb_app_id']			. "');";
		$fbconfig	.=	"\ndefine('FB_SECRET', '"				. $data['fb_secret']			. "'); ?>";
		$twconfig	=	"<?php\ndefine('TW_CONSUMER_KEY', '"	. $data['tw_consumer_key']		. "');";
		$twconfig	.=	"\ndefine('TW_CONSUMER_SECRET', '"		. $data['tw_consumer_secret']	. "'); ?>";
		
		$fb_saveResult = EnmasseControllerSocialLogin::writeToFile($fbconfigFile, $fbconfig);
		$tw_saveResult = EnmasseControllerSocialLogin::writeToFile($twconfigFile, $twconfig);
		
		if($fb_saveResult && $tw_saveResult)
		{
			$msg = JText::_('SAVE_SUCCESS_MSG');
			JFactory::getApplication()->redirect('index.php?option=com_enmasse', $msg);
		}
		else
		{
			$msg = JText::_('SAVE_ERROR_MSG');
			JFactory::getApplication()->redirect('index.php?option=com_enmasse', $msg , 'error');
		}
		
		//echo $fbconfigFile; echo '<br/>';
		//echo $twconfigFile; echo '<br/>';
		//echo $fbconfig; echo '<br/>';
		//echo $twconfig; echo '<br/>';
		//echo '<pre>';print_r($data);echo '</pre>';die();
	}
	
	function writeToFile($file, $string)
	{
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		// Get the new FTP credentials.
		$ftp = JClientHelper::getCredentials('ftp', true);

		// Attempt to make the file writeable if using FTP.
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0644'))
		{
			JError::raiseNotice('SOME_ERROR_CODE', JText::sprintf("SOCIAL_LOGIN_ERROR_CONFIGURATION_PHP_NOTWRITABLE", $file));
		}
		
		if (!JFile::write($file, $string))
		{
			$this->setError(JText::sprintf("SOCIAL_LOGIN_ERROR_WRITE_FAILED", $file));
			return false;
		}

		// Attempt to make the file unwriteable if using FTP.
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0444'))
		{
			JError::raiseNotice('SOME_ERROR_CODE', JText::sprintf("SOCIAL_LOGIN_ERROR_CONFIGURATION_PHP_NOTUNWRITABLE", $file));
		}

		return true;
	}
}
?>