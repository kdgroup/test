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

//load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= 1.6){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
     $language->load($extension, $base_dir, 'en-GB', true);
}

$returnURL = $this->returnURL;
$twitterId = $this->twitterId;;
$name = $this->name;

?>
<style>
	.error{color: red;}
</style>
<div class="deal">	
	<div class="main_deal">
		<h2><?php echo JText::_('TWITTER_REQUEST_EMAIL');?></h2>
		<form id='twitterLogin' name='twitterLogin' method='post' action="<?php echo JRoute::_('index.php?option=com_enmasse&controller=authentication&task=twitter'); ?>">
			<input type="hidden" name="return" value="<?php echo $returnURL; ?>" />
			<input type="hidden" name="twitterId" value="<?php echo $twitterId; ?>" />
			<input type="hidden" name="name" value="<?php echo $name; ?>" />
			<label id="lblemail" for="txtemail" title=""><?php echo JText::_("SOCIAL_LOGIN_EMAIL")?></label>
			<input type="text" name="txtemail" class="textbox required email" value="" />
			<input type="submit" name="Submit" value="<?php echo JText::_("SOCIAL_LOGIN_CONTINUE")?>" />
			<input type="hidden" name="option" value="com_enmasse" />
			<input type="hidden" name="controller" value="authentication" />
			<input type="hidden" name="task" value="twitter" />
			<?php echo JHtml::_('form.token');?>
		</form>
	</div>
	<div class="deal_bottom" />
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
			jQuery("#twitterLogin").validate({
				errorElement: "span",
				rules: {

				}
			});
		});
</script>
