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
JHTML::_('behavior.mootools');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<style>
    ul li{
        list-style: none;
        display: inline;
        vertical-align: top;
    }
    
    a {
        text-decoration: none;
    }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_enmasse&controller=uploader&task=saveImg'); ?>" method="post" name="adminForm" class="form-validate" enctype="multipart/form-data" >
    <div class="width-100 fltlft">
        <fieldset>
            <legend><?php echo JText::_('UPLOAD_FILE_TITLE'); ?></legend>
            
            <ul id="prod_upload_image">
                
                <li style="width:100%;margin-top:10px;display:block;">
                        <input type="file" name="image[]" class="inputbox">
                        <a href="javascript:;" onclick="deleteImageUpload(this);"><?php echo JText::_('DELETE_INFUT_FILE_LABEL');?></a>
                </li>
                
                <li class="cloneObj" style="width:100%;display:none;margin-top:10px;">
                        <input type="file"  name="image[]" class="inputbox">
                        <a href="javascript:;" onclick="deleteImageUpload(this);"><?php echo JText::_('DELETE_INFUT_FILE_LABEL');?></a>
                </li>
            </ul>
            <div class="clr"></div>
            <table style="font-size: 1.2em;" cellpadding="5">
                <tr>
                    <td><?php echo JText::_('CLONE_INPUT_TYPE_LABEL'); ?></td>
                    <td><a style="margin-left: 15px;" href="javascript:;" onclick="addMorImageUpload('prod_upload_image')"><?php echo JText::_('ADD_MORE_INPUT_FILE_LABEL');?></a></td>
                </tr>
            </table>
        </fieldset>
    </div>
    
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('SLIDE_IMAGES_TITLE'); ?></legend>         
            <ul class="deleteImage">
                <?php 
                    if (!EnmasseHelper::is_urlEncoded($this->list_images)) {
				$imageUrlArr = '';
			} else {
				$imageUrlArr = unserialize(urldecode($this->list_images));
                        }
                ?>
                <?php 
                    foreach ($imageUrlArr as $i => $imageUrl){ 
                    $imageUrl = str_replace("\\", "/", $imageUrl);
                ?>
                    <li style="padding: 0;">
                        <img width ="80" height="80" src="<?php echo JURI::root(); ?><?php echo $imageUrl; ?>"  /> 
                        <img src="<?php echo JURI::root(); ?>components/com_enmasse/theme/dark_blue/images/remove.png" onclick="deleteImage(this,'<?php echo $i; ?>');">
                    </li>
                <?php } ?>
            </ul>
        </fieldset>        
    </div>
    <div style="float:right;">
        <input type="submit" name="upload_image" value="<?php echo JText::_('SUBMIT_UPLOAD_FILE_LABEL');?>" onclick="return submitForm();" style="margin-right: 10px; height: 25px; width: 100px;"/>
    </div>
</form>

<script>
    function addMorImageUpload(contId){
	var cont = $(contId);
	if(!cont) return;
	
	var cloneObj = cont.getElement('.cloneObj');
	var clone = cloneObj.clone().setStyle('display','block');
	clone.getFirst().value = '';
	clone.inject(cont.getLast(), 'before');
    }
    
    function deleteImageUpload(btn){
            $(btn).getParent().dispose();
    }
    
    function deleteImage(file,index_img){
        var frm = $('adminForm');
        new Request.HTML({
            url:'index.php?option=com_enmasse&controller=uploader&task=ajaxRemoveImg',
            data:{
                'index_img':index_img
            },
            onSuccess:function(){
                $(file).getParent().dispose();
            }
        }).post(frm);
    }
    
    function submitForm()
    {
        var invalid = false;
        var fileList = $$(".inputbox");
        for(var i = 0; i < fileList.length - 1; i++) {
            var img = fileList[i].value;
            img = img.split('.');
            var ext = img[img.length - 1];
            if(ext.toLowerCase() == 'png' || ext.toLowerCase() == 'jpg' || ext.toLowerCase() == 'jpeg'){
                invalid = false;
            } else {
                invalid = true;
                break;
            }
        }
        
        if(invalid == true)
        {
            alert("Invalid extension!");
            return false;
        }
        else
        {
            return true;
        }    
    }
</script>