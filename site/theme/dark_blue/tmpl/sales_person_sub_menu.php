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

?>
<table class="noBorder" id="top">
	<tr>
		<td><a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=dealShow')?>"><?php echo JTEXT::_('SALE_LIST_DEAL');?></a>
		&nbsp; | &nbsp;
		<a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=dealAdd')?>"><?php echo JTEXT::_('SALE_ADD_DEAL');?></a>
		&nbsp; | &nbsp;
		<a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantList')?>"><?php echo JTEXT::_('SALE_MERCHANT_LIST');?></a>
		&nbsp; | &nbsp;
		<a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit')?>"><?php echo JTEXT::_('SALE_MERCHANT_ADD');?></a>
                </td>
	</tr>
</table>
