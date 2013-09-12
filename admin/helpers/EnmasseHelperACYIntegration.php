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

require_once JPATH_ADMINISTRATOR . DS ."components". DS . 'com_acymailing' .DS .'helpers' .DS .'/helper.php';;
class EnmasseHelperACYIntegration
{
	public static function getMails()
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM " .acymailing::table('mail')
				. "  WHERE published = 1 AND visible = 1";
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}