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


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class EnmasseModelWebserviceSession extends JModel
{
    public static $SESSION_LIFE_TIME = 525600; //5 minute

    public function updateSessionLifetime($token)
    {
        $db = JFactory::getDbo();
        $query = "UPDATE #__enmasse_ws_session SET expired_at = NOW() + INTERVAL " .self::$SESSION_LIFE_TIME ." MINUTE WHERE token= '$token'";

        $db->setQuery($query);
        $db->query();
    }

    public function deleteSession($token)
    {
        $db = JFactory::getDbo();
        $query = "DELETE FROM #__enmasse_ws_session WHERE token='$token'";
        $db->setQuery($query)->query();
        $result = false;
        
        if($db->getAffectedRows() > 0) 
        {
            $result = true;
        }
        
        // Delete expired session
        $query = "DELETE FROM #__enmasse_ws_session WHERE expired_at < NOW() AND token='$token'";
        $db->setQuery($query)->query();
		return $result;
    }

    public function getByToken($token)
    {
        $db = JFactory::getDbo();
        $query = "SELECT *, NOW() as curtime FROM #__enmasse_ws_session WHERE token= '$token'";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    public function createSession($userId, $merchantId)
    {
        //garbage collection
        $this->sessionGC();
        $token = $this->generateToken();
        $db = JFactory::getDbo();
        $query = "INSERT INTO #__enmasse_ws_session (token, user_id,merchant_id, created_at, expired_at) VALUE ('$token', $userId, $merchantId, NOW(), NOW()+ INTERVAL " .self::$SESSION_LIFE_TIME ." MINUTE)";

        $db->setQuery($query);
        $db->query();
        if($db->getAffectedRows() > 0) 
        {
            return $token;
        }else
        {
            return false;
        }
    }

    public function sessionGC()
    {
        $db = JFactory::getDbo();
        $query = "DELETE FROM #__enmasse_ws_session
        WHERE expired_at < NOW()";

        $db->setQuery($query);
        $db->query();

    }

    private function generateToken($length = 32)
    {
        //mimic from joomla session token
        static $chars	=	'0123456789abcdef';
        $max			=	strlen($chars) - 1;
        $token			=	'';
        $name			=  session_name();
        for ($i = 0; $i < $length; ++$i) {
            $token .=	$chars[ (rand(0, $max)) ];
        }

        return md5($token.$name);

    }

    public function validToken($token,$user_id)
    {
        $db = JFactory::getDbo();
        $query = "SELECT *, NOW() as curtime FROM #__enmasse_ws_session WHERE token= '$token' and `expired_at` > NOW() and `user_id`='$user_id'";

        // Return format
//		Array
//		(
//		    [token] => ca25f2df6425e0367072ad54db773737
//		    [user_id] => 54
//		    [merchant_id] => 0
//		    [created_at] => 2012-06-05 16:32:24
//		    [expired_at] => 2013-06-05 16:32:24
//		    [curtime] => 2012-06-06 10:28:04
//		)
        
        return $db->setQuery($query)->loadAssoc();
    }

}
