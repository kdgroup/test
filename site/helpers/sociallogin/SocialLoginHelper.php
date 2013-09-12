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

class SocialLoginHelper {

	/*
		Check the existing user
		If user doesn't existing, insert a new user, assign him/her to the default group in the config file and set current user to logged in
		If user exists, set current user to logged in
	*/
	function checkUser($user, $returnURL = '')
	{
		require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
		require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "config.php");

		//get date time of now
		$dateTimeOfNow = DatetimeWrapper::getDatetimeOfNow();
		
		//Check if user exists in the system
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__users WHERE email='" . $user['email'] . "'";
		$db->setQuery( $query );
		$dbuser = $db->loadObject();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		 //if user doesn't existing
		if(!$dbuser)
		{
			//insert a new user
			SocialLoginHelper::insertUser($user['name'], $user['email'], $user['email'], DEFAULT_PASSWORD_ENCODE, $dateTimeOfNow, $dateTimeOfNow);
		
			//Get User ID that has just inserted
			$user_id = SocialLoginHelper::getUserByEmail($user['email']);
			
			if($user_id){
				//assign a user to the default group
				SocialLoginHelper::assignUserToGroup($user_id, DEFAULT_USER_GROUP);
			}

			$login_username = $user['email'];
			$login_password = DEFAULT_PASSWORD_ENCODE;
		}
		else
		{
			$login_username = $dbuser->username;
			$login_password = $dbuser->password;
			
			//update the lastvisitdate			
			SocialLoginHelper::updateLastVisitDate($login_username, $dateTimeOfNow);
		}
		
		if($returnURL === '')
				return SocialLoginHelper::login($login_username, $login_password, JURI::base());
			else 
				return SocialLoginHelper::login($login_username, $login_password, $returnURL);
		
	}
	
	/*
		Check the existing user (use with Twitter OAuth)
		If user doesn't existing, insert a new user, assign him/her to the default group in the config file and set current user to logged in
		If user exists, set current user to logged in
	*/
	function checkUser_Twitter($user, $returnURL = '')
	{
		require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
		require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "config.php");

		//get date time of now
		$dateTimeOfNow = DatetimeWrapper::getDatetimeOfNow();
		$username = 'twitter.' . $user['twitterId'];
		
		//Check if user exists in the system
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__users WHERE email='" . $user['email'] . "'";
		$db->setQuery( $query );
		$dbuser = $db->loadObject();

		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		 //if user doesn't existing
		if(!$dbuser)
		{		
			//insert a new user
			SocialLoginHelper::insertUser($user['name'], $username, $user['email'], DEFAULT_PASSWORD_ENCODE, $dateTimeOfNow, $dateTimeOfNow);
		
			//Get User ID that has just inserted
			$user_id = SocialLoginHelper::getUserByEmail($user['email']);
			
			if($user_id){
				//assign a user to the default group
				SocialLoginHelper::assignUserToGroup($user_id, DEFAULT_USER_GROUP);
			}

			$login_username = $username;
			$login_password = DEFAULT_PASSWORD_ENCODE;
		}
		else
		{
			//exists email in our database			
			$msg = JText::_("SOCIAL_LOGIN_EMAIL_DUP");
			$link = JURI::base();
			JFactory::getApplication()->redirect($link, $msg);
		}
		
		if($returnURL === '')
				return SocialLoginHelper::login($login_username, $login_password, JURI::base());
			else 
				return SocialLoginHelper::login($login_username, $login_password, $returnURL);
		
	}
	
	/*
		Insert a new user to database
	*/
	function insertUser($name, $username, $email, $password, $registerDate, $lastvisitDate, $activation = 0, $block = 0, $sendEmail = 0)
	{
		$db = JFactory::getDBO();
		$query = "INSERT INTO #__users (`name`, `username`, `email`, `password`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`) ";
		$query .= "VALUES('$name', '$username', '$email', '$password', $block, $sendEmail, '$registerDate', '$lastvisitDate', $activation)";
		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
	
	/*
		Assign a user with userId to one group
	*/
	function assignUserToGroup($userid, $groupid)
	{
		$db = JFactory::getDBO();
		$query = "INSERT INTO #__user_usergroup_map (`user_id`, `group_id`) VALUES($userid, $groupid)";
		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		return true;		
	}
	
	/*
		Update the last visit date
	*/
	function updateLastVisitDate($email, $lastvisitDate)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__users SET `lastvisitDate` = '$lastvisitDate'  WHERE `email` ='$email'";
		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}
	
	/*
		Update the username and last visit date
	*/
	function updateLastVisitDateAndUsername($email, $lastvisitDate, $newUsername)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__users SET `lastvisitDate` = '$lastvisitDate' AND `username` = '$newUsername' WHERE `email` ='$email'";
		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}
	
	/*
		Get user_id by email
	*/
	function getUserByEmail($email)
	{
		$db = JFactory::getDBO();
		//Get User ID that has just inserted
		$query = "SELECT id FROM #__users WHERE email='$email'";
		$db->setQuery($query);
		$user_id = $db->loadResult();
		
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return 0;
		}		
		return $user_id;
	}
	
	/*
		Set a user to logged in with encrypted password
	*/
	function login($username, $encrypted_password, $urlredirect = null)
	{
		// Get the application object.
		$app = JFactory::getApplication();

		$db =& JFactory::getDBO();
		$query = 'SELECT `id`, `username`, `password`'
				. ' FROM `#__users`'
				. ' WHERE username=' . $db->Quote( $username )
				. '   AND password=' . $db->Quote( $encrypted_password )
		;
		$db->setQuery( $query );
		$result = $db->loadObject();

		if($result) {
			JPluginHelper::importPlugin('user');

			$options = array();
			$options['action'] = 'core.login.site';

			$response->username = $result->username;
			$result = $app->triggerEvent('onUserLogin', array((array)$response, $options));
		}

		// if OK go to redirect page
		if ($urlredirect) {
			if ($result) {
				$app->redirect($urlredirect);
			}
		}
		return true;
	}
	
	/*
		Set a user to logged in with plain password
	*/
	function plainLogin($username, $plain_password, $urlredirect = null) {
		// Get the application object.
		$app = JFactory::getApplication();

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $username;
		$credentials['password'] = $plain_password;

		$options = array();
		$result = $app->login($credentials, $options);

		// if OK go to redirect page
		if ($urlredirect) {
			if ($result) {
				$app->redirect($urlredirect);
			}
		}

		return true;
	}
}
?>