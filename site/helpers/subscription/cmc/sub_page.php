<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 09.07.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.module.helper' );
$module = JModuleHelper::getModule('cmc');
$params = new JRegistry;
$params->loadString($module->params, 'JSON');
$params->def(PARAM_NAME);
require_once(JPATH_ROOT . '/modules/mod_cmc/library/form/form.php');
$moduleId = $module->id;
JHtml::_('behavior.mootools', true);
JHtml::script(JURI::root() . '/media/mod_cmc/js/cmc.js');

// create list location for combobox
$locationJOptList = array();
$emptyJOpt = JHTML::_('select.option', '', JText::_('SUBSCR_CHOOSE_ONCE_LOCATION'));
array_push($locationJOptList, $emptyJOpt);

foreach ($data->locationList as $item) {
    $var = JHTML::_('select.option', $item->id, $item->name);
    array_push($locationJOptList, $var);
}
?>
<div id="cmc-signup-<?php echo $moduleId;?>">
   
        <?php
      		 if (!empty($data->module)) {
            echo JModuleHelper::renderModule($data->module);
        }
		echo JHTML::_('select.genericList', $locationJOptList, 'locationId', 'onchange="on_change(this);"', 'value', 'text', ''); 
        ?>
</div>