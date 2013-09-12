<?php
/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */
// No direct access 
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "config.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "fbconfig.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "twconfig.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "googleconfig.php");

require_once( JPATH_SITE . DS . "components" . DS . "com_enmasse" . DS. "helpers" . DS . "sociallogin" . DS . "SocialLoginHelper.php");

class EnmasseControllerAuthentication extends JController {
	function __construct()
    {
    parent::__construct();
    }
	
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'authentication');
		JRequest::setVar('layout', 'login');
		parent::display();
	}
	
	function login()
	{
		$guest = JFactory::getUser()->guest;
		if($guest)
		{
			if($_POST['isLoginWithFacebook'])
				EnmasseControllerAuthentication::login_facebook();
			else if($_POST['isLoginWithTwitter'])
				EnmasseControllerAuthentication::login_twitter();
			else if($_POST['isLoginWithGoogle'])
				EnmasseControllerAuthentication::login_google();
			else return null;
		}
		else return null;
	}
	
	function login_facebook()
	{
		require_once(JPATH_SITE . DS."components".DS."com_enmasse".DS."helpers".DS."sociallogin".DS."api".DS."facebook".DS."facebook.php");
		$facebook = new Facebook(array(
			'appId'  => FB_APP_ID,
			'secret' => FB_SECRET,
			'cookie' => FB_COOKIE,
		));
		$fbuser = $facebook->getUser();
		if ($fbuser) {
			try {
				$user_profile = $facebook->api("/me");
				$returnURL = JRequest::getVar('return', '');
				$returnURL = JURI::base() . substr(base64_decode($returnURL), 1);
				SocialLoginHelper::checkUser($user_profile, $returnURL);
			} catch (FacebookApiException $e) {
				$fbuser = null;
			}
		}
	}
	function login_twitter()
	{
		require_once(JPATH_SITE . DS."components".DS ."com_enmasse".DS."helpers".DS."sociallogin".DS."api".DS."twitter".DS."twitteroauth.php");
		
		$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);
		
		// Requesting authentication tokens, the parameter is the URL we will be redirected to
		$request_token = $twitteroauth->getRequestToken( JURI::base() . 'components/com_enmasse/helpers/sociallogin/getTwitterData.php');

		// Saving them into the session
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		$_SESSION['return'] = JRequest::getVar('return', 'Lw');

		// If everything goes well..
		if ($twitteroauth->http_code == 200) {
			// Let's generate the URL and redirect
			$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
			header('Location: ' . $url);
		} else {
			// It's a bad idea to kill the script, but we've got to know when there's an error.
			die('Something wrong happened.');
		}
		
	}
	function twitter()
	{

		//echo '<pre>';print_r($_GET);echo '</pre>';
		//echo '<pre>';print_r($_POST);echo '</pre>';
		if(isset($_SESSION['oauth_token']))
		{
			if(isset($_GET['twitterId']) && isset($_GET['name']))
			{
				//check $username = twitter.xxxxxxxxx
				$username = 'twitter.' . JRequest::getVar('twitterId');
				
				$db = JFactory::getDBO();
				$query = "SELECT * FROM #__users WHERE `username` = '$username'";
				$db->setQuery( $query );
				$dbuser = $db->loadObject();

				if ($db->getErrorNum()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				
				if(!$dbuser)
				{
					//This is the first time that user uses his twitter account to log in
					//Request his/her email
					JRequest::setVar('view', 'authentication');
					JRequest::setVar('layout', 'twitter');
					parent::display();
				}
				else
				{
					$returnURL = $_SESSION['return'];
					$returnURL = JURI::base() . substr(base64_decode($returnURL), 1);
					SocialLoginHelper::login($dbuser->username, $dbuser->password, $returnURL);
				}
			}
			else
			{			
				//This is the first time that user uses his twitter account to log in
				//Receive the post value (email, name and twitterId)
				$user = array();
				$user['name']		= JRequest::getVar('name', '');
				$user['email']		= JRequest::getVar('txtemail', '');
				$user['twitterId']	= JRequest::getVar('twitterId', '');
				
				$returnURL = $_SESSION['return'];
				$returnURL = JURI::base() . substr(base64_decode($returnURL), 1);
				SocialLoginHelper::checkUser_Twitter($user, $returnURL);
			}
		}
		else
		{
			//Login fail
			$link = JRoute::_('index.php', false);
			$msg = JText::_('SOCIAL_LOGIN_FAIL');
			JFactory::getApplication()->redirect($link, $msg);
		}
	}
	function login_google()
	{
		require_once(JPATH_SITE . DS."components".DS."com_enmasse".DS."helpers".DS."sociallogin".DS."api".DS."google".DS."openid.php");
		
		$returnURL = JRequest::getVar('return', 'Lw');
		$returnURL = JURI::base() . substr(base64_decode($returnURL), 1);
		$_SESSION['returnURL'] = $returnURL;
		
		$callbackURL = JURI::base() . 'components/com_enmasse/helpers/sociallogin/getGoogleData.php';
		
		// Creating new instance
		$openid = new LightOpenID;
		$openid->identity = 'https://www.google.com/accounts/o8/id';
		//setting call back url
		$openid->returnUrl = $callbackURL;
		//finding open id end point from google
		$endpoint = $openid->discover('https://www.google.com/accounts/o8/id');
		$fields =
				'?openid.ns=' . urlencode('http://specs.openid.net/auth/2.0') .
				'&openid.return_to=' . urlencode($openid->returnUrl) .
				'&openid.claimed_id=' . urlencode('http://specs.openid.net/auth/2.0/identifier_select') .
				'&openid.identity=' . urlencode('http://specs.openid.net/auth/2.0/identifier_select') .
				'&openid.mode=' . urlencode('checkid_setup') .
				'&openid.ns.ax=' . urlencode('http://openid.net/srv/ax/1.0') .
				'&openid.ax.mode=' . urlencode('fetch_request') .
				'&openid.ax.required=' . urlencode('email,firstname,lastname') .
				'&openid.ax.type.firstname=' . urlencode('http://axschema.org/namePerson/first') .
				'&openid.ax.type.lastname=' . urlencode('http://axschema.org/namePerson/last') .
				'&openid.ax.type.email=' . urlencode('http://axschema.org/contact/email');
		header('Location: ' . $endpoint . $fields);
	}
	function logout()
	{
		session_destroy();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$user_id = $user->get($user->id);
		$app->logout($user_id, array());
		$app->redirect(JURI::base());
	}
}

?>