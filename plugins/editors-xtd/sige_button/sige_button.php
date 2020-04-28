<?php
/**
 * @Copyright
 * @package    Editor Button - SIGE Parameter Button - Editor Plugin for Joomla! 3
 * @author     Viktor Vogel <admin@kubik-rubik.de>
 * @version    3.2.1 - 2019-08-07
 * @link       https://kubik-rubik.de/sige-simple-image-gallery-extended
 *
 * @license    GNU/GPL
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
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') || die('Restricted access');

class PlgButtonSige_Button extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onDisplay($name)
    {
        JPlugin::loadLanguage('plg_editors-xtd_sige_button', JPATH_ADMINISTRATOR);
        $method = $this->params->get('method', 0);

        if ($method == 0) {
            $params = array();

            $paramsBool = array(
                'displaynavtip',
                'displayarticle',
                'thumbs',
                'limit',
                'noslim',
                'root',
                'ratio',
                'caption',
                'iptc',
                'iptcutf8',
                'print',
                'single_gallery',
                'download',
                'list',
                'crop',
                'watermark',
                'image_info',
                'image_link_new',
                'css_image',
                'css_image_half',
                'copyright',
                'calcmaxthumbsize',
                'fileinfo',
                'resize_images',
                'ratio_image',
                'images_new',
            );

            foreach ($paramsBool as $paramBool) {
                $this->setParamsBoolean($params, $paramBool);
            }

            $paramsValue = array(
                'width',
                'height',
                'gap_v',
                'gap_h',
                'quality',
                'quality_png',
                'limit_quantity',
                'sort',
                'single',
                'scaption',
                'connect',
                'crop_factor',
                'thumbdetail',
                'watermarkposition',
                'watermarkimage',
                'encrypt',
                'image_link',
                'column_quantity',
                'word',
                'width_image',
                'height_image',
            );

            foreach ($paramsValue as $paramValue) {
                $this->setParamsValue($params, $paramValue);
            }

            $paramsSpecial = array('salign', 'turbo');

            foreach ($paramsSpecial as $paramSpecial) {
                $this->setParamsSpecial($params, $paramSpecial);
            }

            sort($params);
            $this->getImageFolder($params);
            $params = '{gallery}' . implode(",", $params) . '{/gallery}';

            $getContent = $this->_subject->getContent($name);
            $js = "function sige_button(editor) {var content = $getContent; jInsertEditorText('$params', editor);}";
            JFactory::getDocument()->addScriptDeclaration($js);

            $button = new JObject();
            $button->set('modal', false);
            $button->set('class', 'btn');
            $button->set('onclick', 'sige_button(\'' . $name . '\');return false;');
            $button->set('text', JText::_('PLG_SIGE_BUTTON_SIGEBUTTONTEXT'));
            $button->set('name', 'camera');
            $button->set('link', '#');

            return $button;
        }

        $lang = JFactory::getLanguage();
        $folderInput = $this->params->get('folder_input');
        $readInFolder = $this->params->get('read_in_folder');
        $token = md5($this->params->get('token'));
        $link = '';

        if (JFactory::getApplication()->isAdmin()) {
            $link .= '../';
        }

        $link .= 'plugins/editors-xtd/sige_button/sige_button.html.php?lang=' . $lang->getTag() . '&amp;e_name=' . $name . '&amp;folder_input=' . $folderInput . '&amp;read_in_folder=' . $readInFolder . '&amp;token=' . $token;

        $button = new JObject();
        $button->set('modal', true);
        $button->set('class', 'btn');
        $button->set('link', $link);
        $button->set('text', JText::_('PLG_SIGE_BUTTON_SIGEBUTTONTEXT'));
        $button->set('name', 'camera');

        $heightModal = 550;

        if ($method == 2) {
            $heightModal = 100;
        }

        $button->set('options', "{handler: 'iframe', size: {x: 800, y: " . $heightModal . "}}");

        return $button;
    }

    /**
     * Sets the boolean parameters
     *
     * @param $params
     * @param $paramBool
     */
    private function setParamsBoolean(&$params, $paramBool)
    {
        $value = $this->params->get($paramBool, false);

        if (!empty($value)) {
            if ($value == 1) {
                $params[] = $paramBool . '=1';
            } elseif ($value == 2) {
                $params[] = $paramBool . '=0';
            }
        }
    }

    /**
     * Sets the value parameters
     *
     * @param $params
     * @param $paramValue
     */
    private function setParamsValue(&$params, $paramValue)
    {
        $value = $this->params->get($paramValue, false);

        if (!empty($value)) {
            $params[] = $paramValue . '=' . $value;
        }
    }

    /**
     * Sets the special parameters
     *
     * @param $params
     * @param $paramSpecial
     */
    private function setParamsSpecial(&$params, $paramSpecial)
    {
        $value = $this->params->get($paramSpecial, false);

        if ($paramSpecial == 'salign') {
            if ($value == 1) {
                $params[] = $paramSpecial . '=left';
            } elseif ($value == 2) {
                $params[] = $paramSpecial . '=right';
            } elseif ($value == 3) {
                $params[] = $paramSpecial . '=center';
            }

            return;
        }

        if ($paramSpecial == 'turbo') {
            if ($value == 1) {
                $params[] = $paramSpecial . '=1';
            } elseif ($value == 2) {
                $params[] = $paramSpecial . '=0';
            } elseif ($value == 3) {
                $params[] = $paramSpecial . '=new';
            }

            return;
        }
    }

    /**
     * Gets the image folder path or set a default name from the language file
     *
     * @param $params
     */
    private function getImageFolder(&$params)
    {
        $readInFolder = $this->params->get('read_in_folder');

        if (!empty($readInFolder)) {
            $root = $this->params->get('root');

            if ($root != 1) {
                if (stripos($readInFolder, 'images/') === 0) {
                    $readInFolder = substr($readInFolder, 7);
                }
            }

            array_unshift($params, $readInFolder);

            return;
        }

        array_unshift($params, JText::_('PLG_SIGE_BUTTON_FOLDER'));
    }
}
