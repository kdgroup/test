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

class EnmasseModelDealType extends JModel
{
	function getDealTypeListByIdDeal($idDeal)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
		*
		FROM
		#__enmasse_deal_type
		WHERE
		deal_id= '".$idDeal."'
		ORDER BY id
		";
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		return $result;
	}
	function getDealTypeListByIdDealEnable($idDeal)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
		*
		FROM
		#__enmasse_deal_type
		WHERE
		deal_id= '".$idDeal."' and
		status = '1'
		ORDER BY id
		";
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		return $result;
	}
	//delete all dealtype of a deal
	function delete($dealId)
	{
		$db = JFactory::getDBO();
		$query = "DELETE
		FROM
		#__enmasse_deal_type
		WHERE
		deal_id= '".$dealId."'
		";
		$db->setQuery( $query );

		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		$db->query();
		return true;
	}
	function store($data)
	{
		$row = $this->getTable();
		$data->name = trim($data->name);
		$row->code = $this->getNewDealTypeCode();
		if (!$row->bind($data)) {
			$row->success = false;
			$this->setError($this->_db->getErrorMsg());
			return $row;
		}
		if (!$row->store()) {
			$row->success = false;
			echo $this->setError($this->_db->getErrorMsg());
			return $row;
		}
		$row->success = true;
		return $row;
		
	}
	function getById($id)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT
						* 
					FROM 
						#__enmasse_deal_type
					WHERE
						id = $id " ;
		$db->setQuery( $query );
		return $db->loadObject();
	}
	private function getNewDealTypeCode()
	{
		$text = "AT" .date('ym', time());
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(id)
		FROM #__enmasse_deal_type
		WHERE code LIKE '$text%'";
		$db->setQuery($query);
		$num = $db->loadResult();
		$str = (string)($num + 1);
		if (strlen($str) < 5) {
			$str = str_repeat('0', 5 - strlen($str)).$str;
		}
		return $text.'-'.$str;
	}
}
?>