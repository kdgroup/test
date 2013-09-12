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

jimport( 'joomla.application.component.model' );
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseModelOrder extends JModel
{
	
	//Order id
	function getById($orderId)
	{
		$db = JFactory::getDBO();
	    $query = "	SELECT 
	    				* 
	    			FROM 
	    				#__enmasse_order 
	    			WHERE
	              		id = $orderId";
	    $db->setQuery( $query );
	    $order = $db->loadObject();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
	    
		return $order;
	}
	//user id
	
	function listForBuyer($buyerId)
	{
		$db = JFactory::getDBO();
	    $query = "	SELECT 
	    				* 
	    			FROM 
	    				#__enmasse_order 
	    			WHERE
	    				status !='Unpaid' AND
	              		buyer_id = $buyerId
	              	ORDER BY
	              		created_at DESC";
	    $db->setQuery( $query );
	    $orderList = $db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderList;
	}
	
	function updateStatus($id, $value)
	{
            $db = JFactory::getDBO(); 
            //get status before update
            $query = 'SELECT * FROM #__enmasse_order where id ='.$id;
            $db->setQuery( $query );
            $rs = $db->loadObject();
            $current_status = $rs->status;
            
            //update order
            $query = 'UPDATE #__enmasse_order SET status ="'.$value.'", updated_at = "'. DatetimeWrapper::getDatetimeOfNow() .'" where id ='.$id;
            $db->setQuery( $query );
            $db-> query();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
                
//Increase qty sold if order status changed to "Paid" 
		if(($value == "Paid" || $value=='Full Paid') && $current_status != "Paid")
		{
			$orderItemList = JModel::getInstance('orderitem', 'enmasseModel')->listByOrderId($id);
			foreach($orderItemList as $orderItem)
			{
				JModel::getInstance('deal', 'enmasseModel')->addQtySold($orderItem->pdt_id, $orderItem->qty);
			}
		}		return true;
	}
	
	function updatePayDetail($id, $payDetail)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__enmasse_order SET pay_detail ='".$payDetail."', updated_at = '". DatetimeWrapper::getDatetimeOfNow() ."' where id =".$id;
		$db->setQuery( $query );
		$db->query();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return true;
	}
    function listByBuyerId($buyerId)
	{
		$db = JFactory::getDBO();
	    $query = "	SELECT 
	    				* 
	    			FROM 
	    				#__enmasse_order 
	    			WHERE
	              		buyer_id =$buyerId AND status IN ('Paid', 'Delivered')";
	    $db->setQuery( $query );
	    $orderList = $db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderList;
	}
	
	public function getOrdersByIds($arIds)
	{
		$db = JFactory::getDBO();
	    $query = "	SELECT 
	    				* 
	    			FROM 
	    				#__enmasse_order 
	    			WHERE
	              		id IN (" .implode(', ', $arIds) . ")"
	    				." AND status NOT IN ('".
	    				EnmasseHelper::$ORDER_STATUS_LIST["Delivered"]."','".
	    				EnmasseHelper::$ORDER_STATUS_LIST["Waiting_For_Refund"]."','".
	    				EnmasseHelper::$ORDER_STATUS_LIST["Refunded"]."'".
	    				")";
	    $db->setQuery( $query );
	    $orderList = $db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderList;
	}
	
	public function getOrderByOrderItemId($oiId)
	{
		$db = JFactory::getDBO();
		$query = "SELECT o.*, oi.description as deal_name
					FROM #__enmasse_order o
					LEFT JOIN #__enmasse_order_item oi ON o.id = oi.order_id
					WHERE oi.id = $oiId";
		
		$db->setQuery( $query );
		return $db->loadObject();
	}
	
	// Web service function
	
	public function ws_getOrderHistory($buyerId)
	{
		$db = JFactory::getDBO();
		$query = "SELECT o.*, oi.description as deal_name
					FROM #__enmasse_order o
					LEFT JOIN #__enmasse_order_item oi ON o.id = oi.order_id
					WHERE o.buyer_id = $buyerId";
		
		$db->setQuery($query);
	    $orderList = $db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		
		return $orderList;
	}
	
	
}
?>