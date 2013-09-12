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

function getLocation(){
		$db = JFactory::getDBO();
    	$query = "SELECT
    					distinct loc.id, loc.name
    	          FROM 
    	               `#__enmasse_location` loc 
    	          WHERE 
    	               loc.published = 1";
    	$db->setQuery($query);
    	//$names = $db->loadResultArray();
    	$names = $db->loadObjectList();
    	//print_r($names);
    	return $names;
}