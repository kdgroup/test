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

require_once 'PEAR.php';

/**
 * Image_Barcode class
 *
 * Package which provides a method to create barcode using GD library.
 *
 * @category   Image
 * @package    Image_Barcode
 * @author     Marcelo Subtil Marcal <msmarcal@php.net>
 * @copyright  2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Image_Barcode
 */
class Image_Barcode extends PEAR
{
    /**
     * Draws a image barcode
     *
     * @param  string $text     A text that should be in the image barcode
     * @param  string $type     The barcode type. Supported types:
     *                          Code39 - Code 3 of 9
     *                          int25  - 2 Interleaved 5
     *                          ean13  - EAN 13
     *                          upca   - UPC-A
     * @param  string $imgtype  The image type that will be generated
     * @param  boolean $bSendToBrowser  if the image shall be outputted to the
     *                                  browser, or be returned.
     *
     * @return image            The corresponding gd image object;
     *                           PEAR_Error on failure
     *
     * @access public
     *
     * @author Marcelo Subtil Marcal <msmarcal@php.net>
     * @since  Image_Barcode 0.3
     */
    function &draw($text, $type = 'int25', $imgtype = 'png', $bSendToBrowser = true)
    {
        //Make sure no bad files are included
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $type)) {
            return PEAR::raiseError('Invalid barcode type ' . $type);
        }
        if (!include_once( $type . '.php')) {
            return PEAR::raiseError($type . ' barcode is not supported');
        }

        $classname = 'Image_Barcode_' . $type;

        if (!in_array('draw',get_class_methods($classname))) {
            return PEAR::raiseError("Unable to find draw method in '$classname' class");
        }

        @$obj = new $classname();

        $img = &$obj->draw($text, $imgtype);

        if (PEAR::isError($img)) {
            return $img;
        }

        if ($bSendToBrowser) {
            // Send image to browser
            switch ($imgtype) {
                case 'gif':
                    header('Content-type: image/gif');
                    imagegif($img);
                    imagedestroy($img);
                    break;

                case 'jpg':
                    header('Content-type: image/jpg');
                    imagejpeg($img);
                    imagedestroy($img);
                    break;

                default:
                    header('Content-type: image/png');
                    imagepng($img);
                    imagedestroy($img);
                    break;
            }
        } else {
            return $img;
        }
    }
}
?>
