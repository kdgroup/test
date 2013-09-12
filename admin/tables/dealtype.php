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

class TableDealType extends JTable
{
	var $id = null;
	var $name = null;
	var $price = null;
	var $origin_price = null;
	var $deal_id = null;
	var $status = null;
	var $code = null;

	function __construct(&$db)
	{
		parent::__construct( '#__enmasse_deal_type', 'id', $db );
	}

}
?>