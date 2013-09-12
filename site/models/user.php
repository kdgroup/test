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

jimport('joomla.application.input');
jimport('joomla.event.dispatcher');
jimport('joomla.environment.response');

jimport( 'joomla.application.component.model' );
require_once(JPATH_ROOT . DS . "components" . DS . "com_users" . DS . "models" . DS . "registration.php");
    class EnmasseModelUser extends JModel
    {

        function getUser()
        {
            return JFactory::getUser();
        }
        
        function checkEmailExisted($user_id,$email)
        {
            $db =& JFactory::getDBO();
            $query = "SELECT `id`,`name`,`username`,`email` 
            FROM #__users WHERE email = '{$email}' AND id != '$user_id'";
            return $db->setQuery($query)->loadAssoc();
        }
    	
        function getUserByUName($userName)
        {
            $db =& JFactory::getDBO();
            $query = "SELECT `id`,`name`,`username`,`email` FROM #__users WHERE username = '{$userName}'";
            return $db->setQuery($query)->loadAssoc();
        }
        
        function getUserByEmail($email)
        {
            $db =& JFactory::getDBO();
            $query = "SELECT `id`,`name`,`username`,`email` FROM #__users WHERE email = '{$email}'";
            $userData = $db->setQuery($query)->loadAssoc();
            return $userData;
        }
        
        function createAccount($username,$password,$fullname,$email)
        {
			// If registration is disabled - Redirect to login page.
//			if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
//				$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
//				return false;
//			}

			$requestData = array();
			$requestData['name'] = $fullname;
			$requestData['username'] = $username;
			$requestData['email'] = $email;
			$requestData['password'] = $password;
			$requestData['groups'] = array(2);

			$user = JFactory::getUser();
			$user->bind($requestData);
			
			return $user->save();
		}
		
        function editAccount($user_id,$email,$fullname)
		{
			$requestData = array();
			$requestData['name'] = $fullname;
			$requestData['email'] = $email;

			$user = JFactory::getUser($user_id);
			$user->bind($requestData);
			
			return $user->save();
		}
		
		function changePassword($user_id, $new_password)
		{
			$requestData = array();
			$requestData['password'] = $new_password;
			$requestData['password2'] = $new_password;
			
			$user = JFactory::getUser($user_id);
			$user->bind($requestData);
			
			return $user->save();
		}
		
		public function login($credentials, $options = array())
		{
			// Get the global JAuthentication object.
			jimport('joomla.user.authentication');
	
			$authenticate = JAuthentication::getInstance();
			$response = $authenticate->authenticate($credentials, $options);
	
			if ($response->status === JAuthentication::STATUS_SUCCESS)
			{
				// validate that the user should be able to login (different to being authenticated)
				// this permits authentication plugins blocking the user
				$authorisations = $authenticate->authorise($response, $options);
				foreach ($authorisations as $authorisation)
				{
					$denied_states = array(JAuthentication::STATUS_EXPIRED, JAuthentication::STATUS_DENIED);
					if (in_array($authorisation->status, $denied_states))
					{
						// Trigger onUserAuthorisationFailure Event.
						$this->triggerEvent('onUserAuthorisationFailure', array((array) $authorisation));
	
						// If silent is set, just return false.
						if (isset($options['silent']) && $options['silent'])
						{
							return false;
						}
	
						// Return the error.
						switch ($authorisation->status)
						{
							case JAuthentication::STATUS_EXPIRED:
								return JError::raiseWarning('102002', JText::_('JLIB_LOGIN_EXPIRED'));
								break;
							case JAuthentication::STATUS_DENIED:
								return JError::raiseWarning('102003', JText::_('JLIB_LOGIN_DENIED'));
								break;
							default:
								return JError::raiseWarning('102004', JText::_('JLIB_LOGIN_AUTHORISATION'));
								break;
						}
					}
				}
	
				// Import the user plugin group.
				JPluginHelper::importPlugin('user');
	
				// OK, the credentials are authenticated and user is authorised.  Lets fire the onLogin event.
//				$results = $this->triggerEvent('onUserLogin', array((array) $response, $options));
				$results = array(true);
				/*
				 * If any of the user plugins did not successfully complete the login routine
				 * then the whole method fails.
				 *
				 * Any errors raised should be done in the plugin as this provides the ability
				 * to provide much more information about why the routine may have failed.
				 */
	
				if (!in_array(false, $results, true))
				{
					// Set the remember me cookie if enabled.
					if (isset($options['remember']) && $options['remember'])
					{
						jimport('joomla.utilities.simplecrypt');
	
						// Create the encryption key, apply extra hardening using the user agent string.
						$key = self::getHash(@$_SERVER['HTTP_USER_AGENT']);
	
						$crypt = new JSimpleCrypt($key);
						$rcookie = $crypt->encrypt(serialize($credentials));
						$lifetime = time() + 365 * 24 * 60 * 60;
	
						// Use domain and path set in config for cookie if it exists.
						$cookie_domain = $this->getCfg('cookie_domain', '');
						$cookie_path = $this->getCfg('cookie_path', '/');
						setcookie(self::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain);
					}
	
					return true;
				}
			}
	
			// Trigger onUserLoginFailure Event.
			//$this->triggerEvent('onUserLoginFailure', array((array) $response));
	
			// If silent is set, just return false.
			if (isset($options['silent']) && $options['silent'])
			{
				return false;
			}
			
			// If status is success, any error will have been raised by the user plugin
			if ($response->status !== JAuthentication::STATUS_SUCCESS)
			{
				JError::raiseWarning('102001', $response->error_message);
			}

			return false;
		}

		public function logout($userid = null, $options = array())
		{
			// Get a user object from the JApplication.
			$user = JFactory::getUser($userid);
	
			// Build the credentials array.
			$parameters['username'] = $user->get('username');
			$parameters['id'] = $user->get('id');
	
			// Set clientid in the options array if it hasn't been set already.
			if (!isset($options['clientid']))
			{
				$options['clientid'] = $this->getClientId();
			}
	
			// Import the user plugin group.
			JPluginHelper::importPlugin('user');
	
			// OK, the credentials are built. Lets fire the onLogout event.
			$results = $this->triggerEvent('onUserLogout', array($parameters, $options));
	
			// Check if any of the plugins failed. If none did, success.
	
			if (!in_array(false, $results, true))
			{
				// Use domain and path set in config for cookie if it exists.
				$cookie_domain = $this->getCfg('cookie_domain', '');
				$cookie_path = $this->getCfg('cookie_path', '/');
				setcookie(self::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);
	
				return true;
			}
	
			// Trigger onUserLoginFailure Event.
			$this->triggerEvent('onUserLogoutFailure', array($parameters));
	
			return false;
		}

		public function triggerEvent($event, $args = null)
		{
			$dispatcher = JDispatcher::getInstance();
	
			return $dispatcher->trigger($event, $args);
		}
		
	
    }
?>