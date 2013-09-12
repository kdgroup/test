<?php
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

$categoryList = $this->categoryList;
?>

<section id="em_main" class="container">
    <div class="row-fluid">
        <section id="em_content" class="span9" style="width: 100%">
            <div class="em_dealMain em_corBlock">
                <h3>Please Select <span>a Category:</span></h3>
                <div class="em_innerBlock em_productPage">
                    <div class="em_wrapProList row-fluid">
                        <ul>
                        <?php
                            foreach($categoryList as $i => $category){ 
                        ?>

                            <li class="span4">
                                <dl>
                                    <dt><a href="<?php echo JRoute::_('index.php?option=com_enmasse&view=deallisting&categoryId='.$category->id); ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a></dt>
                                    <dd><?php echo $category->description ?></dd>
                                </dl>
                                <?php if($category->listChildrenCat) { ?>
                                <ul class="em_listProItem">
                                    <?php foreach($category->listChildrenCat as $item) { ?>
                                        <li><a href="<?php echo JRoute::_('index.php?option=com_enmasse&view=deallisting&categoryId='.$item->id); ?>" title="<?php echo $item->name; ?>"><?php echo $item->name; ?></a></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </li>
                            <?php if(($i+1)%3 == 0) {
                                   echo '</ul><ul>'; 
                                }
                            ?>
                        <?php } ?>
                        </ul>
                    </div><!--wrapProList-->
                </div><!--em_innerBlock-->
            </div><!--em_dealMain-->
        </section><!--content-->
    </div><!--main-->
</section>