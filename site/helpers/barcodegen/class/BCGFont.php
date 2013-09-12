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

/**
 *--------------------------------------------------------------------
 *
 * Holds font family and size.
 *
 *--------------------------------------------------------------------
 * Copyright (C) Jean-Sebastien Goupil
 * http://www.barcodephp.com
 */
class BCGFont {
	private $path;
	private $text;
	private $size;
	private $box;

	/**
	 * Constructor.
	 *
	 * @param string $fontPath path to the file
	 * @param int $size size in point
	 */
	public function __construct($fontPath, $size) {
		$this->path = $fontPath;
		$this->size = $size;
	}

	/**
	 * Sets the text associated to the font.
	 * Sets the text associated to the font.
	 * Sets the text associated to the font.
	 * Sets the text associated to the font.
	 * Sets the text associated to the font.
	 * Sets the text associated to the font.
	 *
	 * @param string text
	 */
	public function setText($text) {
		$this->text = $text;
		$im = imagecreate(1, 1);
		$this->box = imagettftext($im, $this->size, 0, 0, 0, imagecolorallocate($im, 0, 0, 0), $this->path, $this->text);
	}

	/**
	 * Returns the width that the text takes to be written.
	 *
	 * @return float
	 */
	public function getWidth() {
		if ($this->box !== NULL) {
			// Bug fixed : a number is aligned on the "right" in the box...
			// If we are writting the number "1" with minX at 2 and maxX at 10
			// The maxWidth will be 10 and not 8 because we don't squeeze the number
			// on its left. So now we don't remove the minX.
			return abs(max($this->box[2], $this->box[4]));
		} else {
			return 0;
		}
	}

	/**
	 * Returns the height that the text takes.
	 *
	 * @return float
	 */
	public function getHeight() {
		if ($this->box !== NULL) {
			return (float) abs(max($this->box[5], $this->box[7]) - min($this->box[1], $this->box[3]));
		} else {
			return 0.0;
		}
	}

	/**
	 * Returns the number of pixel under the baseline located at 0.
	 *
	 * @return float
	 */
	public function getUnderBaseline() {
		// Y for imagettftext : This sets the position of the fonts baseline, not the very bottom of the character.
		return (float) max($this->box[1], $this->box[3]);
	}

	/**
	 * Draws the text on the image at a specific position.
	 * $x and $y represent the left bottom corner.
	 *
	 * @param resource $im
	 * @param int $color
	 * @param int $x
	 * @param int $y
	 */
	public function draw($im, $color, $x, $y) {
		imagettftext($im, $this->size, 0, $x, $y, $color, $this->path, $this->text);
	}
}
?>