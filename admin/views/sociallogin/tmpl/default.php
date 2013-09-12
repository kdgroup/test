<script src="components/com_enmasse/script/jquery.js"></script>
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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."DatetimeWrapper.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "fbconfig.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "twconfig.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS . "sociallogin" . DS . "config" . DS . "googleconfig.php");

$option = 'com_enmasse';
JHTML::_( 'behavior.modal' );
JHTML::_('behavior.tooltip');

$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
	?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton)
	<?php } else { ?>
		<script language="javascript" type="text/javascript">
			submitbutton = function(pressbutton)
	<?php } ?>
        {
            var form = document.adminForm;
			
            // do field validation
            if (form.fb_app_id.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_FB_APP_ID', true ); ?>" );
            }
            else if (form.fb_secret.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_FB_SECRET', true ); ?>" );
            }
			else if (form.tw_consumer_key.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_TW_CONSUMER_KEY', true ); ?>" );
            }
			else if (form.tw_consumer_secret.value == "")
            {
                alert( "<?php echo JText::_( 'FILL_IN_TW_CONSUMER_SECRET', true ); ?>" );
            }
			else
			{
				submitform( pressbutton );
			}
        }       
        </script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="width-100 fltrt">
		<fieldset class="adminform"><legend><?php echo JText::_('SOCIAL_LOGIN_FACEBOOK')?></legend>
		<table class="admintable" style="width: 100%">
			<tr>
				<td width="150" align="right" class="key">
					<?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_FB_APP_ID'),JTEXT::_('TOOLTIP_FB_APP_ID_TITLE'),'',JTEXT::_('TOOLTIP_FB_APP_ID_NAME'). ' *');?>
				</td>
				<td>
					<input class="text_area" type="text" name="fb_app_id" id="fb_app_id" size="50" maxlength="50" value="<?php echo htmlentities(FB_APP_ID, ENT_QUOTES,"UTF-8");?>" />
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					<?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_FB_SECRET'),JTEXT::_('TOOLTIP_FB_SECRET_TITLE'),'',JTEXT::_('TOOLTIP_FB_SECRET_NAME'). ' *');?>
				</td>
				<td>
					<input class="text_area" type="text" name="fb_secret" id="fb_secret" size="50" maxlength="50" value="<?php echo htmlentities(FB_SECRET, ENT_QUOTES,"UTF-8");?>" />
				</td>
			</tr>
		</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_('SOCIAL_LOGIN_TWITTER')?></legend>
		<table class="admintable" style="width: 100%">
			<tr>
				<td width="150" align="right" class="key">
					<?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_TW_CONSUMER_KEY'),JTEXT::_('TOOLTIP_TW_CONSUMER_KEY_TITLE'),'',JTEXT::_('TOOLTIP_TW_CONSUMER_KEY_NAME'). ' *');?>
				</td>
				<td>
					<input class="text_area" type="text" name="tw_consumer_key" id="tw_consumer_key" size="50" maxlength="50" value="<?php echo htmlentities(TW_CONSUMER_KEY, ENT_QUOTES,"UTF-8");?>" />
				</td>
			</tr>
			<tr>
				<td align="right" class="key">
					<?php echo JHTML::tooltip(JTEXT::_('TOOLTIP_TW_CONSUMER_SECRET'),JTEXT::_('TOOLTIP_TW_CONSUMER_SECRET_TITLE'),'',JTEXT::_('TOOLTIP_TW_CONSUMER_SECRET_NAME'). ' *');?>
				</td>
				<td>
					<input class="text_area" type="text" name="tw_consumer_secret" id="tw_consumer_secret" size="50" maxlength="50" value="<?php echo htmlentities(TW_CONSUMER_SECRET, ENT_QUOTES,"UTF-8");?>" />
				</td>
			</tr>
		</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_('SOCIAL_LOGIN_GOOGLE')?></legend>
			It doesn't need to config.
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="controller" value="sociallogin" />
		<input type="hidden" name="task" value="" />
	</div>
</form>