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

$oItems = $this->cart->getAll();
//$oCartItem = array_pop($oItems);//we just support the cart with one item, so we only need to get the first item in the itemslist of the cart
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="em_borBlock em_shopCart" id="ShoppingCart">
    <form action="index.php" id="changeItem" method="post" name="changeItem" class="form-validate" style="margin: 0px;">
        <div class="em_wrapDealCart row-fluid">
            <table cellpadding="0" cellspacing="0" border="1" class="tableDealList">
                <colgroup>
                    <col span="1" style="width: 50%;">
                    <col span="1" style="width: 15%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 15%;">
                    <col span="1" style="width: 10%;">
                </colgroup>
                <tr>
                    <th >Deal Name</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>&nbsp;</th>
                </tr>
        <?php
         foreach($oItems as $oCartItem){
            $item = $oCartItem->getItem();           
            $deal = JModelLegacy::getInstance('deal','enmasseModel')->getById($item->id);
            $imageUrlArr = array();
            if(EnmasseHelper::is_urlEncoded($deal->pic_dir)){
                    $imageUrlArr = unserialize(urldecode($deal->pic_dir));             
            }else{
                    $imageUrlArr[0] = $deal->pic_dir;
            }
            //tier pricing
            if (($item->tier_pricing)==1){	
                $item->price = EnmasseHelper::getCurrentPriceStep(unserialize($item->price_step),$deal->price,$item->cur_sold_qty);;
                $item->types =NULL;
                $calculatorPrice = EnmasseHelper::getCalculatorPrice(unserialize($item->price_step),$item->price,$item->cur_sold_qty,$oCartItem->getCount(),$deal->price);
                $printOut = "";
                $num_step_price = 0;
                foreach($calculatorPrice as $k => $v)
                {
                        if($k != 'newPrice' && $k != 'totalPrice')
                        {
                            if($num_step_price > 0){
                                $printOut.= "+(".$v."x".$k.")";
                            } else {
                                $printOut.= "(".$v."x".$k.")";
                            }
                            $num_step_price++;
                        }
                }
                $totalPrice = $calculatorPrice['totalPrice'];
            }
        ?>
                 <tr>
                    <td style="max-width: 400px;">
                            <p class="em_img"><img style="height: 100px;width: 157px;" alt="" src="<?php echo JURI::root().str_replace("\\","/",$imageUrlArr[0]);?>" /></p>
                            <p class="txtDeal"><?php echo $item->name; if(($item->tier_pricing)==1) { echo '<br><font style="color:#F34C00;">'.$printOut.'</font>'; } ?></p>
                    </td>
                    <td><span><?php echo  EnmasseHelper::displayCurrency($item->price)?></span></td>
                    <td>

                        <input type="input" class="required validate-numeric" value="<?php echo $oCartItem->getCount();?>" name="value<?php echo $item->id; ?>" id="value" size="10px">
                    </td>
                    <td>
                        <span><strong style="color:#F34C00;"><?php if (($item->tier_pricing)==1){echo EnmasseHelper::displayCurrency($totalPrice);}else{echo EnmasseHelper::displayCurrency($oCartItem->getCount()*$item->price);} ?></strong></span>
                    </td>
                    <td><span title="Delete" class="btnDetele" onclick="javascript:document.getElementById('deleteItem').value=<?php echo  $item->id; ?>;document.changeItem.submit();"><img alt="" src="components/com_enmasse/theme/dark_blue/images/dummy.gif" /></span></td>
                </tr>       
                <?php } ?>
               
            </table>
            <div style="text-align: right;">
                    <input type="hidden" value="0" id="deleteItem" name="deleteItem">
                    <input type="hidden" value="1" name="itemId">
                    <input type="hidden" value="com_enmasse" name="option">
                    <input type="hidden" value="shopping" name="controller">
                    <input type="hidden" value="changeItem" name="task">
                    <input type="hidden" value="0" name="buy4friend">
                    <input style="min-width: 100px; margin: 0px;" type="button" class="em_btn_update" value="<?php echo JText::_('UPDATE_BUTTON');?>" onclick="javascript:document.changeItem.submit();"></input>
            </div>
            <div class="bottom">
                <a href="<?php echo $this->allDealLink; ?>"><input type="button" value="Continue Shopping" class="em_btn_update"></a>
                <dl>
                    <dt class="text"><?php echo JText::_('SHOP_CARD_TOTAL_ITEM');?>: <?php echo $this->cart->getTotalItem();?></dt>
                    <dd class="text"><?php echo JText::_('SHOP_CARD_TOTAL_PRICE');?>: <?php echo EnmasseHelper::displayCurrency($this->cart->getAmountToPay());?></dd> 
                </dl>
            </div>   
            
        </div>
    </form>
</div>

<?php
//$item = $oCartItem->getItem();
//$deal = JModelLegacy::getInstance('deal','enmasseModel')->getById($item->id);
//                $imageUrlArr = array();
//                if(EnmasseHelper::is_urlEncoded($deal->pic_dir)){
//                        $imageUrlArr = unserialize(urldecode($deal->pic_dir));
//                }else{
//                        $imageUrlArr[0] = $deal->pic_dir;
//                }
////tier pricing
//if (($item->tier_pricing)==1)
//{
//	$item->price = EnmasseHelper::getCurrentPriceStep(unserialize($item->price_step),$item->price,$item->cur_sold_qty);
//	$item->types =NULL;
//        $calculatorPrice = EnmasseHelper::getCalculatorPrice(unserialize($item->price_step),$item->price,$item->cur_sold_qty,$oCartItem->getCount(),$deal->price);
//	$printOut = "";
//        $num_step_price = 0;
//	foreach($calculatorPrice as $k => $v)
//	{           
//                 if($k != 'newPrice' && $k != 'totalPrice')
//                                {
//                                    if($num_step_price > 0){
//                                        $printOut.= "+(".$v."x".$k.")";
//                                    } else {
//                                        $printOut.= "(".$v."x".$k.")";
//                                    }
//                                    $num_step_price++;
//                                }
//	}
//}
//$item_price = $item->price;
?>
	