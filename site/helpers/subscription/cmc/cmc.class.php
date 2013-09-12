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
class cmc
{
	function getViewData($params)
	{
		$data->module = EnmasseHelper::getModuleById($params->module_id);
		$data->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
		return $data;
	}
	function addMenu() {
		return true;
	}
	function updateSubscriptionList($location, $email) {
		return true;
	}
	function integration($data,$key) {
		return true;
	}
    function insertNewLetter($data)	{
		return true;
	}
	function seoUrl($string) {
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower($string);
		//Strip any unwanted characters
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "_", $string);
		return $string;
	}
	function insertEnmasseLocation($data) {
		return true;
	}
}
?>