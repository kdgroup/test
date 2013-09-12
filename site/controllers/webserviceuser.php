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

define('EM_WS_TYPE_OK', 'OK');
define('EM_WS_TYPE_ERROR', 'ERROR');
define('EM_WS_FIELD_TYPE', 'Type');
define('EM_WS_FIELD_MESSAGE', 'Message');
define('EM_WS_FIELD_DATA', 'Data');

require_once(JPATH_ADMINISTRATOR . DS . "components" . DS . "com_enmasse" . DS . "helpers" . DS . "EnmasseHelper.class.php");
class EnmasseControllerWebserviceuser extends JController
{

	public function display()
	{
		echo '<pre>';
		print_r("alollllooo");
		echo '</pre>';
		exit();
	}

	private function authenticate()
	{
		$token = JRequest::getVar('token','', 'post');
		$result = array();
		$oSession = JModel::getInstance('WebserviceSession','EnmasseModel')->getByToken($token);

		if($oSession)
		{
			if(strtotime($oSession->expired_at) < strtotime($oSession->curtime))
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_SESSION_EXPIRED");;
				$result[EM_WS_FIELD_DATA] = "";
				echo json_encode($result);
				die;//do not continue processing
			}else
			{
				return $oSession;
			}
		}else
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_NOT_LOGGED_IN");
			$result[EM_WS_FIELD_DATA] = "";
			echo json_encode($result);
			die;//do not continue processing
		}
	}

	private function updateSession()
	{
		$token = JRequest::getVar('token','', 'post');
		JModel::getInstance('WebserviceSession','EnmasseModel')->updateSessionLifetime($token);
	}

	private function convertDealToArray($deal)
	{
		$temp = array();

		$temp['id'] = $deal->id;
		$temp['deal_code'] = $deal->deal_code;
		$temp['name'] = $deal->name;
		$temp['slug_name'] = $deal->slug_name;
		$temp['short_desc'] = $deal->short_desc;

		$imageUrlArr = unserialize(urldecode($deal->pic_dir));
		$temp['pic_dir'] = JURI::base().str_replace("\\","/",$imageUrlArr[0]);;

		$temp['highlight'] = htmlentities($deal->highlight);
		$temp['terms'] = htmlentities($deal->terms);
		$temp['description'] = htmlentities($deal->description);

		$temp['origin_price'] = $deal->origin_price;
		$temp['price'] = $deal->price;
		$temp['discount'] = empty($deal->origin_price)? 100 : (100 - intval($deal->price/$deal->origin_price*100));

		$temp['min_needed_qty'] = $deal->min_needed_qty;
		$temp['max_buy_qty'] = $deal->max_buy_qty;
		$temp['max_coupon_qty'] = $deal->max_coupon_qty;
		$temp['max_qty'] = $deal->max_qty;
		$temp['cur_sold_qty'] = $deal->cur_sold_qty;
		$temp['start_at'] = $deal->start_at;
		$temp['end_at'] = $deal->end_at;
		$temp['merchant_id'] = $deal->merchant_id;
		$temp['sales_person_id'] = $deal->sales_person_id;
		$temp['status'] = $deal->status;
		$temp['published'] = $deal->published;
		$temp['position'] = $deal->position;
		$temp['pay_by_point'] = $deal->pay_by_point;
		$temp['created_at'] = $deal->created_at;
		$temp['updated_at'] = $deal->updated_at;
		$temp['prepay_percent'] = $deal->prepay_percent;
		$temp['auto_confirm'] = $deal->auto_confirm;

		return $temp;
	}


	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=userLogin
	// Input (POST) : username ,password
	// Output : JSON

	public function userLogin()
	{
		//    	$_POST['username'] = 'apolo';
		//    	$_POST['password'] = '123';
			
		$result = array();
		$username = JRequest::getVar('username', '', 'post');
		$password = JRequest::getVar('password', '', 'post');
		$credential = array('username' =>$username, 'password' => $password);

		//        $app = JFactory::getApplication();
		//        $bValid = $app->login($credential);

		$userModel = JModel::getInstance('User','EnmasseModel');
		// Check username
		$bValid = $userModel->login($credential);

		if($bValid)
		{
			$user = $userModel->getUserByUName($username);
			//            $user = JFactory::getUser();
			$emSesion = JModel::getInstance('WebserviceSession','EnmasseModel');
			$token = $emSesion->createSession($user['id'], 0);
			$user['token'] = $token;
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_LOGIN_SUCCESSFUL");
			$result[EM_WS_FIELD_DATA] = $user;
		}
		else
		{
			//            $oError = array_pop($app->getMessageQueue());
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = $oError['message'];
			$result[EM_WS_FIELD_DATA] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=userLogout
	// Input (POST) : token
	// Output : JSON

	public function userLogout()
	{
		$result = array();
		$token = JRequest::getVar('token', 'null', 'post');
		$bValid = JModel::getInstance('WebserviceSession','EnmasseModel')->deleteSession($token);
		if($bValid)
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("USER_WS_LOGOUT_SUCCESSFUL");
			$result[EM_WS_FIELD_DATA] = "";
		}else
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("USER_WS_SESSION_EXPIRED");
			$result[EM_WS_FIELD_DATA] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=validateUserAccount
	// Input (POST) : username ,password
	// Output : JSON

	public function validateUserAccount()
	{
		$result = array();
		$username = JRequest::getVar('username', '', 'post');
		$password = JRequest::getVar('password', '', 'post');
		$credential = array('username' =>$username, 'password' => $password);
		//        $app = JFactory::getApplication();
		//        $bValid = $app->login($credential);

		$userModel = JModel::getInstance('User','EnmasseModel');
		// Check username
		$bValid = $userModel->login($credential);

		if($bValid)
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("USER_WS_ACCOUNT_IS_VALID");
			$result[EM_WS_FIELD_DATA] = "";
		}else
		{
			//            $oError = array_pop($app->getMessageQueue());
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("USER_WS_ACCOUNT_IS_INVALID");
			$result[EM_WS_FIELD_DATA] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=validateCreateAccount
	// Input (POST) : username ,password
	// Output : JSON

	public function validateCreateAccount()
	{
		$result = array();
		$username = JRequest::getVar('username', '', 'post');
		$email = JRequest::getVar('email', '', 'post');

		$userModel = JModel::getInstance('User','EnmasseModel');

		// Check username
		$userData = $userModel->getUserByUName($username);
		if (!empty($userData))
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('ACCOUNT_USERNAME_IS_EXISTED');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		if (!preg_match( "/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/", $email))
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('ACCOUNT_EMAIL_INVALIDTED');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		// Check email
		$userData = $userModel->getUserByEmail($email);
		if (!empty($userData))
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('ACCOUNT_EMAIL_IS_EXISTED');
			$result['Data'] = "";
			echo json_encode($result);die;
		}
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=createUserAccount
	// Input (POST) : username ,password
	// Output : JSON

	public function createUserAccount()
	{
		$result = array();

		//        $_POST['username'] = "hailong";
		//        $_POST['password'] = "123456";
		//        $_POST['fullname'] = "duc bui";
		//        $_POST['email'] = "hailong@gmail.com";

		$username = JRequest::getVar('username', '', 'post');
		$password = JRequest::getVar('password', '', 'post');
		$fullname = JRequest::getVar('fullname', '', 'post');
		$email = JRequest::getVar('email', '', 'post');

		$userModel = JModel::getInstance('User','EnmasseModel');
		$userData =  $userModel->getUserByUName($username);

		// Validate first
		$this->validateCreateAccount();

		$ifSuccess = $userModel->createAccount($username,$password,$fullname,$email);

		if ($ifSuccess)
		{
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('CREATE_ACCOUNT_SUCCESSFULLY');
			$result['Data'] = "";
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('CAN_NOT_CREATE_ACCOUNT');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=editUserAccount
	// Input (POST) : user_id ,email ,fullname ,token
	// Output : JSON

	public function editUserAccount()
	{
		$result = array();

		//        $_POST['user_id'] = "54";
		//        $_POST['email'] = "apolo@yahoo.com";
		//        $_POST['fullname'] = "apoloooooo to";
		//        $_POST['token'] = "6527e468cbabd4ba9ccd683dd96a1728";

		$user_id = JRequest::getVar('user_id', '', 'post');
		$email  = JRequest::getVar('email', '', 'post');
		$fullname = JRequest::getVar('fullname', '', 'post');
		$token = JRequest::getVar('token', '', 'post');

		if (!preg_match( "/^[-\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$/", $email))
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('ACCOUNT_EMAIL_INVALIDTED');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		$userModel = JModel::getInstance('User','EnmasseModel');
		// Check email
		$userData = $userModel->checkEmailExisted($user_id,$email);
		 
		if (!empty($userData))
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('ACCOUNT_EMAIL_IS_EXISTED');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		//verify token
		$tokenModel = JModel::getInstance('WebserviceSession','EnmasseModel');
		$rowToken = $tokenModel->validToken($token,$user_id);
		if (!empty($rowToken))
		{
			//ok to edit now
			$isOk = $userModel->editAccount($user_id,$email,$fullname);
			if ($isOk)
			{
				$result['Type'] = 'OK';
				$result['Message'] = JText::_('EDIT_ACCOUNT_SUCCESSFULLY');
				$result['Data'] = "";
			}
			else
			{
				$result['Type'] = 'ERROR';
				$result['Message'] = JText::_('EDIT_ACCOUNT_FAIL');
				$result['Data'] = "";
			}
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('TOKEN_INVAILD');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=changePassword
	// Input (POST) : user_id ,new_password ,token
	// Output : JSON

	public function changePassword()
	{
		$result = array();
		$user_id = JRequest::getVar('user_id', '', 'post');
		$new_password  = JRequest::getVar('new_password', '', 'post');
		$token = JRequest::getVar('token', '', 'post');

		$tokenModel = JModel::getInstance('WebserviceSession','EnmasseModel');
		$rowToken = $tokenModel->validToken($token,$user_id);

		if (!empty($rowToken))
		{
			$isOk = JModel::getInstance('User','EnmasseModel')->changePassword($user_id,$new_password);
			if ($isOk)
			{
				$result['Type'] = 'OK';
				$result['Message'] = JText::_('CHANGE_PASSWORD_SUCCESSFULLY');
				$result['Data'] = "";
			}
			else
			{
				$result['Type'] = 'ERROR';
				$result['Message'] = JText::_('CHANGE_PASSWORD_FAIL');
				$result['Data'] = "";
			}
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('TOKEN_INVAILD');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getCitites
	// Input (POST) :
	// Output : JSON

	public function getCitites()
	{
		$locationModel = JModel::getInstance('DealLocation','EnmasseModel')->getAllLocation();
		if (!empty($locationModel))
		{
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('LOCATION_LIST');
			$result['Data'] = $locationModel;
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('THERE_IS_NO_LOCATION');
			$result['Data'] = "";
		}
		echo json_encode($result);
		die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getAllDeal
	// Input (POST) : city_id ,type_id ,limit_row
	// Output : JSON

	public function getAllDeal()
	{
		/*
		 * type_id = 0 => Deals today
		 * type_id = 1 => Deals all
		 * type_id = 2 => Deals upcoming
		 * type_id = 3 => Deals expired
		 * */
			
		$result = array();
		$location_id = JRequest::getVar('city_id', '', 'post');
		$type_id  = JRequest::getVar('type_id', '', 'post');
		$limit_row  = JRequest::getVar('limit_row', '15', 'post');

		$type_id = intval($type_id);

		if ($type_id == 0) {

			$allDeals = JModel::getInstance('Deal','EnmasseModel')->ws_todayDealList($location_id,$limit_row);

		} elseif ($type_id == 1) {

			$allDeals = JModel::getInstance('Deal','EnmasseModel')->ws_startedPublishedDealList($location_id,$limit_row);

		} elseif ($type_id == 2) {

			$allDeals = JModel::getInstance('Deal','EnmasseModel')->ws_upcomingDealList($location_id,$limit_row);


		} elseif ($type_id == 3) {

			$allDeals = JModel::getInstance('Deal','EnmasseModel')->ws_expiredPublishedDealList($location_id,$limit_row);

		} else {
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('DEAL_LIST_NO_DEAL_MESSAGE');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		$resultDeal = array();
		foreach ($allDeals as $deal)
		$resultDeal[] = $this->convertDealToArray($deal);

		$result['Type'] = 'OK';
		$result['Message'] = JText::_('DEAL_IS_ON');
		$result['Data'] = $resultDeal;
			
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getDealDetail
	// Input (POST) : deal_id
	// Output : JSON

	public function getDealDetail()
	{
		$result = array();
		$deal_id = JRequest::getVar('deal_id', 'null', 'post');
		$dealInfo = JModel::getInstance('Deal','EnmasseModel')->getById($deal_id);
		if (!empty($dealInfo))
		{
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('DEAL_IS_ON');
			$result['Data'] = $this->convertDealToArray($dealInfo);
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('DEAL_LIST_NO_DEAL_MESSAGE');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getDealDescription
	// Input (POST) : deal_id
	// Output : JSON

	public function getDealDescription()
	{
		$result = array();
		$deal_id = JRequest::getVar('deal_id', 'null', 'post');
		$dealInfo = JModel::getInstance('Deal','EnmasseModel')->getById($deal_id);


		if (!empty($dealInfo))
		{
			$returndealInfo = array(
            	'deal_id' => $deal_id,
		        'deal_code' => $dealInfo->deal_code,
		        'deal_description' => htmlentities($dealInfo->description) . ""
		        );
		         
		        $result['Type'] = 'OK';
		        $result['Message'] = JText::_('DEAL_IS_ON');
		        $result['Data'] = $returndealInfo;
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('DEAL_LIST_NO_DEAL_MESSAGE');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getDealCommentList
	// Input (POST) : deal_id
	// Output : JSON

	public function getDealCommentList()
	{
		$result = array();
		$deal_id = JRequest::getVar('deal_id', 'null');

		$dealInfo = JModel::getInstance('Deal','EnmasseModel')->getById($deal_id);
		if (!empty($dealInfo))
		{
			$allComment = JModel::getInstance('Comment','EnmasseModel')->getFullCommentByDealId($deal_id);
			if (!empty($allComment))
			{
				$result['Type'] = 'OK';
				$result['Message'] = 'List of comment';
				$result['Data'] = $allComment;
			}
			else
			{
				$result['Type'] = 'OK';
				$result['Message'] = 'There is no comment belong to this deal';
				$result['Data'] = "";
			}
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('DEAL_LIST_NO_DEAL_MESSAGE');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getComment
	// Input (POST) : comment_id
	// Output : JSON

	public function getComment()
	{
		$result = array();
		$comment_id = JRequest::getVar('comment_id', 'null', 'post');
		$comment = JModel::getInstance('Comment','EnmasseModel')->getById($comment_id);
		if (!empty($comment))
		{
			$result['Type'] = 'OK';
			$result['Message'] = 'Comment detail';
			$result['Data'] = $comment;
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('COMMENT_ERROR');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=postComment
	// Input (POST) : deal_id ,token ,comment_content ,rating_count
	// Output : JSON

	public function postComment()
	{
		//    	$_POST['deal_id'] = 5;
		//    	$_POST['token'] = '245f21ec668c13871c0c7d7779beb5ba';
		//    	$_POST['comment_content'] = 'hang xin khong ai bang 5 sao';
		//    	$_POST['rating_count'] = 2;
			
		$result = array();

		// Check deal invalid
		$deal_id = JRequest::getVar('deal_id', 'null', 'post');

		$token = JRequest::getVar('token', '', 'post');

		//verify token
		//        $tokenModel = JModel::getInstance('WebserviceSession','EnmasseModel');
		//        $rowToken = $tokenModel->validToken($token,$user_id);

		$token_data = JModel::getInstance('WebserviceSession','EnmasseModel')->getByToken($token);
		if (empty($token_data)) {
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('TOKEN_INVAILD');
			$result['Data'] = "";
			echo json_encode($result);die;
		}

		//		$token_data = JModel::getInstance('WebserviceSession','EnmasseModel')->getByToken($token);
		$data = array (
			'deal_id' => $deal_id,
			'user_id' => $token_data->user_id,
			'comment' => JRequest::getVar('comment_content', 'null', 'post'),
			'rating'  => JRequest::getVar('rating_count', '3', 'post'),
			'created_at' => date('Y-m-d H:i:s')
		);
		$saveOk = JModel::getInstance('Comment','EnmasseModel')->store($data);

		if (!empty($saveOk->success))
		{
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('POST_COMMENT_SUCESSFULLY');
			$result['Data'] = "";
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('POST_COMMENT_FAIL');
			$result['Data'] = "";
		}

		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getOrderHistory
	// Input (POST) : user_id ,token
	// Output : JSON

	public function getOrderHistory()
	{
		$result = array();
		$user_id = JRequest::getVar('user_id', 'null', 'post');
		$token = JRequest::getVar('token', 'null', 'post');

		//        $user_id = JRequest::getVar('user_id', '54', 'post');
		//        $token = JRequest::getVar('token', 'ca25f2df6425e0367072ad54db773737', 'post');

		$tokenModel = JModel::getInstance('WebserviceSession','EnmasseModel');
		$rowToken = $tokenModel->validToken($token,$user_id);

		if (!empty($rowToken))
		{
			$list = JModel::getInstance('Order','EnmasseModel')->ws_getOrderHistory($user_id);
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('GET_ORDER_HISTORY');
			$result['Data'] = $list;
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('TOKEN_INVAILD');
			$result['Data'] = "";
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=getPaypalConf
	// Input (POST) : deal_id ,token ,comment_content ,rating_count
	// Output : JSON

	public function getPaypalConf()
	{
		$result = array();

		$paypalConf = JModel::getInstance('PayGty','EnmasseModel')->getByClass('paypal');

		if (!empty($paypalConf))
		{
			$result['Type'] = 'OK';
			$result['Message'] = JText::_('PAYPAL_DATA');
			$result['Data'] = json_decode($paypalConf->attribute_config);
		}
		else
		{
			$result['Type'] = 'ERROR';
			$result['Message'] = JText::_('CAN_NOT_RETRIVE_PAYPAL_SETTINGS');
			$result['Data'] = NULL;
		}
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=postPaymentTracking
	// Input (POST) : user_id ,token ,deal_id ,quantity ,tracking_content (JSON)
	//				,buy4friend	,receiver_address ,receiver_phone
	//	            ,receiver_name ,receiver_email ,receiver_msg
	// Output : JSON

	public function postPaymentTracking() {

		$arr['user_id'] = JRequest::getVar('user_id',"");
		$arr['token'] = JRequest::getVar('token',"");
		$arr['deal_id'] = JRequest::getVar('deal_id',"");
		$arr['quantity'] = JRequest::getVar('quantity',"");
		$arr['buy4friend'] = JRequest::getVar('buy4friend',"");
		$arr['tracking_content'] = JRequest::getVar('tracking_content',"");
		
		$arr['receiver_address'] = JRequest::getVar('receiver_address',"");
		$arr['receiver_phone'] = JRequest::getVar('receiver_phone',"");
		$arr['receiver_name'] = JRequest::getVar('receiver_name',"");
		$arr['receiver_email'] = JRequest::getVar('receiver_email',"");
		$arr['receiver_msg'] = JRequest::getVar('receiver_msg',"");
		
		$result = array();
		$result['Type'] = 'OK';
		$result['Message'] = JText::_('PAYMENT_SUCCESSFULLY');
		$result['Data'] = json_encode($arr);
		echo json_encode($result);die;
	}

	//-------------------------------
	// /index.php?option=com_enmasse&controller=webserviceuser&task=postPaymentTracking
	// Input (POST) : user_id ,token ,deal_id ,quantity ,tracking_content (JSON)
	//				,buy4friend	,receiver_address ,receiver_phone
	//	            ,receiver_name ,receiver_email ,receiver_msg
	// Output : JSON

	public function postPaymentTracking_bak()
	{
		//		$user_id = JRequest::getVar('user_id', 'null', 'post');

		// get current user
		$currentUser = null;

		$result = array();
			
		$dealId = JRequest::getVar('dealId');

		$quantity = JRequest::getVar('quantity');

		$referralId = JRequest::getVar('referralid');
		$bBuy4friend = JRequest::getVar('buy4friend',0);
		$deal = JModel::getInstance('deal','enmasseModel')->getById($dealId);
			
		// We only allow 1 item per cart from now one...
		$cart = new Cart();
		$cart->addItem($deal);
		$cart->changeItem($dealId, $quantity);

		//Set sesstion for referral ID
		if($referralId!='')
		{
			$cart->setReferralId($referralId);
		}

		$dealName = $deal->name;
		$cartItemCount = $cart->getItem($dealId)->getCount();

		foreach($cart->getAll() as $cartItem)
		{
			$item = $cartItem->getItem();
		}
		//get enmasse setting
		$setting = JModel::getInstance('setting','enmasseModel')->getSetting();

		//		validate Buyer information
		$buyerName 	= JRequest::getVar('name');
		$buyerEmail 	= JRequest::getVar('email');

		if(empty($buyerName) || empty($buyerEmail))
		{
			$msg = JText::_("SHOP_CARD_CHECKOUT_BUYER_INFORMATION_REQUIRED_MSG");
		}
		elseif (!preg_match($sEmailPt, $buyerEmail))
		{
			$msg = JText::_("SHOP_CARD_CHECKOUT_BUYER_EMAIL_INVALID_MSG");
		}

		//----- If the deal permit partial payment, it mean the coupon was delivery by directly, so we need to validate address and phone number of receiver
		if($item->prepay_percent <100)
		{
			$receiverAddress = JRequest::getVar('receiver_address');
			$receiverPhone = JRequest::getVar('receiver_phone');

			if(empty($receiverPhone) || empty($receiverAddress))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_INFORMATION_REQUIRED_MSG");
			}else if(!preg_match('/^[0-9 \.,\-\(\)\+]*$/', $receiverPhone))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_PHONE_INVALID");
			}
		}

		if($bBuy4friend)
		{
			$receiverName = JRequest::getVar('receiver_name');
			$receiverEmail = JRequest::getVar('receiver_email');
			$receiverMsg 	= JRequest::getVar('receiver_msg');
			if(empty($receiverName) || empty($receiverEmail))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_INFORMATION_REQUIRED_MSG");
			}
			elseif (!preg_match($sEmailPt, $receiverEmail))
			{
				$msg = JText::_("SHOP_CARD_CHECKOUT_RECEIVER_EMAIL_INVALID_MSG");
			}
		}

		//------------------------------------------------------
		// to check it this deal is free for customer
		if($cart->getTotalPrice() > 0)
		{
			//deal is not free
			$payGtyId 	= JRequest::getVar('payGtyId',2);
			// Default paypal

			$payGty = JModel::getInstance('payGty','enmasseModel')->getById($payGtyId);

			//--------If admin set the prepay_percent of the deal to 0.00, set the order status to 'Paid' (with paid_amount is 0.00)
			if($item->prepay_percent == 0.00)
			{
				$status = EnmasseHelper::$ORDER_STATUS_LIST['Paid'];
				$couponStatus = EnmasseHelper::$INVTY_STATUS_LIST['Hold'];
			}else
			{
				//------------------------------------
				// generate name of payment gateway file and class
				$payGtyFile = 'PayGty'.ucfirst($payGty->class_name).'.class.php';
				$className = 'PayGty'.ucfirst($payGty->class_name);
				//---------------------------------------------------
				// get payment gateway object
				require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."payGty". DS .$payGty->class_name. DS.$payGtyFile);
				$paymentClassObj = new $className();
				$paymentReturnStatusObj = $paymentClassObj->returnStatus();

				$status = $paymentReturnStatusObj->order;
				$couponStatus = $paymentReturnStatusObj->coupon;
			}
				
		}
		else
		{
			//deal is free
		}

		//----------------------------------------
		//determine information of coupon receiver
		if($bBuy4friend)
		{
			$deliveryDetail = array ('name' => $receiverName, 'email' => $receiverEmail, 'msg' => $receiverMsg, 'address' => $receiverAddress, 'phone' => $receiverPhone);
		}
		else
		{
			$deliveryDetail = array ('name' => $buyerName, 'email' => $buyerEmail, 'msg' => '', 'address' => $receiverAddress, 'phone' => $receiverPhone);
		}


		//--------------------------
		//generate order
		$dvrGty = ($item->prepay_percent < 100)? 2: 1;
		$deliveryGty 	= JModel::getInstance('deliveryGty','enmasseModel')->getById($dvrGty);

		$user = array();
		$user['id'] = $user_id;
		$user['name'] = $currentUser->name;
		$user['email'] = $currentUser->email;

		$order = CartHelper::saveOrder($cart, $user, $payGty, null, $deliveryGty, $deliveryDetail,$status);

		//		$session =& JFactory::getSession();
		//		$session->set( 'newOrderId', $order->id );

		$orderItemList 	= CartHelper::saveOrderItem($cart, $order,$status);

		//-----------------------------
		// if this deal is set limited the coupon to sold out, go to invty and allocate coupons for this order
		// if not create coupons for that order
		if($item->max_coupon_qty > 0)
		{
			$now = DatetimeWrapper::getDatetimeOfNow();
			$nunOfSecondtoAdd = (EnmasseHelper::getMinuteReleaseInvtyFromSetting($payGty))*60;
			$intvy = CartHelper::allocatedInvty($orderItemList,DatetimeWrapper::mkFutureDatetimeSecFromNow($now,$nunOfSecondtoAdd),$couponStatus);
		}
		else
		{
			JModel::getInstance('invty','enmasseModel')->generateForOrderItem($orderItemList[0]->pdt_id, $orderItemList[0]->id, $orderItemList[0]->qty, $couponStatus);
		}

		//------------------------
		//generate integration class
		$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
		if($isPointSystemEnabled)
		{
			$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
			$integrateFileName = $integrationClass.'.class.php';
			require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
			$user = JFactory::getUser();
			$user_id = $user->get('id');
			$point = $cart->getPoint();
			if($point>0) //If user buys with point, point will be greater than zero
			{
				$integrationObject = new $integrationClass();
				$integrationObject->integration($user_id,'paybypoint',$point);
			}
		}

		//validating is ok, flush user data
		JFactory::getApplication()->setUserState("com_enmasse.checkout.data", null);
			

		// Tracking return data
		$payDetail = "JSON form data";
		JModel::getInstance('order','enmasseModel')->updatePayDetail($order->id, $payDetail);

		$result['Type'] = 'OK';
		$result['Message'] = JText::_('PAYPAL_DATA');
		$result['Data'] = json_decode($paypalConf->attribute_config);


		//        $paypalConf = JModel::getInstance('PayGty','EnmasseModel')->getByClass('paypal');
		//
		//        if (!empty($paypalConf))
		//        {
		//            $result['Type'] = 'OK';
		//            $result['Message'] = JText::_('PAYPAL_DATA');
		//            $result['Data'] = json_decode($paypalConf->attribute_config);
		//        }
		//        else
		//        {
		//            $result['Type'] = 'ERROR';
		//            $result['Message'] = JText::_('CAN_NOT_RETRIVE_PAYPAL_SETTINGS');
		//            $result['Data'] = NULL;
		//        }
		//        echo json_encode($result);die;
	}



}

