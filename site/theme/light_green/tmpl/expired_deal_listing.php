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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');

$oDefault = new JObject();
$oDefault->name = '';
$oDefault->id   = '';
//add an empty select option for location and category list
array_unshift($this->locationList, $oDefault);
array_unshift($this->categoryList, $oDefault);

JFactory::getDocument()->addScript('components/com_enmasse/theme/js/jquery/jquery-1.7.2.min.js');

$dealList = $this->dealList;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="maincol_full_header">
	<h2><?php echo JText::_('DEAL_LIST_EXPIRED_DEAL')?></h2>
	<?php if( !($locationId = JRequest::getVar('locationId')) ):?>
		<div id="rss"><a href="components/com_enmasse/views/rss/listdeal/"><img src="components/com_enmasse/theme/<?php echo $theme?>/images/rss.gif" alt="Expired Deals RSS Feed" title="RSS Feed"/></a></div>
	<?php else :?>
		<div id="rss"><a href="components/com_enmasse/views/rss/location/index.php?locationId=<?php echo $locationId?>"><img src="components/com_enmasse/theme/<?php echo $theme?>/images/rss.gif" alt="Expired Deals RSS Feed" title="RSS Feed"/></a></div>
	<?php endif;?>
</div>
<div class="maincol_full_content">
	<div class="filters">
		<div class="field clearfix">
			<form action="<?php echo JRoute::_('index.php')?>">
				<input type="hidden" name="option" id="option" value="com_enmasse"/>
				<input type="hidden" name="controller" id="controller" value="deal"/>
				<input type="hidden" name="task" id="task" value="expiredlisting"/>
				<table>
					<tr>
						<th><?php echo JText::_('DEAL_SEARCH_NAME');?>:</th>
						<th><?php echo JText::_('DEAL_SEARCH_LOCATION');?>:</th>
						<th><?php echo JText::_('DEAL_SEARCH_CATEGORY');?>:</th>
						<td></td>
					</tr>
					<tr>
						<td><input type="text" name="keyword" size="20" value="<?php echo $this->keyword; ?>"/></td>
						<td><?php echo JHTML::_('select.genericList', $this->locationList, 'locationId', null , 'id', 'name', $this->locationId);?></td>
						<td><?php echo JHTML::_('select.genericList', $this->categoryList, 'categoryId', null , 'id', 'name', $this->categoryId);?></td>
						<td>
							<select name="sortBy">
							<option value=""><?php echo JText::_('DEAL_LIST_OPTION_SORT_BY');?></option>
							<option value="name" <?php if($this->sortBy=="name"){ echo " selected"; }?>><?php echo JText::_('DEAL_LIST_OPTION_NAME');?></option>
							<option value="end_at" <?php if($this->sortBy=="end_at"){ echo " selected"; }?>><?php echo JText::_('DEAL_LIST_OPTION_END_DATE');?></option>
							<option value="price" <?php if($this->sortBy=="price"){ echo " selected"; }?>><?php echo JText::_('DEAL_LIST_OPTION_PRICE');?></option>
							</select>
						</td>
						<td><input type="submit" class="button" value="<?php echo JText::_('DEAL_LIST_SEARCH'); ?>"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<div class="maincol_full_content">
	
	<?php if(!count($dealList)):?>
		<div><h3><?php echo JText::_('DEAL_LIST_NO_DEAL_MESSAGE') ?></h3></div>
	<?php else:?>
		<?php
			$oDeal = array_shift($dealList);
			$nItemId = JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_enmasse&view=dealtoday',true)->id;
			$link = 'index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name ."&Itemid=$nItemId";
            if (!EnmasseHelper::is_urlEncoded($oDeal->pic_dir)) {
                $imageUrl = $oDeal->pic_dir;
            } else {
                $imageUrlArr = unserialize(urldecode($oDeal->pic_dir));
                $imageUrl = str_replace("\\", "/", $imageUrlArr[0]);
            }
            
            
		?>
		<div class="deal">
			<div class="image">
				<div class="inner">
					<a title="" href="<?php echo JRoute::_($link);?>"><img src="<?php echo $imageUrl?>" width="426" alt="<?php echo $oDeal->name; ?>" /></a>
				</div>
			</div>
			<div class="info">
				<div class="title">
					<a href="<?php echo JRoute::_($link);?>"><?php echo $oDeal->name?></a>
				</div>
				<div class="subtitle"><?php echo implode(", ", EnmasseHelper::getDealLocationNames($oDeal->id));?></div>
				<div class="description">
					<?php echo $oDeal->short_desc?>
				</div>
				<div class="timer">
					<span><?php echo JText::_("DEAL_TIME_LEFT")?>: </span>
						<span id="cday">00 <?php echo JText::_("DAY")?></span>
						<span id="chour">00</span>
						<span id="cmin">00</span>
						<span id="csec">00</span>
				</div>
				<div class="line"></div>
				<input name="" type="button" class="button" value="<?php echo JText::_('DEAL_LIST_VIEW_THIS_DEAL')?>" onclick="window.location.href='<?php echo JRoute::_($link)?>'" />
			</div>
		</div>
		<?php foreach ($dealList as $oDeal):?>
			<?php
			$link = 'index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name ."&Itemid=$nItemId";
            if (!EnmasseHelper::is_urlEncoded($oDeal->pic_dir)) {
                $imageUrl = $oDeal->pic_dir;
            } else {
                $imageUrlArr = unserialize(urldecode($oDeal->pic_dir));
                $imageUrl = str_replace("\\", "/", $imageUrlArr[0]);
            }
            $sDealName = $oDeal->name;
            if(strlen($sDealName) > 30)
            {
            	$sDealName = substr($sDealName, 0, 30) ."...";
            }
			?>
			<div class="deal_small">
				<div class="image">
					<div class="inner">
						<a title="" href="<?php echo JRoute::_($link);?>"><img src="<?php echo $imageUrl?>" alt="<?php echo $oDeal->name; ?>" /></a>
					</div>
				</div>
				<div class="info">
					<div class="title">
						<div class="price-tag"></div>
						<a href="<?php echo JRoute::_($link);?>"><?php echo $sDealName?></a>
					</div>
					<div class="subtitle">
						<div class="apollo_info"><?php echo JText::_('DEAL_VALUE'); ?>: <b><?php echo EnmasseHelper::displayCurrency($oDeal->origin_price) ?> </b></div>
						<div class="apollo_info"><?php echo JText::_('DEAL_PRICE'); ?>: <b><?php echo EnmasseHelper::displayCurrency($oDeal->price) ?> </b></div>
	                </div>
	                <div class="apollpo_discount">
	                	<b><?php echo (100 - intval($oDeal->price / $oDeal->origin_price * 100)) ?>%</b>
	                </div>
					<input name="" type="button" class="button" value="<?php echo JText::_('DEAL_LIST_VIEW_THIS_DEAL')?>" onclick="window.location.href='<?php echo JRoute::_($link)?>'" />
	                <div class="line"></div>
	 			</div>
 			</div>
		<?php endforeach;?>
	<?php endif;?>
	
</div>
