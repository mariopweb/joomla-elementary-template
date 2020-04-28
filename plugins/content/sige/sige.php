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
defined('_JEXEC') || die('Restricted access');

class PlgContentSige extends JPlugin
{
    protected $absolutePath;
    protected $allowedExtensions = array('jpg', 'png', 'gif');
    protected $articleTitle = '';
    protected $autoloadLanguage = true;
    protected $image;
    protected $imageInfo = array();
    protected $imagesDir;
    protected $liveSite;
    protected $photoswipeJs = array();
    protected $pluginParameters = array();
    protected $rootFolder = '/images/';
    protected $session;
    protected $sigCount;
    protected $sigCountArticles;
    protected $syntaxParameters = array();
    protected $thumbnailMaxHeight;
    protected $thumbnailMaxWidth;
    protected $turboCssReadIn;
    protected $turboHtmlReadIn;

    /**
     * PlgContentSige constructor.
     *
     * @param $subject
     * @param $config
     *
     * @throws Exception
     */
    public function __construct(&$subject, $config)
    {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }

        parent::__construct($subject, $config);

        $version = new JVersion();
        $joomlaMainVersion = substr($version->RELEASE, 0, strpos($version->RELEASE, '.'));

        if ($joomlaMainVersion != '3') {
            throw new Exception(JText::_('PLG_SIGE_NEEDJ3'), 404);
        }

        $this->session = JFactory::getSession();
        $this->session->clear('sigcount', 'sige');
        $this->session->clear('sigcountarticles', 'sige');

        $this->absolutePath = JPATH_SITE;
        $this->liveSite = JUri::base();

