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
 
class EnmasseViewHelp extends JView
{
    function display($tpl = null)
    { 
        TOOLBAR_enmasse::_SMENU();
    	TOOLBAR_enmasse::_EHELP();
        //$this->addToolBar();
        
        parent::display($tpl);
    }
    /*
    protected function addToolBar() 
	{
		JToolBarHelper::title( JText::_( 'T_HELP' ),
                                           'generic.png' );
        JToolBarHelper::custom( 'help.control', 'back.png', 'back.png', 'T_MAIN', false, false );		
	}*/
}
?>