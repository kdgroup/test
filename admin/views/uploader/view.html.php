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

class EnmasseViewUploader extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title( JText::_( 'En Masse Uploader'),
                                           'generic.png' );
		$filePath = JRequest::getVar('folder');
        
		$parentId = JRequest::getVar('parentId');
		$parent = JRequest::getVar('parent');
		$couponBg = JRequest::getVar('couponbg');
		
		$this->assignRef('couponbg', $couponBg );
		$this->assignRef('parentId', $parentId );
		$this->assignRef('parent', $parent );
		if(!empty($filePath))
		{
			$this->assignRef('imageUrl', $filePath );
		}
                
		if(JRequest::getVar('task') == 'displayall'){
                    $oSession = JFactory::getSession();
                    $list_images = $oSession->get('deal_list_images');
                    $this->list_images = $list_images;
                }
		parent::display($tpl);
	}

}
?>