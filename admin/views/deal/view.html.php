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

jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."toolbar.enmasse.html.php");

class EnmasseViewDeal extends JViewLegacy
{
	function display($tpl = null)
	{
		$task = JRequest::getWord('task');
					
		if($task == 'edit' || $task == 'add')
		{
			$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );

			$row = JModelLegacy::getInstance('deal','enmasseModel')->getById($cid[0]);
			if(!empty($row->id))
			{
				TOOLBAR_enmasse::_DEAL_DETAIL($row->status);
			}
			else
			{
				TOOLBAR_enmasse::_DEAL_NEW();
			}
			$this->deal = $row ;
                        $oSession = JFactory::getSession();
                        $oSession->set('deal_list_images',$row->pic_dir);
                        
			$this->currencyPrefix = JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix();
			$this->currencyPostfix = JModel::getInstance('setting','enmasseModel')->getCurrencyPostfix();
			$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
			$this->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
			$this->categoryList = JModel::getInstance('category','enmasseModel')->listAllPublished();
			$this->salesPersonList = JModel::getInstance('salesPerson','enmasseModel')->listAllPublished();
			$this->merchantList = JModel::getInstance('merchant','enmasseModel')->listAllPublished();	
			if($row->id)
			{
								
				$dealCategoryIdList = JModel::getInstance('dealcategory','enmasseModel')->getCategoryByDealId($row->id);
				//------------------------------
				// get category list of deal
				if(count($dealCategoryIdList)!=0)
					$dealCategoryList = JModel::getInstance('category','enmasseModel')->getCategoryListInArrId($dealCategoryIdList);
				else
				    $dealCategoryList = array();
				 // assign category list to template  
			    $this->assignRef('dealCategoryList',$dealCategoryList);
			    
			    $dealLocationIdList = JModel::getInstance('deallocation','enmasseModel')->getLocationByDealId($row->id);
			    //------------------------------
				// get location list of deal
			    if(count($dealLocationIdList)!=0)
			    	$dealLocationList = JModel::getInstance('location','enmasseModel')->getLocationListInArrId($dealLocationIdList);
			    else 
			       $dealLocationList = array();
			    $this->assignRef('dealLocationList',$dealLocationList);
			    
			    // Set a flag for the template to recognize we are creating a new deal
                $this->bNewDeal = false;   
				
				//get type list of deal
				$dealTypeList = JModel::getInstance('dealtype','enmasseModel')->getDealTypeListByIdDeal($row->id);
				$this->assignRef('dealTypeList',$dealTypeList);
			}
			else
			{
				$dealCategoryList = array();
				$this->assignRef('dealCategoryList',$dealCategoryList);
				$dealLocationList = array();
				$this->assignRef('dealLocationList',$dealLocationList);
				$this->bNewDeal = true;
			}
                                             
		}
		else //Task == Show
		{
                    $oSession = JFactory::getSession();
                    $oSession->set('deal_list_images','');
                    
			TOOLBAR_enmasse::_SMENU();
			$nNumberOfDeals = JModel::getInstance('deal','enmasseModel')->countAll();
			if($nNumberOfDeals==0)
			{
				TOOLBAR_enmasse::_DEAL_EMPTY();
			}
			else
			{
				TOOLBAR_enmasse::_DEAL();
			}
			
            $filter 	= JRequest::getVar('filter');
            $filter['code'] = isset($filter['code']) ? $filter['code'] : '';
            $filter['location_id'] = isset($filter['location_id']) ? $filter['location_id'] : '';
            $filter['category_id'] = isset($filter['category_id']) ? $filter['category_id'] : '';
            $filter['name'] = isset($filter['name']) ? $filter['name'] : '';
            $filter['published'] = isset($filter['published']) ? $filter['published'] : '';
            $filter['status'] = isset($filter['status']) ? $filter['status'] : '';
            
			$dealFromLocation = null;
			$dealFromCategory = null;

			if(!empty($filter['location_id']) && trim($filter['location_id'])!= '')
			 	$dealFromLocation = JModel::getInstance('dealLocation','enmasseModel')->getDealByLocationId($filter['location_id']);
			if(!empty($filter['category_id']) && trim($filter['category_id'])!= '')
                        {
                            //get children category
                            $listChildren = JModelLegacy::getInstance('dealCategory','enmasseModel')->getChildrenCategory($filter['category_id']);
                            if($listChildren){
                                $filter['category_id'] = $filter['category_id'].','.implode(',', $listChildren);
                            }
                            //get all deal belong selected category
                            $dealFromCategory = JModelLegacy::getInstance('dealCategory','enmasseModel')->getDealByListCategoryId($filter['category_id']);
                        }
                        
			$dealList 		= JModel::getInstance('deal','enmasseModel')->search($filter['code'], $filter['name'],$dealFromLocation,$dealFromCategory,$filter['published'], $filter['status']);
			/// load pagination
			$pagination =JModel::getInstance('deal','enmasseModel')->getPagination($filter['code'], $filter['name'],$dealFromLocation,$dealFromCategory,$filter['published'], $filter['status']);
			$state = $this->get( 'state' );
			// get order values
			$order['order_dir'] = $state->get( 'filter_order_dir' );
            $order['order']     = $state->get( 'filter_order' );
			for($i=0; $i < count($dealList); $i++)
			{
				$dealCategoryIdList = JModel::getInstance('dealcategory','enmasseModel')->getCategoryByDealId($dealList[$i]->id);
				$dealLocationIdList = JModel::getInstance('deallocation','enmasseModel')->getLocationByDealId($dealList[$i]->id);
				
				//----------------------------------------------
				// get list of category name
				if(count($dealCategoryIdList)!=0)
					$categoryList = JModel::getInstance('category','enmasseModel')->getCategoryListInArrId($dealCategoryIdList);
				else
				   $categoryList = null;

				 //----------------------------------------------
				// get list of location name
				if(count($dealLocationIdList)!=0)
			    	$locationList = JModel::getInstance('location','enmasseModel')->getLocationListInArrId($dealLocationIdList);
				else
				   $locationList = null;
				   
				   
				if(count($locationList)!=0 && $locationList!=null)
					$dealList[$i]->location_name 		= $locationList;
				else 
					$dealList[$i]->location_name 		= null;
					
				if(count($categoryList)!=0 && $categoryList!=null)
					$dealList[$i]->category_name 		= $categoryList;
				else
				    $dealList[$i]->category_name 		= null;
				$dealList[$i]->sales_person_name 	= JModel::getInstance('salesPerson','enmasseModel')->retrieveName($dealList[$i]->sales_person_id);
			}

			$this->assignRef( 'filter', $filter);
			$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
			$this->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
			$this->categoryList = JModel::getInstance('category','enmasseModel')->listAllPublished();
			$this->assignRef( 'dealList', $dealList );
			$this->assignRef('pagination', $pagination);
			$this->assignRef( 'order', $order );
		}
		
		parent::display($tpl);
	}

}
?>