        if (substr($this->liveSite, -1) == '/') {
            $this->liveSite = substr($this->liveSite, 0, -1);
        }
    }

    /**
     * Entry point of the plugin in core content trigger onContentPrepare
     *
     * @param $context
     * @param $article
     * @param $params
     * @param $limitstart
     *
     * @throws Exception
     */
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        if (stripos($article->text, '{gallery}') === false) {
            return;
        }

        $this->sigCountArticles = $this->session->get('sigcountarticles', -1, 'sige');

        if (preg_match_all('@{gallery}(.*){/gallery}@Us', $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
            $this->sigCountArticles++;
            $this->session->set('sigcountarticles', $this->sigCountArticles, 'sige');
            $this->sigCount = $this->session->get('sigcount', -1, 'sige');
            $this->pluginParameters['lang'] = JFactory::getLanguage()->getTag();

            foreach ($matches[1] as $match) {
                $this->sigCount++;
                $this->session->set('sigcount', $this->sigCount, 'sige');
                $sigeSyntaxArray = explode(',', $match);
                $this->imagesDir = $sigeSyntaxArray[0];

                $this->getSyntaxParameters($sigeSyntaxArray);
                $this->setParams($article);
                $this->setTurboParameters();
                $html = $this->createHtmlOutput();

                $article->text = preg_replace('@(<p>)?{gallery}' . $match . '{/gallery}(</p>)?@s', $html, $article->text);
            }

            $this->addDataToBottom($article->text);
            $this->loadHeadData();
        }
    }

    /**
     * Extracts all parameters from the entered syntax
     *
     * @param $sigeArray
     */
    private function getSyntaxParameters($sigeArray)
    {
        // Reset syntax parameters if syntax is used more than once per page
        $this->syntaxParameters = array();

        if (count($sigeArray) >= 2) {
            for ($i = 1; $i < count($sigeArray); $i++) {
                $parameterTemp = explode('=', $sigeArray[$i], 2);

                if (count($parameterTemp) == 2) {
                    $this->syntaxParameters[strtolower(trim($parameterTemp[0]))] = trim($parameterTemp[1]);
                }
            }
        }
    }

    /**
     * Sets all parameters for the correct execution
     *
     * @param $article
     *
     * @throws Exception
     */
    private function setParams($article)
    {
        $params = $this->getParamsList();

        foreach ($params as $value) {
            $this->pluginParameters[$value] = $this->getParams($value);
        }

        $count = $this->getParams('count', true);

        if (!empty($count)) {
            $this->sigCount = $count;
        }

        if ($this->pluginParameters['root']) {
            $this->rootFolder = '/';
        }

        if (!empty($this->pluginParameters['displaymessage'])) {
            if (JFactory::getApplication()->input->getWord('view') != 'featured' && isset($article->title)) {
                $this->articleTitle = preg_replace("@\"@", "'", $article->title);
            }
        }

        $this->thumbnailMaxHeight = $this->pluginParameters['height'];
        $this->thumbnailMaxWidth = $this->pluginParameters['width'];
    }

    /**
     * Returns the complete params list as an array
     *
     * @return array
     */
    private function getParamsList()
    {
        return array(
            'calcmaxthumbsize',
            'caption',
            'column_quantity',
            'connect',
            'copyright',
            'crop',
            'crop_factor',
            'css_image',
            'css_image_half',
            'displaymessage',
            'displaynavtip',
            'download',
            'encrypt',
            'fileinfo',
            'gap_h',
            'gap_v',
            'height',
            'height_image',
            'image_info',
            'image_link',
            'image_link_new',
            'images_new',
            'iptc',
            'iptcutf8',
            'js',
            'limit',
            'limit_quantity',
            'list',
            'message',
            'modaltitle',
            'navtip',
            'nodebug',
            'noslim',
            'print',
            'quality',
            'quality_png',
            'ratio',
            'ratio_image',
            'resize_images',
            'root',
            'salign',
            'scaption',
            'single',
            'single_gallery',
            'sort',
            'thumbdetail',
            'thumbs',
            'thumbs_new',
            'turbo',
            'view',
            'watermark',
            'watermark_new',
            'watermarkimage',
            'watermarkposition',
            'width',
            'width_image',
            'word',
        );
    }

    /**
     * Gets a specific parameter - syntax or plugins settings
     *
     * @param        $param
     * @param bool   $syntaxOnly
     * @param string $default
     *
     * @return mixed|string
     */
    private function getParams($param, $syntaxOnly = false, $default = '')
    {
        if (array_key_exists($param, $this->syntaxParameters) && $this->syntaxParameters[$param] !== '') {
            return $this->syntaxParameters[$param];
        }

        if (empty($syntaxOnly)) {
            return $this->params->get($param);
        }

        return $default;
    }

    /**
     * Sets the turbo mode parameters
     */
    private function setTurboParameters()
    {
        $this->turboHtmlReadIn = false;
        $this->turboCssReadIn = false;

        if ($this->pluginParameters['turbo']) {
            if ($this->pluginParameters['turbo'] == 'new') {
                $this->turboHtmlReadIn = true;
                $this->turboCssReadIn = true;

                return;
            }

            if (!file_exists($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_html-' . $this->pluginParameters['lang'] . '.txt')) {
                $this->turboHtmlReadIn = true;
            }

            if (!file_exists($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_css-' . $this->pluginParameters['lang'] . '.txt')) {
                $this->turboCssReadIn = true;
            }
        }
    }

    /**
     * Creates the HTML output of the gallery
     *
     * @return string
     */
    private function createHtmlOutput()
    {
        if (!$this->pluginParameters['turbo'] || ($this->pluginParameters['turbo'] && $this->turboHtmlReadIn)) {
            $images = array();
            $imagesLoaded = 0;
            $this->loadImagesFromDir($images, $imagesLoaded);

            // Set default message
            $html = '';

            if (empty($this->pluginParameters['nodebug'])) {
                $html .= '<p class="sige_noimages">' . JText::_('NOIMAGES') . '<br /><br />' . JText::_('NOIMAGESDEBUG') . ' ' . $this->liveSite . $this->rootFolder . $this->imagesDir . '</p>';
            }

            if (!empty($imagesLoaded)) {
                if (!file_exists($this->absolutePath . $this->rootFolder . $this->imagesDir . '/index.html')) {
                    file_put_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/index.html', '');
                }

                $this->sortImagesArray($images);

                $imagesLoadedRest = 0;
                $singleYes = false;

                if ($this->pluginParameters['single']) {
                    $this->singleImageHandling($images, $imagesLoaded, $imagesLoadedRest, $singleYes);
                }

                $fileInfo = $this->getFileInfo($images, $imagesLoaded, $singleYes);

                if ($this->pluginParameters['calcmaxthumbsize']) {
                    $this->calculateMaxThumbnailSize($images);
                }

                $sigeCss = $this->createMainCssInstruction();
                $this->loadHeadData($sigeCss);

                if ($this->pluginParameters['resize_images']) {
                    $this->resizeImages($images);
                }

                if ($this->pluginParameters['watermark']) {
                    $this->createWatermark($images, $singleYes);
                }

                $this->limitImageList($imagesLoaded, $imagesLoadedRest);

                if ($this->pluginParameters['thumbs']) {
                    $this->createThumbnails($images, $imagesLoaded);
                }

                if ($this->pluginParameters['word']) {
                    $imagesLoadedRest = $imagesLoaded;
                    $this->pluginParameters['limit_quantity'] = 1;
                    $imagesLoaded = 1;
                }

                $html = '<!-- Simple Image Gallery Extended - Plugin Joomla! 3.x - Kubik-Rubik Joomla! Extensions -->';

                if (empty($this->pluginParameters['word'])) {
                    $html .= '<ul id="sige_' . $this->sigCount . '" class="';

                    if ($this->pluginParameters['single'] && !empty($singleYes)) {
                        $html .= 'sige_single';
                    } elseif ($this->pluginParameters['list']) {
                        $html .= 'sige_list';
                    } else {
                        $html .= 'sige';
                    }

                    // PhotoSwipe - Add specific class for the PhotoSwipe library
                    if ($this->pluginParameters['view'] == 7) {
                        $swipeClassId = $this->getSwipeClassId();

                        $html .= ' sige_swipe_' . $swipeClassId;
                    }

                    $html .= '">';
                } else {
                    if ($this->pluginParameters['view'] == 7) {
                        $swipeClassId = $this->getSwipeClassId();

                        $html .= '<span class="sige_swipe_' . $swipeClassId . '">';
                    }
                }

                for ($a = 0; $a < $imagesLoaded; $a++) {
                    $this->htmlImage($images[$a]['filename'], $html, 0, $fileInfo, $a);
                }

                if (!empty($imagesLoadedRest) && !$this->pluginParameters['image_link']) {
                    for ($a = $this->pluginParameters['limit_quantity']; $a < $imagesLoadedRest; $a++) {
                        $this->htmlImage($images[$a]['filename'], $html, 1, $fileInfo, $a);
                    }
                }

                if (empty($this->pluginParameters['word'])) {
                    if ($this->pluginParameters['list']) {
                        $html .= '</ul>';
                    } else {
                        $html .= '</ul><span class="sige_clr"></span>';
                    }
                } else {
                    if ($this->pluginParameters['view'] == 7) {
                        $html .= '</span>';
                    }
                }

                // PhotoSwipe
                if ($this->pluginParameters['view'] == 7) {
                    // Add Photoswipe JavaScript code but only once for the same class ID
                    static $photoswipeId = false;

                    if ($photoswipeId !== $swipeClassId) {
                        $photoswipeJs = 'jQuery(document).ready(photoSwipeSige(".sige_swipe_' . $swipeClassId . '", ".sige_swipe_single_' . $swipeClassId . '"));';

                        $this->photoswipeJs[] = $photoswipeJs;

                        if ($this->turboHtmlReadIn) {
                            file_put_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_js-' . $this->pluginParameters['lang'] . '.txt', $photoswipeJs);
                        }

                        $photoswipeId = $swipeClassId;
                    }
                }

                if ($this->pluginParameters['copyright']) {
                    if ((!$this->pluginParameters['single'] || ($this->pluginParameters['single'] && !$singleYes)) && !$this->pluginParameters['list'] && !$this->pluginParameters['word']) {
                        $html .= '<p class="sige_small"><a href="https://kubik-rubik.de/" title="SIGE - Simple Image Gallery Extended - Kubik-Rubik Joomla! Extensions" target="_blank">Simple Image Gallery Extended</a></p>';
                    }
                }
            }

            if ($this->turboHtmlReadIn) {
                file_put_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_html-' . $this->pluginParameters['lang'] . '.txt', $html);
            }

            return $html;
        }

        $this->loadHeadData(1);
        $this->loadPhotoSwipeJs();
        $html = file_get_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_html-' . $this->pluginParameters['lang'] . '.txt');

        return $html;
    }

    /**
     * Loads all images with the allowed extensions from the specified directory
     *
     * @param $images
     * @param $imagesLoaded
     */
    private function loadImagesFromDir(&$images, &$imagesLoaded)
    {
        $directory = $this->absolutePath . $this->rootFolder . $this->imagesDir;

        if (is_dir($directory)) {
            if ($handle = opendir($directory)) {
                while (($file = readdir($handle)) !== false) {
                    if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->allowedExtensions)) {
                        $images[] = array('filename' => $file);
                        $imagesLoaded++;
                    }
                }

                closedir($handle);
            }
        }
    }

    /**
     * Sorts the images array
     *
     * @param $images
     */
    private function sortImagesArray(&$images)
    {
        if ($this->pluginParameters['sort'] == 1) {
            shuffle($images);
        } elseif ($this->pluginParameters['sort'] == 2) {
            sort($images);
        } elseif ($this->pluginParameters['sort'] == 3) {
            rsort($images);
        } elseif ($this->pluginParameters['sort'] == 4 || $this->pluginParameters['sort'] == 5) {
            for ($a = 0; $a < count($images); $a++) {
                $images[$a]['timestamp'] = filemtime($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $images[$a]['filename']);
            }

            if ($this->pluginParameters['sort'] == 4) {
                usort($images, array($this, 'timeasc'));
            } elseif ($this->pluginParameters['sort'] == 5) {
                usort($images, array($this, 'timedesc'));
            }
        }
    }

    /**
     * Handles the single image output properly
     *
     * @param $images
     * @param $imagesLoaded
     * @param $imagesLoadedRest
     * @param $singleYes
     */
    private function singleImageHandling(&$images, &$imagesLoaded, &$imagesLoadedRest, &$singleYes)
    {
        if ($images[0]['filename'] == $this->pluginParameters['single']) {
            if ($this->pluginParameters['single_gallery']) {
                $imagesLoadedRest = $imagesLoaded;
                $this->pluginParameters['limit_quantity'] = 1;
            }

            $imagesLoaded = 1;
            $singleYes = true;

            return;
        }

        for ($a = 1; $a < $imagesLoaded; $a++) {
            if ($images[$a]['filename'] == $this->pluginParameters['single']) {
                if ($this->pluginParameters['single_gallery']) {
                    $imagesLoadedRest = $imagesLoaded;
                    $this->pluginParameters['limit_quantity'] = 1;
                }

                $imageSingle = $images[$a];
                unset($images[$a]);
                array_unshift($images, $imageSingle);

                $imagesLoaded = 1;
                $singleYes = true;

                break;
            }
        }
    }

    /**
     * Loads all information from the info text file
     *
     * @param $images
     * @param $imagesLoaded
     * @param $singleYes
     *
     * @return array
     */
    private function getFileInfo(&$images, &$imagesLoaded, $singleYes)
    {
        $fileInfo = array();

        if ($this->pluginParameters['fileinfo']) {
            $captionsLang = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/captions-' . $this->pluginParameters['lang'] . '.txt';
            $captionsTxtFile = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/captions.txt';

            if (file_exists($captionsLang)) {
                $captionsFile = array_map('trim', file($captionsLang));

                foreach ($captionsFile as $value) {
                    if (!empty($value)) {
                        $captionsLine = explode('|', $value);
                        $fileInfo[] = $captionsLine;
                    }
                }
            } elseif (file_exists($captionsTxtFile) && !file_exists($captionsLang)) {
                $captionsFile = array_map('trim', file($captionsTxtFile));

                foreach ($captionsFile as $value) {
                    if (!empty($value)) {
                        $captionsLine = explode('|', $value);
                        $fileInfo[] = $captionsLine;
                    }
                }
            }

            // Use the sorting from the captions.text to sort the images
            if (!empty($fileInfo) && $this->pluginParameters['sort'] == 6 && empty($singleYes)) {
                $imagesFileInfo = array();

                foreach ($fileInfo as $fileInfoImage) {
                    foreach ($images as $key => $image) {
                        if ($fileInfoImage[0] == $image['filename']) {
                            $imagesFileInfo[]['filename'] = $fileInfoImage[0];
                            unset($images[$key]);
                            break;
                        }
                    }
                }

                if (!empty($imagesFileInfo)) {
                    $images = $imagesFileInfo;
                    $imagesLoaded = count($images);
                }
            }
        }

        return $fileInfo;
    }

    /**
     * Calculates the maximum thumbnails size (resolution) of all loaded images
     *
     * @param $images
     */
    private function calculateMaxThumbnailSize($images)
    {
        $maxHeight = array();
        $maxWidth = array();

        foreach ($images as $image) {
            list($maxHeight[], $maxWidth[]) = $this->calculateSize($image['filename'], 1);
        }

        rsort($maxHeight);
        rsort($maxWidth);

        $this->thumbnailMaxHeight = $maxHeight[0];
        $this->thumbnailMaxWidth = $maxWidth[0];
    }

    /**
     * Gets the correct resolution dependent of selected parameters
     *
     * @param $image
     * @param $thumbnail
     *
     * @return array
     */
    private function calculateSize($image, $thumbnail)
    {
        if ($this->pluginParameters['resize_images'] && empty($thumbnail)) {
            list($heightNew, $widthNew) = $this->calculateSizeProcess($image, $this->pluginParameters['height_image'], $this->pluginParameters['width_image'], $this->pluginParameters['ratio_image']);

            return array($heightNew, $widthNew);
        }

        list($heightNew, $widthNew) = $this->calculateSizeProcess($image, $this->pluginParameters['height'], $this->pluginParameters['width'], $this->pluginParameters['ratio']);

        return array($heightNew, $widthNew);
    }

    /**
     * Calculates the proper resolution of a specific image and returns the height and width in an array with integer
     * type casting
     *
     * @param $image
     * @param $height
     * @param $width
     * @param $ratio
     *
     * @return array
     */
    private function calculateSizeProcess($image, $height, $width, $ratio)
    {
        $heightNew = $height;
        $widthNew = $width;

        if (!empty($ratio)) {
            $imageData = getimagesize($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image);
            $heightNew = $imageData[1] * ($width / $imageData[0]);

            if ($heightNew > $height) {
                $heightNew = $height;
                $widthNew = $imageData[0] * ($height / $imageData[1]);
            }
        }

        return array((int) $heightNew, (int) $widthNew);
    }

    /**
     * Creates the main CSS instructions for the gallery output
     *
     * @return string
     */
    private function createMainCssInstruction()
    {
        $sigeCss = '';

        if ($this->pluginParameters['css_image']) {
            $cssImageWidth = 600;

            if ($this->pluginParameters['css_image_half']) {
                $cssImageWidth = $cssImageWidth / 2;
            }

            $sigeCss .= '.sige_cont_' . $this->sigCount . ' .sige_css_image:hover span{width: ' . $cssImageWidth . 'px;}' . "\n";
        }

        $captionHeight = 0;

        if ($this->pluginParameters['caption']) {
            $captionHeight = 20;
        }

        if ($this->pluginParameters['salign']) {
            if ($this->pluginParameters['salign'] == 'left') {
                $sigeCss .= '.sige_cont_' . $this->sigCount . ' {width:' . ($this->thumbnailMaxWidth + $this->pluginParameters['gap_h']) . 'px;height:' . ($this->thumbnailMaxHeight + $this->pluginParameters['gap_v'] + $captionHeight) . 'px;float:left;display:inline-block;}' . "\n";
            } elseif ($this->pluginParameters['salign'] == 'right') {
                $sigeCss .= '.sige_cont_' . $this->sigCount . ' {width:' . ($this->thumbnailMaxWidth + $this->pluginParameters['gap_h']) . 'px;height:' . ($this->thumbnailMaxHeight + $this->pluginParameters['gap_v'] + $captionHeight) . 'px;float:right;display:inline-block;}' . "\n";
            } elseif ($this->pluginParameters['salign'] == 'center') {
                $sigeCss .= '.sige_cont_' . $this->sigCount . ' {width:' . ($this->thumbnailMaxWidth + $this->pluginParameters['gap_h']) . 'px;height:' . ($this->thumbnailMaxHeight + $this->pluginParameters['gap_v'] + $captionHeight) . 'px;display:inline-block;}' . "\n";
            }

            return $sigeCss;
        }

        $sigeCss .= '.sige_cont_' . $this->sigCount . ' {width:' . ($this->thumbnailMaxWidth + $this->pluginParameters['gap_h']) . 'px;height:' . ($this->thumbnailMaxHeight + $this->pluginParameters['gap_v'] + $captionHeight) . 'px;float:left;display:inline-block;}' . "\n";

        return $sigeCss;
    }

    /**
     * Loads the CSS and JS instructions to the head section of the HTML page
     *
     * @param int $sigeCss
     */
    private function loadHeadData($sigeCss = 0)
    {
        $document = JFactory::getDocument();

        if ($document instanceof JDocumentHtml) {
            $head = $this->getHeadData($sigeCss);

            // Combine dynamic CSS instructions - Check whether a custom style tag was already set and combine them to
            // avoid problems in some browsers due to too many CSS instructions
            if (!empty($sigeCss)) {
                if (!empty($document->_custom)) {
                    $customTags = array();

                    foreach ($document->_custom as $key => $customTag) {
                        if (preg_match('@<style type="text/css">(.*)</style>@Us', $customTag, $match)) {
                            $customTags[] = $match[1];
                            unset($document->_custom[$key]);
                        }
                    }

                    // If content is loaded from the turbo file, then the CSS instructions need to be prepared for the output
                    if ($sigeCss == 1) {
                        if (preg_match('@<style type="text/css">(.*)</style>@Us', $head, $match)) {
                            $sigeCss = $match[1];
                        }
                    }

                    if (!empty($customTags)) {
                        $head = '<style type="text/css">' . implode('', $customTags) . $sigeCss . '</style>';
                    }
                }
            }

            if (!empty($head)) {
                $document->addCustomTag($head);
            }
        }
    }

    /**
     * Creates the correct CSS and JS instructions for the loaded gallery
     *
     * @param $sigeCss
     *
     * @return array|string
     */
    private function getHeadData($sigeCss)
    {
        if (!empty($sigeCss)) {
            if (!$this->pluginParameters['turbo'] || ($this->pluginParameters['turbo'] && $this->turboCssReadIn)) {
                $head = '<style type="text/css">' . $sigeCss . '</style>';

                if ($this->turboCssReadIn) {
                    file_put_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_css-' . $this->pluginParameters['lang'] . '.txt', $head);
                }

                return $head;
            }

            $head = file_get_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_css-' . $this->pluginParameters['lang'] . '.txt');

            return $head;
        }

        if ($this->sigCountArticles == 0) {
            $head = array();
            $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/sige.css" type="text/css" media="screen" />';

            if ($this->pluginParameters['js'] == 1) {
                JHtml::_('behavior.framework');

                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/slimbox.js"></script>';
                $head[] = '<script type="text/javascript">
                                Slimbox.scanPage = function() {
                                    $$("a[rel^=lightbox]").slimbox({counterText: "' . JText::_('PLG_SIGE_SLIMBOX_IMAGES') . '"}, null, function(el) {
                                        return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
                                    });
                                };
                                if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
                                    window.addEvent("domready", Slimbox.scanPage);
                                }
                                </script>';
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/slimbox.css" type="text/css" media="screen" />';
            } elseif ($this->pluginParameters['js'] == 2) {
                if ($this->pluginParameters['lang'] == 'de-DE') {
                    $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/lytebox.js"></script>';
                } else {
                    $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/lytebox_en.js"></script>';
                }

                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/lytebox.css" type="text/css" media="screen" />';
            } elseif ($this->pluginParameters['js'] == 3) {
                if ($this->pluginParameters['lang'] == 'de-DE') {
                    $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/shadowbox.js"></script>';
                } else {
                    $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/shadowbox_en.js"></script>';
                }

                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/shadowbox.css" type="text/css" media="screen" />';
                $head[] = '<script type="text/javascript">Shadowbox.init();</script>';
            } elseif ($this->pluginParameters['js'] == 4) {
                JHtml::_('behavior.framework');

                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/milkbox.js"></script>';
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/milkbox.css" type="text/css" media="screen" />';
            } elseif ($this->pluginParameters['js'] == 5) {
                JHtml::_('jquery.framework');

                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/slimbox2.js"></script>';
                $head[] = '<script type="text/javascript">
                                if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
                                    jQuery(function($) {
                                        $("a[rel^=\'lightbox\']").slimbox({counterText: "' . JText::_('PLG_SIGE_SLIMBOX_IMAGES') . '"}, null, function(el) {
                                            return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
                                        });
                                    });
                                }
                                </script>';
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/slimbox2.css" type="text/css" media="screen" />';
            } elseif ($this->pluginParameters['js'] == 6) {
                JHtml::_('jquery.framework');

                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/venobox/venobox.js"></script>';

                $venoboxIni = '<script type="text/javascript">jQuery(document).ready(function(){jQuery(\'.venobox\').venobox(';

                if (!empty($this->pluginParameters['modaltitle'])) {
                    $venoboxIni .= '{titleattr: \'' . $this->pluginParameters['modaltitle'] . '\'}';
                }

                $venoboxIni .= ');});</script>';

                $head[] = $venoboxIni;
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/venobox/venobox.css" type="text/css" media="screen" />';
            } elseif ($this->pluginParameters['js'] == 7) {
                JHtml::_('jquery.framework');

                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/photoswipe/photoswipe.min.js"></script>';
                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/photoswipe/photoswipe-ui-default.min.js"></script>';
                $head[] = '<script type="text/javascript" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/photoswipe/photoswipe.sige.min.js"></script>';
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/photoswipe/photoswipe.css" type="text/css" />';
                $head[] = '<link rel="stylesheet" href="' . $this->liveSite . '/plugins/content/sige/plugin_sige/photoswipe/default-skin/default-skin.css" type="text/css" />';
            }

            return "\n" . implode("\n", $head) . "\n";
        }

        return '';
    }

    /**
     * Resizes all loaded images to the specified resolution
     *
     * @param $images
     */
    private function resizeImages($images)
    {
        $this->createEmptyDirectory($this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages');
        $num = count($images);

        for ($a = 0; $a < $num; $a++) {
            $this->resizeImage($images[$a]['filename']);
        }
    }

    /**
     * Creates an empty directory and index.html file in the transferred path
     *
     * @param $path
     */
    private function createEmptyDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755);
            file_put_contents($path . '/index.html', '');
        }
    }

    /**
     * Resizes a specific image properly
     *
     * @param $image
     */
    private function resizeImage($image)
    {
        if (!empty($image)) {
            $fileNameThumb = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $image;

            if (!file_exists($fileNameThumb) || !empty($this->pluginParameters['images_new'])) {
                $imageType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

                if ($imageType == 'jpg') {
                    $imageSource = imagecreatefromjpeg($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image);
                    $imageThumb = $this->resizeImageThumbnail($image, $imageSource);
                    imagejpeg($imageThumb, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $image, $this->pluginParameters['quality']);
                } elseif ($imageType == 'png') {
                    $imageSource = imagecreatefrompng($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image);
                    $imageThumb = $this->resizeImageThumbnail($image, $imageSource);
                    imagepng($imageThumb, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $image, $this->pluginParameters['quality_png']);
                } elseif ($imageType == 'gif') {
                    $imageSource = imagecreatefromgif($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image);
                    $imageThumb = $this->resizeImageThumbnail($image, $imageSource);
                    imagegif($imageThumb, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $image);
                }

                if (isset($imageSource) && is_resource($imageSource)) {
                    imagedestroy($imageSource);
                }

                if (isset($imageThumb) && is_resource($imageThumb)) {
                    imagedestroy($imageThumb);
                }

                return;
            }
        }
    }

    /**
     * Creates the copy of the resized image
     *
     * @param $image
     * @param $imageSource
     *
     * @return resource
     */
    private function resizeImageThumbnail($image, $imageSource)
    {
        $widthOriginal = imagesx($imageSource);
        $heightOriginal = imagesy($imageSource);
        list($heightNew, $widthNew) = $this->calculateSize($image, 0);
        $imageThumb = imagecreatetruecolor($widthNew, $heightNew);
        imagecopyresampled($imageThumb, $imageSource, 0, 0, 0, 0, $widthNew, $heightNew, $widthOriginal, $heightOriginal);

        return $imageThumb;
    }

    /**
     * Creates the watermark on the images
     *
     * @param $images
     * @param $singleYes
     */
    private function createWatermark($images, $singleYes)
    {
        $this->createEmptyDirectory($this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm');
        $num = count($images);

        if (empty($this->pluginParameters['single_gallery']) && $singleYes) {
            $num = 1;
        }

        for ($a = 0; $a < $num; $a++) {
            $this->createWatermarkImage($images[$a]['filename']);
        }
    }

    /**
     * Creates the watermark on a specific image
     *
     * @param $image
     */
    private function createWatermarkImage($image)
    {
        if (!empty($image)) {
            $imageHash = $this->encryptImageName($image);
            $fileNameWatermark = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm/' . $imageHash;

            if (!file_exists($fileNameWatermark) || !empty($this->pluginParameters['watermark_new'])) {
                $imageWatermark = imagecreatefrompng($this->absolutePath . '/plugins/content/sige/plugin_sige/watermark.png');

                if ($this->pluginParameters['watermarkimage']) {
                    $imageWatermark = imagecreatefrompng($this->absolutePath . '/plugins/content/sige/plugin_sige/' . $this->pluginParameters['watermarkimage']);
                }

                $watermarkWidth = imagesx($imageWatermark);
                $watermarkHeight = imagesy($imageWatermark);
                $imageType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

                if ($imageType == 'jpg') {
                    $imageSource = imagecreatefromjpeg($this->createWatermarkImageThumbnailSourcePath($image));
                    $imageSourceWatermark = $this->createWatermarkImageCopy($imageSource, $imageWatermark, $watermarkHeight, $watermarkWidth);
                    imagejpeg($imageSourceWatermark, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm/' . $imageHash, $this->pluginParameters['quality']);
                } elseif ($imageType == 'png') {
                    $imageSource = imagecreatefrompng($this->createWatermarkImageThumbnailSourcePath($image));
                    $imageSourceWatermark = $this->createWatermarkImageCopy($imageSource, $imageWatermark, $watermarkHeight, $watermarkWidth);
                    imagepng($imageSourceWatermark, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm/' . $imageHash, $this->pluginParameters['quality_png']);
                } elseif ($imageType == 'gif') {
                    $imageSource = imagecreatefromgif($this->createWatermarkImageThumbnailSourcePath($image));
                    $imageSourceTemp = imagecreatetruecolor(imagesx($imageSource), imagesy($imageSource));
                    imagecopy($imageSourceTemp, $imageSource, 0, 0, 0, 0, imagesx($imageSource), imagesy($imageSource));
                    $imageSource = $imageSourceTemp;

                    $imageSourceWatermark = $this->createWatermarkImageCopy($imageSource, $imageWatermark, $watermarkHeight, $watermarkWidth);
                    imagegif($imageSourceWatermark, $this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm/' . $imageHash);
                }

                if (isset($imageSource) && is_resource($imageSource)) {
                    imagedestroy($imageSource);
                }

                if (isset($imageWatermark) && is_resource($imageWatermark)) {
                    imagedestroy($imageWatermark);
                }
            }
        }

        return;
    }

    /**
     * Creates an image name hash to hide the original image name
     *
     * @param $image
     *
     * @return string
     */
    private function encryptImageName($image)
    {
        $watermarkEncryption = (int) $this->pluginParameters['encrypt'];

        if ($watermarkEncryption === -1) {
            return $image;
        }

        $imageName = pathinfo($image, PATHINFO_FILENAME);
        $imageType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $imageHash = md5($imageName);

        if ($watermarkEncryption == 0) {
            $imageHash = str_rot13($imageName);
        }

        if ($watermarkEncryption == 2) {
            $imageHash = sha1($imageName);
        }

        return $imageHash . '.' . $imageType;
    }

    /**
     * Creates the watermark image thumbnail source path
     *
     * @param $image
     *
     * @return string
     */
    private function createWatermarkImageThumbnailSourcePath($image)
    {
        if ($this->pluginParameters['resize_images']) {
            return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $image;
        }

        return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image;
    }

    /**
     * Creates the watermark image copy
     *
     * @param $imageSource
     * @param $imageWatermark
     * @param $watermarkHeight
     * @param $watermarkWidth
     *
     * @return mixed
     */
    private function createWatermarkImageCopy($imageSource, $imageWatermark, $watermarkHeight, $watermarkWidth)
    {
        $widthOriginal = imagesx($imageSource);
        $heightOriginal = imagesy($imageSource);

        if ($this->pluginParameters['watermarkposition'] == 1) {
            imagecopy($imageSource, $imageWatermark, 0, 0, 0, 0, $watermarkWidth, $watermarkHeight);
        } elseif ($this->pluginParameters['watermarkposition'] == 2) {
            imagecopy($imageSource, $imageWatermark, $widthOriginal - $watermarkWidth, 0, 0, 0, $watermarkWidth, $watermarkHeight);
        } elseif ($this->pluginParameters['watermarkposition'] == 3) {
            imagecopy($imageSource, $imageWatermark, 0, $heightOriginal - $watermarkHeight, 0, 0, $watermarkWidth, $watermarkHeight);
        } elseif ($this->pluginParameters['watermarkposition'] == 4) {
            imagecopy($imageSource, $imageWatermark, $widthOriginal - $watermarkWidth, $heightOriginal - $watermarkHeight, 0, 0, $watermarkWidth, $watermarkHeight);
        } else {
            imagecopy($imageSource, $imageWatermark, ($widthOriginal - $watermarkWidth) / 2, ($heightOriginal - $watermarkHeight) / 2, 0, 0, $watermarkWidth, $watermarkHeight);
        }

        return $imageSource;
    }

    /**
     * Limits the image list if corresponding parameter is set
     *
     * @param $imagesLoaded
     * @param $imagesLoadedRest
     */
    private function limitImageList(&$imagesLoaded, &$imagesLoadedRest)
    {
        if ($this->pluginParameters['limit'] && (empty($this->pluginParameters['single']) || empty($this->pluginParameters['single_gallery']))) {
            $imagesLoadedRest = $imagesLoaded;

            if ($imagesLoaded > $this->pluginParameters['limit_quantity']) {
                $imagesLoaded = $this->pluginParameters['limit_quantity'];
            }
        }
    }

    /**
     * Creates and stores thumbnails of the original images
     *
     * @param $images
     * @param $imagesLoaded
     */
    private function createThumbnails($images, $imagesLoaded)
    {
        if (empty($this->pluginParameters['list']) && empty($this->pluginParameters['word'])) {
            $this->createEmptyDirectory($this->absolutePath . $this->rootFolder . $this->imagesDir . '/thumbs');

            for ($a = 0; $a < $imagesLoaded; $a++) {
                $this->createThumbnail($images[$a]['filename']);
            }
        }
    }

    /**
     * Creates a thumbnail from a specific image
     *
     * @param $image
     */
    private function createThumbnail($image)
    {
        if (!empty($image)) {
            $fileNameThumb = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/thumbs/' . $image;

            if ($this->pluginParameters['watermark']) {
                $fileNameThumb = $this->absolutePath . $this->rootFolder . $this->imagesDir . '/thumbs/' . $this->encryptImageName($image);
            }

            if (file_exists($fileNameThumb) == false || !empty($this->pluginParameters['thumbs_new'])) {
                $imageSourcePath = $this->createThumbnailSourceImagePath($image);
                $imageType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

                if ($imageType == 'jpg') {
                    $imageSource = imagecreatefromjpeg($imageSourcePath);
                    $imageThumb = $this->createThumbnailResize($image, $imageSource);
                    imagejpeg($imageThumb, $this->createThumbnailDestinationImagePath($image), $this->pluginParameters['quality']);
                } elseif ($imageType == 'png') {
                    $imageSource = imagecreatefrompng($imageSourcePath);
                    $imageThumb = $this->createThumbnailResize($image, $imageSource);
                    imagepng($imageThumb, $this->createThumbnailDestinationImagePath($image), $this->pluginParameters['quality_png']);
                } elseif ($imageType == 'gif') {
                    $imageSource = imagecreatefromgif($imageSourcePath);
                    $imageThumb = $this->createThumbnailResize($image, $imageSource);
                    imagegif($imageThumb, $this->createThumbnailDestinationImagePath($image));
                }

                if (isset($imageSource) && is_resource($imageSource)) {
                    imagedestroy($imageSource);
                }

                if (isset($imageThumb) && is_resource($imageThumb)) {
                    imagedestroy($imageThumb);
                }
            }
        }

        return;
    }

    /**
     * Creates the thumbnail source image path
     *
     * @param $image
     *
     * @return string
     */
    private function createThumbnailSourceImagePath($image)
    {
        if ($this->pluginParameters['watermark']) {
            return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/wm/' . $this->encryptImageName($image);
        }

        return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $image;
    }

    /**
     * Creates the resized thumbnail copy
     *
     * @param $image
     * @param $imageSource
     *
     * @return resource
     */
    private function createThumbnailResize($image, $imageSource)
    {
        list($heightNew, $widthNew) = $this->calculateSize($image, 1);

        $heightOriginal = imagesy($imageSource);
        $widthOriginal = imagesx($imageSource);
        $imageThumb = imagecreatetruecolor($widthNew, $heightNew);

        if ($this->pluginParameters['crop'] && ($this->pluginParameters['crop_factor'] > 0 && $this->pluginParameters['crop_factor'] < 100)) {
            list($cropWidth, $cropHeight, $xCoordinate, $yCoordinate) = $this->getCropInformation($widthOriginal, $heightOriginal);
            imagecopyresampled($imageThumb, $imageSource, 0, 0, $xCoordinate, $yCoordinate, $widthNew, $heightNew, $cropWidth, $cropHeight);

            return $imageThumb;
        }

        if ($this->pluginParameters['thumbdetail'] == 1) {
            imagecopyresampled($imageThumb, $imageSource, 0, 0, 0, 0, $widthNew, $heightNew, $widthNew, $heightNew);
        } elseif ($this->pluginParameters['thumbdetail'] == 2) {
            imagecopyresampled($imageThumb, $imageSource, 0, 0, $widthOriginal - $widthNew, 0, $widthNew, $heightNew, $widthNew, $heightNew);
        } elseif ($this->pluginParameters['thumbdetail'] == 3) {
            imagecopyresampled($imageThumb, $imageSource, 0, 0, 0, $heightOriginal - $heightNew, $widthNew, $heightNew, $widthNew, $heightNew);
        } elseif ($this->pluginParameters['thumbdetail'] == 4) {
            imagecopyresampled($imageThumb, $imageSource, 0, 0, $widthOriginal - $widthNew, $heightOriginal - $heightNew, $widthNew, $heightNew, $widthNew, $heightNew);
        } else {
            imagecopyresampled($imageThumb, $imageSource, 0, 0, 0, 0, $widthNew, $heightNew, $widthOriginal, $heightOriginal);
        }

        return $imageThumb;
    }

    /**
     * Gets correct crop information from specified parameter values
     *
     * @param $widthOriginal
     * @param $heightOriginal
     *
     * @return array
     */
    private function getCropInformation($widthOriginal, $heightOriginal)
    {
        $biggestSide = $heightOriginal;

        if ($widthOriginal > $heightOriginal) {
            $biggestSide = $widthOriginal;
        }

        $cropPercent = (1 - ($this->pluginParameters['crop_factor'] / 100));

        $cropWidth = $widthOriginal * $cropPercent;
        $cropHeight = $heightOriginal * $cropPercent;

        if (!$this->pluginParameters['ratio'] && ($this->pluginParameters['width'] == $this->pluginParameters['height'])) {
            $cropWidth = $biggestSide * $cropPercent;
            $cropHeight = $biggestSide * $cropPercent;
        } elseif (!$this->pluginParameters['ratio'] && ($this->pluginParameters['width'] != $this->pluginParameters['height'])) {
            $cropWidth = ($this->pluginParameters['width'] * ($heightOriginal / $this->pluginParameters['height'])) * $cropPercent;
            $cropHeight = $heightOriginal * $cropPercent;

            if (($widthOriginal / $this->pluginParameters['width']) < ($heightOriginal / $this->pluginParameters['height'])) {
                $cropWidth = $widthOriginal * $cropPercent;
                $cropHeight = ($this->pluginParameters['height'] * ($widthOriginal / $this->pluginParameters['width'])) * $cropPercent;
            }
        }

        $xCoordinate = ($widthOriginal - $cropWidth) / 2;
        $yCoordinate = ($heightOriginal - $cropHeight) / 2;

        return array($cropWidth, $cropHeight, $xCoordinate, $yCoordinate);
    }

    /**
     * Creates the thumbnail destination image path
     *
     * @param $image
     *
     * @return string
     */
    private function createThumbnailDestinationImagePath($image)
    {
        if ($this->pluginParameters['watermark']) {
            return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/thumbs/' . $this->encryptImageName($image);
        }

        return $this->absolutePath . $this->rootFolder . $this->imagesDir . '/thumbs/' . $image;
    }

    /**
     * Creates the dynamic Swipe class ID
     *
     * @return mixed
     */
    private function getSwipeClassId()
    {
        $swipeClassId = $this->sigCount;

        if ($this->pluginParameters['connect']) {
            $swipeClassId = $this->pluginParameters['connect'];
        }

        return $swipeClassId;
    }

    /**
     * Creates the HTML code for a specific image in the gallery
     *
     * @param $image
     * @param $html
     * @param $hidden
     * @param $fileInfo
     * @param $a
     */
    private function htmlImage($image, &$html, $hidden, &$fileInfo, $a)
    {
        if (!empty($image)) {
            $this->image = $image;
            $this->setImageInformation($fileInfo);

            if (!empty($hidden)) {
                $this->htmlImageHidden($html);

                return;
            }

            if ($this->pluginParameters['list'] && !$this->pluginParameters['word']) {
                $html .= '<li>';
            } elseif ($this->pluginParameters['word']) {
                $html .= '<span class="' . $this->getSingleElementClass(true) . '">';
            } else {
                $html .= '<li class="' . $this->getSingleElementClass() . '"><span class="sige_thumb">';
            }

            $this->htmlImageAnchorTag($html);

            if (!$this->pluginParameters['list'] && !$this->pluginParameters['word']) {
                $this->htmlImageImgTag($html);
            } elseif ($this->pluginParameters['list'] && !$this->pluginParameters['word']) {
                $html .= $this->imageInfo['image_title'];

                if (!empty($this->imageInfo['image_description'])) {
                    $html .= ' - ' . $this->imageInfo['image_description'];
                }
            } elseif ($this->pluginParameters['word']) {
                $html .= JText::_($this->pluginParameters['word']);
            }

            if ($this->pluginParameters['css_image'] && !$this->pluginParameters['image_link']) {
                $this->htmlImageImgTagCssImage($html);
            }

            if (!$this->pluginParameters['noslim'] || $this->pluginParameters['image_link'] || $this->pluginParameters['css_image'] || !empty($this->imageInfo['image_link_file'])) {
                $html .= '</a>';
            }

            if ($this->pluginParameters['caption']) {
                $this->htmlImageCaption($html);
            }

            if ($this->pluginParameters['list'] && !$this->pluginParameters['word']) {
                $html .= '</li>';
            } elseif ($this->pluginParameters['word']) {
                $html .= '</span>';
            } elseif (!$this->pluginParameters['caption']) {
                $html .= '</span></li>';
            }
        }

        if ($this->pluginParameters['column_quantity']) {
            if (($a + 1) % $this->pluginParameters['column_quantity'] == 0) {
                $html .= '<br class="sige_clr"/>';
            }
        }
    }

    /**
     * Sets image information and converts special characters to HTML entities
     *
     * @param $fileInfo
     */
    private function setImageInformation(&$fileInfo)
    {
        $this->imageInfo = array(
            'image_hash'        => $this->encryptImageName($this->image),
            'image_title'       => pathinfo($this->image, PATHINFO_FILENAME),
            'image_alt'         => pathinfo($this->image, PATHINFO_FILENAME),
            'image_description' => '',
            'image_link_file'   => '',
        );

        if (!empty($fileInfo)) {
            $this->htmlImageFileInfo($fileInfo);
        }

        if ($this->pluginParameters['iptc'] == 1) {
            $this->iptcInfo();
        }

        $this->imageInfo = array_map(array($this, 'cleanImageInformation'), $this->imageInfo);
    }

    /**
     * Defines file info information if provided for the loaded image
     *
     * @param $fileInfo
     */
    private function htmlImageFileInfo(&$fileInfo)
    {
        foreach ($fileInfo as $key => $value) {
            if ($value[0] == $this->image) {
                // Image title
                if (!empty($value[1])) {
                    $this->imageInfo['image_title'] = $value[1];
                }

                // Image description
                if (!empty($value[2])) {
                    $this->imageInfo['image_description'] = $value[2];
                }

                // Alt attribute for image
                if (!empty($value[3])) {
                    $this->imageInfo['image_alt'] = $value[3];
                }

                // Link for image
                if (!empty($value[4])) {
                    $this->imageInfo['image_link_file'] = $value[4];
                }

                // Remove information from file_info array to speed up the process for the following images
                unset($fileInfo[$key]);
                break;
            }
        }
    }

    /**
     * Sets IPTC information if set and provided
     */
    private function iptcInfo()
    {
        $iptcTitle = '';
        $iptcCaption = '';
        $info = array();

        getimagesize(JPATH_SITE . $this->rootFolder . $this->imagesDir . '/' . $this->image, $info);

        if (isset($info['APP13'])) {
            $iptcPhp = iptcparse($info['APP13']);

            if (is_array($iptcPhp)) {
                $data = array('caption' => '', 'title' => '');

                if (isset($iptcPhp["2#120"][0])) {
                    $data['caption'] = $iptcPhp["2#120"][0];
                }

                if (isset($iptcPhp["2#005"][0])) {
                    $data['title'] = $iptcPhp["2#005"][0];
                }

                $iptcTitle = utf8_encode(html_entity_decode($data['title'], ENT_NOQUOTES));
                $iptcCaption = utf8_encode(html_entity_decode($data['caption'], ENT_NOQUOTES));

                if ($this->pluginParameters['iptcutf8'] == 1) {
                    $iptcTitle = html_entity_decode($data['title'], ENT_NOQUOTES);
                    $iptcCaption = html_entity_decode($data['caption'], ENT_NOQUOTES);
                }
            }
        }

        if (!empty($iptcTitle)) {
            $this->imageInfo['image_title'] = $iptcTitle;
        }

        if (!empty($iptcCaption)) {
            $this->imageInfo['image_description'] = $iptcCaption;
        }
    }

    /**
     * Creates the hidden output for the gallery - e.g. used in the lightbox gallery view
     *
     * @param $html
     *
     * @return string
     */
    private function htmlImageHidden(&$html)
    {
        $singleElementClass = $this->getSingleElementClass(true);

        if (!$this->pluginParameters['noslim']) {
            if ($this->pluginParameters['watermark']) {
                $html .= '<span class="sige_hidden ' . $singleElementClass . '"><a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'] . '"';
            } else {
                if ($this->pluginParameters['resize_images']) {
                    $html .= '<span class="sige_hidden ' . $singleElementClass . '"><a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image . '"';
                } else {
                    $html .= '<span class="sige_hidden ' . $singleElementClass . '"><a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/' . $this->image . '"';
                }
            }

            if ($this->pluginParameters['view'] == 7) {
                $html .= ' data-size="' . $this->getDataSizeAttribute() . '"';
            }

            $this->htmlImageRelAttribute($html);

            $html .= ' title="';

            $this->htmlImageAddTitleAttribute($html);

            $html .= '"></a></span>';
        }

        return $html;
    }

    /**
     * Gets the CSS class for a single image element
     *
     * @return string
     */
    private function getSingleElementClass($swipeOnly = false)
    {
        $class = '';

        if (!$swipeOnly) {
            $class = 'sige_cont_' . $this->sigCount;
        }

        // PhotoSwipe - Add specific class for the PhotoSwipe library
        if ($this->pluginParameters['view'] == 7) {
            $swipeClassId = $this->sigCount;

            if ($this->pluginParameters['connect']) {
                $swipeClassId = $this->pluginParameters['connect'];
            }

            $class .= ' sige_swipe_single_' . $swipeClassId;
        }

        return $class;
    }

    /**
     * Gets the correct image sizes for the data-size attribute, required by PhotoSwipe
     *
     * @return string
     */
    private function getDataSizeAttribute()
    {
        $imageSize = getimagesize($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $this->image);

        if (empty($imageSize) || !is_array($imageSize)) {
            return '';
        }

        return $imageSize[0] . 'x' . $imageSize[1];
    }

    /**
     * Creates the image rel attribute code for a specific image
     *
     * @param $html
     */
    private function htmlImageRelAttribute(&$html)
    {
        if ($this->pluginParameters['connect']) {
            if ($this->pluginParameters['view'] == 0 || $this->pluginParameters['view'] == 5) {
                $html .= ' rel="lightbox.sig' . $this->pluginParameters['connect'] . '"';
            } elseif ($this->pluginParameters['view'] == 1) {
                $html .= ' rel="lytebox.sig' . $this->pluginParameters['connect'] . '"';
            } elseif ($this->pluginParameters['view'] == 2) {
                $html .= ' rel="lyteshow.sig' . $this->pluginParameters['connect'] . '"';
            } elseif ($this->pluginParameters['view'] == 3) {
                $html .= ' rel="shadowbox[sig' . $this->pluginParameters['connect'] . ']"';
            } elseif ($this->pluginParameters['view'] == 4) {
                $html .= ' data-milkbox="milkbox-' . $this->pluginParameters['connect'] . '"';
            } elseif ($this->pluginParameters['view'] == 6) {
                $html .= ' class="venobox" data-gall="venobox-' . $this->pluginParameters['connect'] . '"';
            }

            return;
        }

        if ($this->pluginParameters['view'] == 0 || $this->pluginParameters['view'] == 5) {
            $html .= ' rel="lightbox.sig' . $this->sigCount . '"';
        } elseif ($this->pluginParameters['view'] == 1) {
            $html .= ' rel="lytebox.sig' . $this->sigCount . '"';
        } elseif ($this->pluginParameters['view'] == 2) {
            $html .= ' rel="lyteshow.sig' . $this->sigCount . '"';
        } elseif ($this->pluginParameters['view'] == 3) {
            $html .= ' rel="shadowbox[sig' . $this->sigCount . ']"';
        } elseif ($this->pluginParameters['view'] == 4) {
            $html .= ' data-milkbox="milkbox-' . $this->sigCount . '"';
        } elseif ($this->pluginParameters['view'] == 6) {
            $html .= ' class="venobox" data-gall="venobox-' . $this->sigCount . '"';
        }
    }

    /**
     * Creates the title attribute data for a specific image
     *
     * @param $html
     */
    private function htmlImageAddTitleAttribute(&$html)
    {
        if ($this->pluginParameters['displaynavtip'] && !empty($this->pluginParameters['navtip'])) {
            $html .= $this->pluginParameters['navtip'] . '&lt;br /&gt;';
        }

        if ($this->pluginParameters['displaymessage'] && !empty($this->articleTitle)) {
            if (!empty($this->pluginParameters['message'])) {
                $html .= $this->pluginParameters['message'] . ': ';
            }

            $html .= '&lt;span class=&quot;sige_js_title&quot;&gt;' . $this->articleTitle . '&lt;/span&gt;&lt;br /&gt;';
        }

        if ($this->pluginParameters['image_info']) {
            $html .= '&lt;span class=&quot;sige_js_title&quot;&gt;' . $this->imageInfo['image_title'] . '&lt;/span&gt;';

            if (!empty($this->imageInfo['image_description'])) {
                $html .= ' - ' . $this->imageInfo['image_description'];
            }
        }

        if ($this->pluginParameters['print'] == 1) {
            $html .= ' &lt;a href=&quot;' . $this->liveSite . '/plugins/content/sige/plugin_sige/print.php?img=' . rawurlencode($this->htmlImagePrintPath()) . '&amp;name=' . rawurlencode($this->imageInfo['image_title']) . '&quot; title=&quot;Print&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;' . $this->liveSite . '/plugins/content/sige/plugin_sige/print.png&quot; /&gt;&lt;/a&gt;';
        }

        if ($this->pluginParameters['download'] == 1) {
            $html .= ' &lt;a href=&quot;' . $this->liveSite . '/plugins/content/sige/plugin_sige/download.php?img=' . rawurlencode($this->htmlImageDownloadPath()) . '&quot; title=&quot;Download&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;' . $this->liveSite . '/plugins/content/sige/plugin_sige/download.png&quot; /&gt;&lt;/a&gt;';
        }
    }

    /**
     * Returns the correct print path
     *
     * @return string
     */
    private function htmlImagePrintPath()
    {
        if ($this->pluginParameters['watermark']) {
            return $this->liveSite . $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'];
        }

        if ($this->pluginParameters['resize_images']) {
            return $this->liveSite . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image;
        }

        return $this->liveSite . $this->rootFolder . $this->imagesDir . '/' . $this->image;
    }

    /**
     * Returns the correct download path
     *
     * @return string
     */
    private function htmlImageDownloadPath()
    {
        if ($this->pluginParameters['watermark']) {
            return $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'];
        }

        if ($this->pluginParameters['resize_images']) {
            return $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image;
        }

        return $this->rootFolder . $this->imagesDir . '/' . $this->image;
    }

    /**
     * Creates the anchor tag code for a specific image
     *
     * @param $html
     */
    private function htmlImageAnchorTag(&$html)
    {
        if ($this->pluginParameters['image_link'] || !empty($this->imageInfo['image_link_file'])) {
            // Use link from captions.txt if provided
            if (!empty($this->imageInfo['image_link_file'])) {
                // Add http:// if not already set
                if (!preg_match('@http.?://@', $this->imageInfo['image_link_file'])) {
                    $this->imageInfo['image_link_file'] = 'http://' . $this->imageInfo['image_link_file'];
                }

                $html .= '<a href="' . $this->imageInfo['image_link_file'] . '" title="' . $this->imageInfo['image_link_file'] . '" ';
            } else {
                $html .= '<a href="http://' . $this->pluginParameters['image_link'] . '" title="' . $this->pluginParameters['image_link'] . '" ';
            }

            if ($this->pluginParameters['image_link_new']) {
                $html .= 'target="_blank"';
            }

            $html .= '>';

            return;
        }

        if ($this->pluginParameters['noslim'] && $this->pluginParameters['css_image']) {
            $html .= '<a class="sige_css_image" href="#sige_thumbnail">';

            return;
        }

        if (!$this->pluginParameters['noslim']) {
            if ($this->pluginParameters['watermark']) {
                $html .= '<a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'] . '"';
            } elseif ($this->pluginParameters['resize_images']) {
                $html .= '<a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image . '"';
            } else {
                $html .= '<a href="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/' . $this->image . '"';
            }

            if ($this->pluginParameters['css_image']) {
                $html .= ' class="sige_css_image';

                // Add Venobox class if this JS application is selected
                if ($this->pluginParameters['view'] == 6) {
                    $html .= ' venobox';
                }

                $html .= '"';
            }

            if ($this->pluginParameters['view'] == 7) {
                $html .= ' data-size="' . $this->getDataSizeAttribute() . '"';
            }

            $this->htmlImageRelAttribute($html);

            $modalTitle = ' title="';

            if (!empty($this->pluginParameters['modaltitle'])) {
                $modalTitle = ' title="' . $this->imageInfo['image_title'] . '" ' . $this->pluginParameters['modaltitle'] . '="';
            } elseif ($this->pluginParameters['view'] == 7) {
                $modalTitle = ' title="' . $this->imageInfo['image_title'] . '" data-title="';
            }

            $html .= $modalTitle;
            $this->htmlImageAddTitleAttribute($html);
            $html .= '" >';

            return;
        }
    }

    /**
     * Creates the image tag code for a specific image
     *
     * @param $html
     */
    private function htmlImageImgTag(&$html)
    {
        if ($this->pluginParameters['thumbs']) {
            $html .= '<img alt="' . $this->imageInfo['image_alt'] . '" title="' . $this->imageInfo['image_title'];

            if (!empty($this->imageInfo['image_description'])) {
                $html .= ' - ' . $this->imageInfo['image_description'];
            }

            if ($this->pluginParameters['watermark']) {
                $html .= '" src="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/thumbs/' . $this->imageInfo['image_hash'] . '" />';
            } else {
                $html .= '" src="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/thumbs/' . $this->image . '" />';
            }

            return;
        }

        $this->htmlImageImgTagDynamic($html);
    }

    /**
     * Creates the image tag code for a specific image using on-the-fly thumbnail generation
     *
     * @param $html
     */
    private function htmlImageImgTagDynamic(&$html)
    {
        $html .= '<img alt="' . $this->imageInfo['image_alt'] . '" title="' . $this->imageInfo['image_title'];

        if ($this->imageInfo['image_description']) {
            $html .= ' - ' . $this->imageInfo['image_description'];
        }

        if ($this->pluginParameters['watermark']) {
            $html .= '" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/showthumb.php?img=' . $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'] . '&amp;width=' . $this->pluginParameters['width'] . '&amp;height=' . $this->pluginParameters['height'] . '&amp;quality=' . $this->pluginParameters['quality'] . '&amp;ratio=' . $this->pluginParameters['ratio'] . '&amp;crop=' . $this->pluginParameters['crop'] . '&amp;crop_factor=' . $this->pluginParameters['crop_factor'] . '&amp;thumbdetail=' . $this->pluginParameters['thumbdetail'] . '" />';

            return;
        }

        if ($this->pluginParameters['resize_images']) {
            $html .= '" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/showthumb.php?img=' . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image . '&amp;width=' . $this->pluginParameters['width'] . '&amp;height=' . $this->pluginParameters['height'] . '&amp;quality=' . $this->pluginParameters['quality'] . '&amp;ratio=' . $this->pluginParameters['ratio'] . '&amp;crop=' . $this->pluginParameters['crop'] . '&amp;crop_factor=' . $this->pluginParameters['crop_factor'] . '&amp;thumbdetail=' . $this->pluginParameters['thumbdetail'] . '" />';

            return;
        }

        $html .= '" src="' . $this->liveSite . '/plugins/content/sige/plugin_sige/showthumb.php?img=' . $this->rootFolder . $this->imagesDir . '/' . $this->image . '&amp;width=' . $this->pluginParameters['width'] . '&amp;height=' . $this->pluginParameters['height'] . '&amp;quality=' . $this->pluginParameters['quality'] . '&amp;ratio=' . $this->pluginParameters['ratio'] . '&amp;crop=' . $this->pluginParameters['crop'] . '&amp;crop_factor=' . $this->pluginParameters['crop_factor'] . '&amp;thumbdetail=' . $this->pluginParameters['thumbdetail'] . '" />';
    }

    /**
     * Creates the image tag code for a specific image using the CSS image tooltip
     *
     * @param $html
     */
    private function htmlImageImgTagCssImage(&$html)
    {
        $html .= '<span>';

        if ($this->pluginParameters['watermark']) {
            $html .= '<img src="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/wm/' . $this->imageInfo['image_hash'] . '"';
        } else {
            if ($this->pluginParameters['resize_images']) {
                $html .= '<img src="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/resizedimages/' . $this->image . '"';
            } else {
                $html .= '<img src="' . $this->liveSite . $this->rootFolder . $this->imagesDir . '/' . $this->image . '"';
            }
        }

        if ($this->pluginParameters['css_image_half'] && !$this->pluginParameters['list']) {
            $imageData = getimagesize($this->absolutePath . $this->rootFolder . $this->imagesDir . '/' . $this->image);
            $html .= ' width="' . ($imageData[0] / 2) . '" height="' . ($imageData[1] / 2) . '"';
        }

        $html .= ' alt="' . $this->imageInfo['image_alt'] . '" title="' . $this->imageInfo['image_title'];

        if ($this->imageInfo['image_description']) {
            $html .= ' - ' . $this->imageInfo['image_description'];
        }

        $html .= '" /></span>';
    }

    /**
     * Adds image caption to a specific image
     *
     * @param $html
     */
    private function htmlImageCaption(&$html)
    {
        if (!$this->pluginParameters['list'] && !$this->pluginParameters['word']) {
            if ($this->pluginParameters['single'] && !empty($this->pluginParameters['scaption'])) {
                $html .= '</span><span class="sige_caption">' . $this->pluginParameters['scaption'] . '</span></li>';
            } else {
                $html .= '</span><span class="sige_caption">' . $this->imageInfo['image_title'] . '</span></li>';
            }
        }
    }

    /**
     * Loads PhotoSwipe dynamic JavaScript code from the cache file
     */
    private function loadPhotoSwipeJs()
    {
        if (file_exists($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_js-' . $this->pluginParameters['lang'] . '.txt')) {
            $photoswipeJs = file_get_contents($this->absolutePath . $this->rootFolder . $this->imagesDir . '/sige_turbo_js-' . $this->pluginParameters['lang'] . '.txt');
            $this->photoswipeJs[] = $photoswipeJs;
        }
    }

    /**
     * Adds data like the PhotoSwipe template and JS start code to the end of the article
     *
     * @param $article
     */
    private function addDataToBottom(&$article)
    {
        $data = '';

        if ($this->pluginParameters['view'] == 7) {
            $data .= file_get_contents(__DIR__ . '/plugin_sige/photoswipe/pswp.txt');

            if (!empty($this->photoswipeJs)) {
                $data .= '<script type="text/javascript">' . implode("\n", $this->photoswipeJs) . '</script>';
            }
        }

        if (!empty($data)) {
            $article .= $data;
        }
    }

    /**
     * Cleans the image information - removes HTML tags and converts special characters
     *
     * @param $value
     *
     * @return string
     */
    private function cleanImageInformation($value)
    {
        return htmlspecialchars(strip_tags($value));
    }

    /**
     * Compares timestamps of images - ascending
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function timeasc($a, $b)
    {
        return strcmp($a['timestamp'], $b['timestamp']);
    }

    /**
     * Compares timestamps of images - descending
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function timedesc($a, $b)
    {
        return strcmp($b['timestamp'], $a['timestamp']);
    }
}
