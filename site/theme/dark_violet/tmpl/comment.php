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


require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();
$oDeal = $this->objDeal;
$aComments = $this->aComments;
?>
<script type="text/javascript" src="components/com_enmasse/theme/js/rating.js"></script>
<div class="deal">	
	<div class="main_deal">
		<div class="deal_left">
			<div id="price_tag">
				<div id="price_tag_cont">
					<div id="amount"><?php echo EnmasseHelper::displayCurrency($oDeal->price)?></div>
					<?php if(strtotime($oDeal->end_at) + 86400 < time()):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_EXPIRED')?></div>
					<?php elseif(strtotime($oDeal->start_at) > time()):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_UPCOMING')?></div>
					<?php elseif ($oDeal->status == 'Voided'):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_VOIDED')?></div>
					<?php elseif($oDeal->max_coupon_qty != "-1" && $oDeal->cur_sold_qty >= $oDeal->max_coupon_qty):?>
						<div class="buy"><?php echo JText::_('BUY_DEAL_SOLD_OUT')?></div>
					<?php else :?>
                    	<a href="index.php?option=com_enmasse&controller=shopping&task=addToCart&dealId=<?php echo $oDeal->id .'&slug_name=' .$oDeal->slug_name;?>" class="buy"><?php echo JText::_('Buy')?></a>
                    <?php endif;?> 				
                </div>
			</div>
        </div>
        <div class="deal_right">
            <h2 class="comment_deal_name"><?php echo $oDeal->name; ?></h2>
            <a href="<?php echo JRoute::_('index.php?option=com_enmasse&controller=deal&task=view&id=' . $oDeal->id ."&slug_name=" .$oDeal->slug_name); ?>"><?php echo JText::_('RETURN_TO_DEAL'); ?></a>
        </div>
    </div>
	<div class="deal_bottom">
	</div>
</div>
<div class="deal">	
	<div class="main_deal">
        <?php foreach($aComments as $aComment): ?>
        <?php
        $user = JFactory::getUser($aComment['user_id']);
        ?>
        <div class="comment_area">
            <p class="comment_content"><?php echo nl2br($aComment['comment']); ?></p>
            <p class="comment_details">
                <span class="rating disabled">
                    <?php for($i=1; $i<=$aComment['rating']; $i++): ?>
                        <span class="ratingStar filled">&nbsp;</span>
                    <?php endfor;?>
                    <?php for($i=$aComment['rating']; $i<5; $i++): ?>
                        <span class="ratingStar">&nbsp;</span>
                    <?php endfor;?>                            
                </span>                    
                <br />
                <span class="author"><?php echo $user->name; ?></span>
                <span>-</span>
                <span class="timestamp"><?php echo $aComment['created_at']; ?></span>
            </p>
        </div>
        <?php endforeach; ?>
        <?php if(JFactory::getUser()->get("guest")):
            $sRedirectUrl = base64_encode('index.php?option=com_enmasse&controller=deal&task=comment&id=' . $oDeal->id);
			$sLoginLink = JRoute::_("index.php?option=com_users&view=login&return=" . $sRedirectUrl, false);
        ?>
        <a class="sign_in_to_review" href="<?php echo $sLoginLink; ?>"><?php echo JText::_('SIGN_IN_TO_REVIEW'); ?></a> 
        <?php else: ?>
        <div class="post_review">
            <h3><?php echo JText::_('POST_REVIEW_TITLE'); ?></h3>
            <p class="rate_this_deal">
                <?php echo JText::_('RATE_THIS_DEAL'); ?>
                <a class="rating" href="#">
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                    <span class="ratingStar">&nbsp;</span>
                </a>               
            </p>              
            <form id="review" name="review" method="post" action="index.php">
                <input type="hidden" name="option" value="com_enmasse" />
                <input type="hidden" name="controller" value="comment" />
                <input type="hidden" name="task" value="submit_review" />
                <input type="hidden" name="nDealId" value="<?php echo $oDeal->id; ?>" />
                <input type="hidden" id="nRating" name="nRating" value="" />
                <div class="review_form">
                    <textarea rows="10" id="sReviewBody" name="sReviewBody" cols="70"></textarea>
                </div>
                <br />
                <div id="review_errors" class="review_errors"></div>
                <br />
                <input type="button" class="button" onclick="submit_form();" value="<?php echo JText::_('POST_REVIEW_BUTTON');?>"></input>
            </form>
        </div>
        <?php endif; ?>
    </div>
	<div class="deal_bottom">
	</div>
</div>
<script type="text/javascript">
function submit_form()
{
    var form = document.review;
    if(form.nRating.value == '' || (form.nRating.value <= 0 && form.nRating.value > 5))
    {
        document.getElementById("review_errors").innerHTML = "<?php echo JText::_('PLEASE_RATE'); ?>";
        return false;
    }
    if(form.sReviewBody.value == '')
    {
        document.getElementById("review_errors").innerHTML = "<?php echo JText::_('PLEASE_ENTER_REVIEW'); ?>";
        return false;
    }
    form.submit();
}
</script>