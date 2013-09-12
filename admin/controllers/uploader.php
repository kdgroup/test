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

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_media'.DS.'helpers'.DS.'media.php');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerUploader extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'uploader');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	function getExtension($str) {

         $i = strrpos($str,".");
         if (!$i) { return ""; } 
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 	}
        
    function upload()
	{
		global $mainframe;
        
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        if(substr($joomla,0,3) >= '1.6'){
    	   $mainframe = JFactory::getApplication();
        }
        
		$fileArr    = JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$folder		= JRequest::getVar( 'folder', '', '', 'path' );
		$format		= JRequest::getVar( 'format', 'html', '', 'cmd');
		$return		= JRequest::getVar( 'return-url', null, 'post', 'base64' );
		$parentId   = JRequest::getVar('parentId');
		$parent     = JRequest::getVar('parent');
		$couponbg   = JRequest::getVar('couponbg');
		$err		= null;
		//------------------------------
		// to get the image size from seeting table
		
		$dealImageSize = EnmasseHelper::getDealImageSize();
		
		if(!empty($dealImageSize) && $dealImageSize->image_height!=0 && $dealImageSize->image_width!=0)
		{
			$image_height = $dealImageSize->image_height;
			$image_width = $dealImageSize->image_width;
		}
		else
		{
			$image_height = 252 ;
			$image_width = 400;
		}
		
		if($couponbg!='' && $couponbg!=null)
		{
			$image_height = 424 ;
			$image_width = 680;
		}
		
		for($i=0 ; $i<count($fileArr['name']); $i++)
		{
			$file[$i]['name'] = $fileArr['name'][$i];
			$file[$i]['type'] = $fileArr['type'][$i];
			$file[$i]['tmp_name'] = $fileArr['tmp_name'][$i];
			$file[$i]['error'] = $fileArr['error'][$i];
			$file[$i]['size'] = $fileArr['size'][$i];
		}
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$random = rand();
		for ($count=0 ; $count < count($file); $count++)
		{
			
			$file[$count]['name']	= JFile::makeSafe($file[$count]['name']);
	        
			if (isset($file[$count]['name'])) 
			{
				// Modifine by phuocndt - update image upload code
				$filepath = JPath::clean(JPATH_SITE.DS.'components'.DS.'com_enmasse'.DS.'upload'.DS.strtolower($random.$file[$count]['name']));
	            $imagepath = JPath::clean('components'.DS.'com_enmasse'.DS.'upload'.DS.strtolower($random.$file[$count]['name']));
                //$imagepath = $random.$file[$count]['name'];
                $imagePathArr[$count] = $imagepath;
	            if (!MediaHelper::canUpload( $file[$count], $err )) 
	            {
					if ($format == 'json') 
					{
						jimport('joomla.error.log');
						$log = &JLog::getInstance('upload.error.php');
						$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
						header('HTTP/1.0 415 Unsupported Media Type');
						jexit('Error. Unsupported Media Type!');
					}
					else
					 {
						JError::raiseNotice(100, JText::_($err));
						// REDIRECT
						if ($return)
						{
							$mainframe->redirect(base64_decode($return).'&folder='.$folder);
						}
						return;
					}
				}
	
				if (JFile::exists($filepath)) 
				{
					if ($format == 'json') 
					{
						jimport('joomla.error.log');
						$log = &JLog::getInstance('upload.error.php');
						$log->addEntry(array('comment' => 'File already exists: '.$filepath));
						header('HTTP/1.0 409 Conflict');
						jexit('Error. File already exists');
					} 
					else 
					{
						JError::raiseNotice(100, JText::_('Error. File already exists'));
						// REDIRECT
						if ($return) 
						{
							$mainframe->redirect(base64_decode($return).'&folder='.$folder);
						}
						return;
					}
				}
				
				    $image =$file[$count]["name"];
 					$uploadedfile = $file[$count]['tmp_name'];
	                $filename = stripslashes($file[$count]['name']);
  		            $extension =$this->getExtension($filename);
 		            $extension = strtolower($extension);
 		            $size=filesize($file[$count]['tmp_name']);
 		            
					if($extension=="jpg" || $extension=="jpeg" )
					{
					$uploadedfile = $file[$count]['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
					}
					else if($extension=="png")
					{
					$uploadedfile = $file[$count]['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
					
					}
					
					list($width,$height)=getimagesize($uploadedfile);
					$newwidth=60;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
		
		            if($parent == 'merchant')
		            {
		            	$newwidth1=$width;
						$newheight1=$height;
		            }
		            else
		            {
						$newwidth1=$image_width;
						$newheight1=$image_height;
		            }
                    
                    if($couponbg!='' && $couponbg!=null)
                    {
						$newwidth1=$width;
						$newheight1=$height;
                    }
                    
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					
					imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
					$filename = $filepath;
			
					$filename1 = $filepath;
		
		
					imagejpeg($tmp,$filename,100);
					
					imagejpeg($tmp1,$filename1,100);
					
					imagedestroy($src);
					imagedestroy($tmp);
					imagedestroy($tmp1);
					
			        if ($count == count($file)-1) 
				    {
                       
                            $mainframe->redirect(base64_decode($return).'&folder='.urlencode(serialize($imagePathArr)).'&parentId='.$parentId);
                            
                      
					}
			
			} 
			else 
			{
				$mainframe->redirect('index.php', 'Invalid Request', 'error');
			}
		}
		
		//$mainframe->redirect(base64_decode($return).'&folder='.$imagepath.'&parentId='.$parentId);
	}
	

        
        function displayall($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'uploader');
		JRequest::setVar('layout', 'showall');
		parent::display();
	}
        
        function saveImg()
        {
            $oSession = JFactory::getSession();
            $list_images = $oSession->get('deal_list_images');
            $list_images_new = $this->mulUploadImg();
            $all_images = $list_images_new;
            
            if($list_images){
                $all_images = $this->mergeListImg($list_images,$list_images_new);
            }
            
            echo "<script type='text/javascript'>
                    window.parent.addImg('".$all_images."');
                    window.parent.SqueezeBox.close();
                </script>";
            
            $oSession->set('deal_list_images',$all_images);
            exit();
        }
        
        function mergeListImg($list1,$list2)
        {
            if(!$list1){
                return $list2;
            }
            if(!$list2){
                return $list1;
            }
            
            $list1 = unserialize(urldecode($list1));
            $list2 = unserialize(urldecode($list2));
            
            $list = array_merge($list1,$list2);
            
            return urlencode(serialize($list));
        }
        
        function mulUploadImg() {
            //require_once( JPATH_SITE . DS . "components" . DS . "com_media" . DS . "helpers" . DS . "media.php");
            
            $file = JRequest::getVar('image', '', 'files', 'array');
            $listFileNames = array();
            
            for($i=0; $i < count($file['name']); $i++){
                if($file['error'][$i] == 0){
                    //set name
                    $file['name'][$i] = JFile::makeSafe($file['name'][$i]);

                    $fileName = strtolower('I'.date('YmdHis').$file['name'][$i]);
                    
                    $strFileName = 'components' . DS . 'com_enmasse' . DS . 'upload' . DS . $fileName;

                    $filepath = JPath::clean(JPATH_SITE . DS . $strFileName);

                    //set new image dimension
                    $dealImageSize = EnmasseHelper::getDealImageSize();
		
                    if(!empty($dealImageSize) && $dealImageSize->image_height!=0 && $dealImageSize->image_width!=0)
                    {
                            $image_height = $dealImageSize->image_height;
                            $image_width = $dealImageSize->image_width;
                    }
                    else
                    {
                            $image_height = 252 ;
                            $image_width = 400;
                    }
                    
                    $uploadedfile = $file['tmp_name'][$i];
                    $extension = $this->getExtension(stripslashes($file['name'][$i]));
                    $extension = strtolower($extension);
                    $size = filesize($file['tmp_name'][$i]);
 		            
                    $uploadedfile = $file['tmp_name'][$i];
                    if($extension == "jpg" || $extension == "jpeg" )
                    {
                        $src = imagecreatefromjpeg($uploadedfile);
                    }
                    else if($extension=="png")
                    {
                        $src = imagecreatefrompng($uploadedfile);
                    }

                    list($width,$height)=getimagesize($uploadedfile);
                    $tmp = imagecreatetruecolor($image_width,$image_height);
                        
                    imagecopyresampled($tmp,$src,0,0,0,0,$image_width,$image_height,$width,$height);
					
                    imagejpeg($tmp,$filepath,100);
		
                    imagedestroy($src);
                    imagedestroy($tmp);
                    
                    $fileName = JPath::clean('components'.DS.'com_enmasse'.DS.'upload'.DS.strtolower($fileName));
                    $listFileNames[] = $fileName;
                }
            }
            if($listFileNames){
                return urlencode(serialize($listFileNames));
            }else{
                return '';
            }
        }
        
        public function ajaxRemoveImg(){
            $oSession = JFactory::getSession();
            $imageUrlArr = $oSession->get('deal_list_images');
            $imageUrlArr = unserialize(urldecode($imageUrlArr));
            $index_img = JRequest::getVar('index_img');
            
            //remove img
            foreach($imageUrlArr as $i => $image)
            {
                if($index_img == $i)
                {
                    unset($imageUrlArr[$i]);
                }
            }
            $imageUrlArr = array_values($imageUrlArr);
            if($imageUrlArr){
                $imageUrlArr = urlencode(serialize($imageUrlArr));
            }else{
                $imageUrlArr = '';
            }
            
            //set clinical notes
            $oSession->set('deal_list_images',$imageUrlArr);
            
            echo "<script type='text/javascript'>
                    window.parent.addImg('".$imageUrlArr."');
                </script>";
            exit();
        }
}
?>