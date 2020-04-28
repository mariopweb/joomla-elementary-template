<?php
/**
 * @Copyright
 * @package     SIGE - Simple Image Gallery Extended for Joomla! 3.x
 * @author      Viktor Vogel <admin@kubik-rubik.de>
 * @version     3.3.4 - 2019-08-06
 * @link        https://kubik-rubik.de/sige-simple-image-gallery-extended
 *
 * @license     GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Fast check whether image url variable was transmitted
if ($_GET['img'] == '') {
    exit('No parameters!');
}

class TempThumbnailCreation
{
    protected $image;
    protected $imageData;
    protected $imageExtension;
    protected $width = 300;
    protected $height = 300;
    protected $widthNew = 300;
    protected $heightNew = 300;
    protected $widthOriginal = 300;
    protected $heightOriginal = 300;
    protected $quality = 80;
    protected $ratio = 1;
    protected $crop = 0;
    protected $cropFactor = 50;
    protected $cropPercent = 100;
    protected $cropWidth = 300;
    protected $cropHeight = 300;
    protected $thumbDetail = 0;
    protected $allowedExtensions = array('jpg', 'png', 'gif');
    protected $xCoordinate = 0;
    protected $yCoordinate = 0;

    public function __construct()
    {
        if ($this->loadImageData()) {
            $this->createThumbnail();
        }
    }

    /**
     * Prepares the data for the thumbnail creation
     *
     * @return bool
     */
    private function loadImageData()
    {
        $_GET['img'] = str_replace('..', '', htmlspecialchars(urldecode($_GET['img'])));
        $this->image = '../../../..' . $_GET['img'];

        if (empty($this->image) OR !file_exists($this->image)) {
            return false;
        }

        $this->validateRequestValue($this->width, 'width');
        $this->validateRequestValue($this->height, 'height');
        $this->validateRequestValue($this->quality, 'quality');
        $this->validateRequestValue($this->ratio, 'ratio');
        $this->validateRequestValue($this->crop, 'crop');
        $this->validateRequestValue($this->thumbDetail, 'thumbdetail');

        $this->imageExtension = strtolower(pathinfo($_GET['img'], PATHINFO_EXTENSION));

        if (!in_array($this->imageExtension, $this->allowedExtensions)) {
            return false;
        }

        $this->imageData = getimagesize($this->image);

        if (empty($this->imageData[0]) OR empty($this->imageData[1])) {
            return false;
        }

        $this->widthOriginal = $this->imageData[0];
        $this->heightOriginal = $this->imageData[1];

        $this->widthNew = $this->width;
        $this->heightNew = $this->height;

        $this->ratioCheck();
        $this->cropImage();

        return true;
    }

    /**
     * Validates the request variables
     *
     * @param $variable
     * @param $value
     */
    private function validateRequestValue(&$variable, $value)
    {
        if (isset($_GET[$value])) {
            $variable = intval(htmlspecialchars($_GET[$value]));
        }
    }

    /**
     * Checks the ratio of the image
     */
    private function ratioCheck()
    {
        if ($this->ratio) {
            $this->heightNew = (int) ($this->imageData[1] * ($this->widthNew / $this->imageData[0]));

            if ($this->height AND ($this->heightNew > $this->height)) {
                $this->heightNew = $this->height;
                $this->widthNew = (int) ($this->imageData[0] * ($this->heightNew / $this->imageData[1]));
            }
        }
    }

    /**
     * Crops the image if crop option is activated
     */
    private function cropImage()
    {
        if ($this->crop AND ($this->cropFactor > 0 AND $this->cropFactor < 100)) {
            $biggestSide = $this->heightOriginal;

            if ($this->widthOriginal > $this->heightOriginal) {
                $biggestSide = $this->widthOriginal;
            }

            $this->cropPercent = (1 - ($this->cropFactor / 100));
            $this->cropWidth = $this->widthOriginal * $this->cropPercent;
            $this->cropHeight = $this->heightOriginal * $this->cropPercent;

            if (!$this->ratio AND ($this->width == $this->height)) {
                $this->cropWidth = $biggestSide * $this->cropPercent;
                $this->cropHeight = $biggestSide * $this->cropPercent;
            } elseif (!$this->ratio AND ($this->width != $this->height)) {
                $this->cropWidth = ($this->width * ($this->heightOriginal / $this->height)) * $this->cropPercent;
                $this->cropHeight = $this->heightOriginal * $this->cropPercent;

                if (($this->widthOriginal / $this->width) < ($this->heightOriginal / $this->height)) {
                    $this->cropWidth = $this->widthOriginal * $this->cropPercent;
                    $this->cropHeight = ($this->height * ($this->widthOriginal / $this->width)) * $this->cropPercent;
                }
            }

            $this->xCoordinate = ($this->widthOriginal - $this->cropWidth) / 2;
            $this->yCoordinate = ($this->heightOriginal - $this->cropHeight) / 2;
        }
    }

    /**
     * Created the thumbnail output
     */
    private function createThumbnail()
    {
        if ($this->imageExtension == 'jpg') {
            header('Content-type: image/jpg');
            $srcImg = imagecreatefromjpeg($this->image);
            $dstImg = imagecreatetruecolor($this->widthNew, $this->heightNew);

            $this->resizeImage($dstImg, $srcImg);

            imagejpeg($dstImg, null, $this->quality);

            if (is_resource($srcImg)) {
                imagedestroy($srcImg);
            }

            if (is_resource($dstImg)) {
                imagedestroy($dstImg);
            }

            return;
        }

        if ($this->imageExtension == 'gif') {
            header('Content-type: image/gif');
            $srcImg = imagecreatefromgif($this->image);
            $dstImg = imagecreatetruecolor($this->widthNew, $this->heightNew);
            imagepalettecopy($dstImg, $srcImg);

            $this->resizeImage($dstImg, $srcImg);

            imagegif($dstImg);

            if (is_resource($srcImg)) {
                imagedestroy($srcImg);
            }

            if (is_resource($dstImg)) {
                imagedestroy($dstImg);
            }

            return;
        }

        if ($this->imageExtension == 'png') {
            header('Content-type: image/png');
            $srcImg = imagecreatefrompng($this->image);
            $dstImg = imagecreatetruecolor($this->widthNew, $this->heightNew);
            imagepalettecopy($dstImg, $srcImg);

            $this->resizeImage($dstImg, $srcImg);

            imagepng($dstImg, null, 6);

            if (is_resource($srcImg)) {
                imagedestroy($srcImg);
            }

            if (is_resource($dstImg)) {
                imagedestroy($dstImg);
            }

            return;
        }
    }

    /**
     * Resizes the image depending on selected parameters
     *
     * @param $dstImg
     * @param $srcImg
     */
    private function resizeImage(&$dstImg, $srcImg)
    {
        if ($this->crop AND ($this->cropFactor > 0 AND $this->cropFactor < 100)) {
            imagecopyresampled($dstImg, $srcImg, 0, 0, $this->xCoordinate, $this->yCoordinate, $this->widthNew, $this->heightNew, $this->cropWidth, $this->cropHeight);

            return;
        }

        if ($this->thumbDetail == 1) {
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $this->widthNew, $this->heightNew, $this->widthNew, $this->heightNew);
        } elseif ($this->thumbDetail == 2) {
            imagecopyresampled($dstImg, $srcImg, 0, 0, $this->widthOriginal - $this->widthNew, 0, $this->widthNew, $this->heightNew, $this->widthNew, $this->heightNew);
        } elseif ($this->thumbDetail == 3) {
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, $this->heightOriginal - $this->heightNew, $this->widthNew, $this->heightNew, $this->widthNew, $this->heightNew);
        } elseif ($this->thumbDetail == 4) {
            imagecopyresampled($dstImg, $srcImg, 0, 0, $this->widthOriginal - $this->widthNew, $this->heightOriginal - $this->heightNew, $this->widthNew, $this->heightNew, $this->widthNew, $this->heightNew);
        } else {
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $this->widthNew, $this->heightNew, $this->widthOriginal, $this->heightOriginal);
        }
    }
}

new TempThumbnailCreation();