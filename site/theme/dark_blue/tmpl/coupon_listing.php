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

$useSecurityCode 	= EnmasseHelper::getSetting()->enable_security_code; 
$invtyList 			= $this->invtyList;
$deal 				= $this->deal;
?>
<div class="deal">
	<div class="main_deal">
		<?php $count = 0;?>
		<h3><?php echo JText::_('COUPON_MESSAGE');?> "<?php echo $deal->name ?>":</h3>
		<table class='coupon_listing'>
		<?php foreach ($invtyList as $invty):?>
			<?php $link = "index.php?option=com_enmasse&controller=coupon&task=generate&invtyName=".$invty->name
			          ."&token=".EnmasseHelper::generateCouponToken($invty->name);
			          ?>
			<tr>
				<td><?php echo JText::_('COUPON');?>: <?php echo $invty->name;?></td>
				
				<td>
					<a href="<?php echo JRoute::_($link) ?>" target="_blank"><?php echo JText::_('COUPON_PRINT_LINK');?></a>
				</td>
				
				<?php if($useSecurityCode){ ?>
				<td>&nbsp;<?php echo JText::_('MERCHANT_LOGIN_COUPON_SECURITY_CODE');?>: <?php echo $invty->security_code;?></td>
				<?php } ?>
			</tr>	
		<?php endforeach;?>
		</table>
	</div>
	<div class="deal_bottom"></div>
</div>