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


class CartItem
{
	var $count = 0;
	var $item = null;

	function __construct(stdClass $item)
	{
		$this->count = 1;
		$this->item = $item;
	}

	function getCount()
	{
		return $this->count;
	}

	function getItem()
	{
		return $this->item;
	}

	function addItem(stdClass $item)
	{
		$this->count ++;
	}

	function changeValueItem($value)
	{
		if ( $value > 0 )
		$this->count = $value;
		else
		$this->count = 0;
	}
	function changePriceItem($newPrice)
	{
		$this->item->price = $newPrice;
	}
	function changeTypeItem($types,$type)
	{
		$this->item->types = $types;
		$this->item->type = $type;
	}
	function getTotalPrice()
	{
		return $this->item->price * $this->count;
	}
}