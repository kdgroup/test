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

jimport('joomla.application.component.controller');

class EnmasseControllerRss extends JController
{
	function __construct()
	{
		parent::__construct();
	}
  
	function today() 
	{
		JRequest::setVar('view', 'rss');
		parent::display();
	}
  
	function listdeal() 
	{
		JRequest::setVar('view', 'rss');
		parent::display();
	}
  
	function location() 
	{		
		JRequest::setVar('view', 'rss');
		parent::display();
	}  
	function deal() 
	{		

		JRequest::setVar('view', 'rss');
		parent::display();
	}  
        function listexpireddeal()
        {
                JRequest::setVar('view', 'rss');
		parent::display();
        }
  
}
?>