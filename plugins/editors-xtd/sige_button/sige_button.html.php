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
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__) . '/../../..');

use Joomla\Registry\Registry;

// Deactivate error reporting to suppress possible warnings
ini_set('error_reporting', 0);

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

$input = JFactory::getApplication('site')->input;
$plugin = JPluginHelper::getPlugin('editors-xtd', 'sige_button');
$plugin_params = new Registry($plugin->params);

$token = $input->get('token', 'd41d8cd98f00b204e9800998ecf8427e', 'ALNUM');
$lang = $input->get('lang', 'en-GB');

// Exit if token is empty or like example in the language files ;-)
if ($token == 'd41d8cd98f00b204e9800998ecf8427e' OR $token == '01bc57e95dd5b885db2e4db90cf18a3a') {
    $message = 'No token established! You must enter a token to restrict the possibility to access the file from the outside.  Without this security measure, someone else could read the folder structure of the server from outside the plugin. Please set up in the plugin settings.';

    if ($lang == 'de-DE') {
        $message = 'Token nicht festgelegt! Es muss ein Token eingegeben werden, um den Aufruf der Datei von außen zu unterbinden. Ohne Absicherung könnten Dritte von außen die Verzeichnisstruktur des Servers auslesen. Bitte in den Plugineinstellungen einrichten.';
    }

    jexit($message);
}

// Token is not correct
if ($token != md5($plugin_params->get('token'))) {
    $message = 'Token wrong!';

    if ($lang == 'de-DE') {
        $message = 'Token falsch!';
    }

    jexit($message);
}

$e_name = $input->get('e_name', '');
$folder_input = $input->get('folder_input', 0, 'INTEGER');
$read_in_folder = $input->get('read_in_folder', 'images', 'PATH');
$method = $plugin_params->get('method', 0);

if ($lang == 'de-DE') {
    $folder = 'Bildordner';
    $yes = 'Ja';
    $no = 'Nein';
    $notuse = 'Nicht verwenden';
    $left = 'Links';
    $right = 'Rechts';
    $topleft = 'Oben links';
    $topright = 'Oben rechts';
    $bottomleft = 'Unten links';
    $bottomright = 'Unten rechts';
    $center = 'Zentriert';
    $noasc = 'Aufsteigend';
    $nodesc = 'Absteigend';
    $timeasc = 'Änderungsdatum aufsteigend';
    $timedesc = 'Änderungsdatum absteigend';
    $sortfromfile = 'Sortierung aus der Informationsdatei';
    $insert = 'Einfügen';
    $size = 'Größe';
    $gallery = 'Galerie';
    $js_view = 'JS-Ansicht';
    $thumbnail = 'Thumbnail';
    $single_image = 'Einzelbild';
    $select = 'Bitte auswählen:';
    $selectdesc = 'Klicken Sie auf die Links, um die gewünschten Parameter zu setzen!';
    $folderdesc = 'Bildordner angeben. Wurde Option \'root\' nicht gesetzt, dann muss sich der angegeben Ordner unter images/stories befinden!';
    $widththumbsdesc = 'Maximale Breite der Thumbnails angeben. Leer lassen, wenn nicht benötigt!';
    $heightthumbsdesc = 'Maximale Höhe der Thumbnails angeben. Leer lassen, wenn nicht benötigt!';
    $gapvdesc = 'Vertikaler Abstand der Bilder. Leer lassen, wenn nicht benötigt!';
    $gaphdesc = 'Horizontaler Abstand der Bilder. Leer lassen, wenn nicht benötigt!';
    $qualityjpgdesc = 'Qualität jpg-Format. Qualität in Prozent (ohne %) von 0 - 100. Empfohlen: 80. Leer lassen, wenn nicht benötigt!';
    $qualitypngdesc = 'Qualität png-Format. Kompressionsstufe: 0 (keine Kompression) - 9. Empfohlen: 6. Leer lassen, wenn nicht benötigt!';
    $displaynavtipdesc = 'Navigationstipp einblenden?';
    $displaymessagedesc = 'Soll der Artikelname eingeblendet werden?';
    $thumbsdesc = 'Thumbnails generieren und laden? Thumbnails werden generiert und geladen, was das Laden wesentlich beschleunigt. Thumbnails werden im Ordner \'thumbs\' im gewählten Ordner abgelegt.';
    $limitdesc = 'Anzahl der angezeigten Bilder limitieren?';
    $limitquantitydesc = 'Anzahl der angezeigten Bilder. Leer lassen, wenn nicht benötigt!';
    $noslimdesc = 'Bilder ohne Slimbox-Effekt anzeigen? Mit dieser Option kann man Bilder ganz normal anzeigen lassen (ohne Verlinkung).';
    $sortdesc = 'Sortierung der Bilder auswählen. Bei \'Sortierung aus der Informationsdatei\' wird die Reihenfolge aus der Informationsdatei (z.B.: captions.txt) ausgelesen, alle anderen Bilder, die nicht angegeben sind, werden in der Galerie nicht angezeigt. Um diese Option nutzen zu können, muss die Option "Informationen aus Textdatei (Info)" aktiviert und eine Informationsdatei im Bildordner zur Verfügung gestellt werden. Siehe Projektseite für mehr Informationen!';
    $sortrandom = 'Zufällig';
    $rootdesc = 'Bildordner ausgehend vom Rootverzeichnis? Pfad zum Bildordner vom Rootverzeichnis, nicht images/stories';
    $ratiodesc = 'Sollen die Seitenverhältnisse beibehalten werden?';
    $captiondesc = 'Bildunterschrift anzeigen?';
    $iptcdesc = 'IPTC einlesen? Titel und Unterschrift wird aus den IPTC-Werten (Title und Description) generiert!';
    $iptcutf8desc = 'IPTC utf8-kodiert? Sind die Daten bereits utf8-kodiert?';
    $printdesc = 'Druckbutton anzeigen? Das angezeigte Bild lässt sich über einen Link in der Slimbox / Lytebox ganz einfach ausdrucken!';
    $singledesc = 'Einzelbild anzeigen? Hier den Namen des Einzelbildes (mit Endung) eingeben. Leer lassen, wenn nicht benötigt!';
    $singlegallerydesc = 'Restliche Bilder im Ordner in der Galerie bei gewähltem Einzelbild laden?';
    $saligndesc = 'Ausrichtung eines Einzelbildes.';
    $connectdesc = 'Unterschiedliche Galerien (je Syntaxaufruf) miteinander verbinden. Eingabe zum Beispiel: bildset. Somit werden alle Syntaxaufrufe, die diesen Paramater beinhalten in der Galerie zusammengefasst. Leer lassen, wenn nicht benötigt!';
    $downloaddesc = 'Downloadbutton anzeigen? Mit einem Klick kann das angezeigte Bild runtergeladen werden.';
    $listdesc = 'Bilder als Liste anzeigen. Bilder werden nicht als Thumnbnails angezeigt, sondern in einer Liste aufgezählt.';
    $cropdesc = 'Crop - Bildausschnitt? Soll das Bild beschnitten werden? Es wird nur ein Bildausschnitt als Thumbnail angezeigt. Dadurch lassen sich einheitliche Vorschaubilder erstellen.';
    $cropfactordesc = 'Crop-Faktor einstellen. Hier wird der -Zoom-Faktor- eingestellt. Angaben sind in Prozent, jedoch ohne %-Zeichen. Beispiel: 50 für 50 Prozent Zoom. Mögliche Eingabe: 1-99. Leer lassen, wenn nicht benötigt!';
    $thumbnaildetaildesc = 'Bildausschnitt für Thumbnail. Soll das ganze Bild verkleinert werden oder nur ein Teil des Originalbildes? Crop-Option muss deaktiviert sein!';
    $watermarkdesc = 'Wasserzeichen - Soll ein Wasserzeichen auf die Bilder gesetzt werden? Bild watermark.png unter plugins/content/plugin_sige mit einem eigenen austauschen!';
    $watermarkpositiondesc = 'Position des Wasserzeichens.';
    $watermarktransdesc = 'Transparenz des Wasserzeichens. Werte zwischen 0-100 möglich. 0 - Undurchsichtig --- 100 - komplett durchsichtig. Leer lassen, wenn nicht benötigt!';
    $encryptdesc = 'Verschlüsselungsmethode für Bildnamen. Um Bildnamen der Originalbilder zu verschleiern. ROT13 - sehr schwach, aber schnell. MD5 - sicher, relativ schnell. SHA1 - sehr sicher, langsamer als MD5. Empfehlung: MD5';
    $encryptNoneDesc = 'Keine';
    $imageinfodesc = 'Bildinformation anzeigen? Sollen der Bildname oder die IPTC-Werte angezeigt werden?';
    $imagelinkdesc = 'Link setzen - Einen Link auf eine beliebige Website setzen. Achtung: JS-Ansicht und Tooltip werden deaktiviert! Eingabe in der Form: www.kubik-rubik.de';
    $imagelinknewdesc = 'Link in neuem Fenster öffnen.';
    $columnquantitydesc = 'Zeilenumbruch nach Anzahl Bildern. Soll ein Zeilenumbruch nach einer bestimmten Anzahl an Bildern pro Zeile erfolgen? Wenn nicht, dann leer lassen. Leer lassen, wenn nicht benötigt!';
    $cssimagedesc = 'CSS Bildertooltip - Das Bild wird in einem Tooltip beim Überfahren der Maus angezeigt.';
    $cssimagehalfdesc = 'Halbe Größe im Tooltip. Ist das Originalbild zu groß, kann mit dieser Einstellung nur die halbe Größe angezeigt werden.';
    $copyrightdesc = 'Link \'Simple Image Gallery Extended\' anzeigen? Danke für die Unterstützung! Tipp: Bei mehreren Galerieaufrufen auf einer Seite kann der Link global deaktiviert und über diesen Parameter in einer Galerie gesetzt werden.';
    $watermarkimagedesc = 'Ein anderes Wasserzeichen wählen (Bild muss unter plugins/content/sige/plugin_sige liegen) - Beispiel: watermark-new.png';
    $worddesc = 'Galerie mit einem Wort verlinken - Beispiel: Galerie';
    $scaptiondesc = 'Bildunterschrift bei Einzelbild - nur in Verbindung mit dem Parameter single zu verwenden!';
    $calcmaxthumbsizedesc = 'Maximale Abmessungen aller Thumbnails berechnen.';
    $fileinfodesc = 'Informationen aus Textdatei (captions.txt).';
    $turbodesc = 'Turbomodus aktivieren.';
    $resize_imagesdesc = 'Verkleinerung der Originalbilder aktivieren.';
    $width_imagedesc = 'Maximale Breite der verkleinerten Originalbilder. Leer lassen, wenn nicht benötigt!';
    $height_imagedesc = 'Maximale Höhe der verkleinerten Originalbilder. Leer lassen, wenn nicht benötigt!';
    $ratio_imagedesc = 'Seitenverhältnisse bei der Umwandlung der Originalbilder beibehalten.';
    $images_newdesc = 'Bereits erzeugte verkleinerte Originalbilder überschreiben.';
} else {
    $folder = 'Folder';
    $yes = 'Yes';
    $no = 'No';
    $notuse = 'Don\'t use';
    $left = 'Left';
    $right = 'Right';
    $topleft = 'Top left';
    $topright = 'Top right';
    $bottomleft = 'Bottom left';
    $bottomright = 'Bottom right';
    $center = 'Center';
    $noasc = 'Ascending';
    $nodesc = 'Descending';
    $timeasc = 'Modified ascending';
    $timedesc = 'Modified descending';
    $sortfromfile = 'Sorting from information file';
    $insert = 'Insert';
    $size = 'Size';
    $gallery = 'Gallery';
    $js_view = 'JS-View';
    $thumbnail = 'Thumbnail';
    $single_image = 'Single Image';
    $select = 'Please select:';
    $selectdesc = 'Click on the links to set the required parameters!';
    $folderdesc = 'Specify images folder. If option \'root\' is not set, then the folder must be under images / stories!';
    $widththumbsdesc = 'Adjust the maximum image thumbnail width in pixels. Leave blank if not required!';
    $heightthumbsdesc = 'Adjust the maximum image thumbnail height in pixels. Leave blank if not required!';
    $gapvdesc = 'Vertical gap between the images. Leave blank if not required!';
    $gaphdesc = 'Horizontal gap between the images. Leave blank if not required!';
    $qualityjpgdesc = 'Image thumbnail quality (jpg). Adjust the quality of the generated image thumbnail. Values range from 0 to 100, with 100 giving the best possible result. Values between 70 to 80 should be OK, too! Leave blank if not required!';
    $qualitypngdesc = 'Image thumbnail quality (png). Level of compression:  0 (no compression) - 9. Recommended: 6. Leave blank if not required!';
    $displaynavtipdesc = 'Display a navigation tip for the slideshow?';
    $displaymessagedesc = 'Display the content item\'s title?';
    $thumbsdesc = 'Generate and save Thumbnails? Thumbnails will be generated, saved and loaded, which speeds the loading significantly. Suitable size should be found out with on-the-fly-method. Thumbnails are stored in the folder \'thumbs\' in the selected folder.';
    $limitdesc = 'Limit the number of shown images?';
    $limitquantitydesc = 'Number of shown images. Leave blank if not required!';
    $noslimdesc = 'Show images without slimbox-effect? With this option you can show images quite normally (without link - like a Web1.0 Gallery).';
    $sortdesc = 'Select the sorting of the images. With \'Sorting from information file\' the sorting is determined through the information file (e.g. captions.txt), all other images which are not specified are excluded from the gallery. If you want to use this option, then you have to activate the option "Information from text file (Info)" and also provide the information file in the images folder. See project page for more information!';
    $sortrandom = 'Random';
    $rootdesc = 'Starting from the root directory? Path to image folder from root, not from images/stories.';
    $ratiodesc = 'Should the aspect ratios be maintained?';
    $captiondesc = 'Show caption?';
    $iptcdesc = 'Read IPTC? Title and signature will be generated from the IPTC values(title und description)!';
    $iptcutf8desc = 'IPTC utf8-encoded? IPTC values are already encoded utf8?';
    $printdesc = 'Print option in JS-View? The displayed image can be print via a link in the slimbox / lytebox!';
    $singledesc = 'Show single image? Enter the name of the single image (with extension type). Leave blank if not required!';
    $singlegallerydesc = 'Should remaining images in the folder be loaded in the gallery - JS-View?';
    $saligndesc = 'Alignment of the single image.';
    $connectdesc = 'Connect different galleries (each syntax call) to each other. Enter for example: imageset. Thus, all syntax calls involving this parameter are summarized in the gallery. Leave blank if not required!';
    $downloaddesc = 'Show download button? Display a button to download the shown image.';
    $listdesc = 'Show images as a list. Images are shown as a list (no thumbnails).';
    $cropdesc = 'Use crop? Soll das Bild beschnitten werden? Should the image be cropped? As a result, uniform thumbnails are created.';
    $cropfactordesc = 'Set crop-factor. Set the -zoom- factor. Details in percent, but without %-character. Example: 50 for 50 percent zoom. Possible input: 1-99. Leave blank if not required!';
    $thumbnaildetaildesc = 'Detail for thumbnail. Should the whole image or only a part of the original image be used for the thumbnail? Crop option must be deactivated!';
    $watermarkdesc = 'Watermark - Should a watermark be added on the images? Replace the image watermark.png under plugins/content/plugin_sige.';
    $watermarkpositiondesc = 'Position of the watermark.';
    $watermarktransdesc = 'Transparency of the watermark. Values between 0-100 are possible. 0 - opaque --- 100 - fully transparent. Leave blank if not required!';
    $encryptdesc = 'Encryption method for image name. To encrypt image names of the original images. ROT13 - very weak, but fast. MD5 - safe, relatively quickly. SHA1 - very safe, slower than MD5. Recommended: MD5';
    $encryptNoneDesc = 'None';
    $imageinfodesc = 'Show image information? Show image name or IPTC values?';
    $imagelinkdesc = 'Set a link - Set a link to any website. Note: JS-view and tooltip will be disabled! Input should be in this form: www.kubik-rubik.de';
    $imagelinknewdesc = 'Open link in new window.';
    $columnquantitydesc = 'Line break after number of images. Should after a certain number of pictures per row a line break be inserted? If not, then leave empty.';
    $cssimagedesc = 'CSS Image Tooltip - The image is displayed in a tooltip when hovering the mouse.';
    $cssimagehalfdesc = 'Half size in the tooltip. If the original image size is too big, with this setting, only the half size of the original is displayed.';
    $copyrightdesc = 'Show \'Simple Image Gallery Extended\' link? Thank you for your support! Tip: If you use more than one gallery on a site, you can set the link also with the parameter \'copyright\'.';
    $watermarkimagedesc = 'Select a different watermark image (image must be in plugins/content/sige/plugin_sige) - Example: watermark-new.png';
    $worddesc = 'Link gallery with a word - Example: Gallery';
    $scaptiondesc = 'Caption in single view, only use with parameter single.';
    $calcmaxthumbsizedesc = 'Calculate maximum size of all thumbnails.';
    $fileinfodesc = 'Information from text file (captions.txt).';
    $turbodesc = 'Activate turbo mode.';
    $resize_imagesdesc = 'Resize original images.';
    $width_imagedesc = 'Maximum width of resized images. Leave blank if not required!';
    $height_imagedesc = 'Maximum height of resized images. Leave blank if not required!';
    $ratio_imagedesc = 'Maintain aspect ratios of the original images.';
    $images_newdesc = 'Overwrite resized images.';
}

if ($folder_input === 0) {
    function read_recursiv($path)
    {
        $result = array();
        $handle = opendir($path);
        $files_blacklist = array('.', '..', 'thumbs', 'resizedimages');

        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, $files_blacklist)) {
                    $name = $path . '/' . $file;

                    if (is_dir($name)) {
                        $ar = read_recursiv($name);

                        foreach ($ar as $value) {
                            $result[] = $value;
                        }

                        $result[] = $name;
                    }
                }
            }
        }

        closedir($handle);
        sort($result);

        return $result;
    }

    $url = '../../../' . $read_in_folder;
    $folders = read_recursiv($url);
    $path_prefix = '';

    if (substr($read_in_folder, -1, 1) != '/') {
        $read_in_folder .= '/';
    }

    // Get information from main plugin to create correct image path
    $plugin_sige = JPluginHelper::getPlugin('content', 'sige');
    $plugin_sige_params = new Registry($plugin_sige->params);

    if ($plugin_sige_params->get('root') == 0) {
        if (substr($read_in_folder, 0, 7) == 'images/') {
            $read_in_folder = substr($read_in_folder, 7);
        }
    }

    if (!empty($read_in_folder)) {
        $path_prefix = $read_in_folder;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Editor Button - SIGE Parameter</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="<?php echo htmlspecialchars(dirname($_SERVER['PHP_SELF'])); ?>/../../../media/system/js/mootools-core.js"></script>
    <script type="text/javascript" src="<?php echo htmlspecialchars(dirname($_SERVER['PHP_SELF'])); ?>/../../../media/system/js/mootools-more.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="sige_button/css/sige_button.css"/>
    <script type="text/javascript">
        window.addEvent('domready', function() {
            <?php if($method != 2) : ?>
            var mySlide = new Fx.Slide('size').hide();
            $('toggle_size').addEvent('click', function(event) {
                event.stop();
                mySlide.toggle();
                mySlide2.hide();
                mySlide3.hide();
                mySlide4.hide();
                mySlide5.hide();
            });

            var mySlide2 = new Fx.Slide('gallery').hide();
            $('toggle_gallery').addEvent('click', function(event) {
                event.stop();
                mySlide2.toggle();
                mySlide.hide();
                mySlide3.hide();
                mySlide4.hide();
                mySlide5.hide();
            });

            var mySlide3 = new Fx.Slide('thumbnail').hide();
            $('toggle_thumbnail').addEvent('click', function(event) {
                event.stop();
                mySlide3.toggle();
                mySlide.hide();
                mySlide2.hide();
                mySlide4.hide();
                mySlide5.hide();
            });

            var mySlide4 = new Fx.Slide('single').hide();
            $('toggle_single').addEvent('click', function(event) {
                event.stop();
                mySlide4.toggle();
                mySlide.hide();
                mySlide2.hide();
                mySlide3.hide();
                mySlide5.hide();
            });

            var mySlide5 = new Fx.Slide('jsview').hide();
            $('toggle_jsview').addEvent('click', function(event) {
                event.stop();
                mySlide5.toggle();
                mySlide.hide();
                mySlide2.hide();
                mySlide3.hide();
                mySlide4.hide();
            });
            <?php endif; ?>

            $$('.tooltip').each(function(element, index) {
                var content = element.get('title').split('::');
                element.store('tip:title', content[0]);
                element.store('tip:text', content[1]);
            });

            var tooltip = new Tips('.tooltip', {
                className: 'tooltip',
                fixed: true,
                hideDelay: 80,
                showDelay: 80
            });
        });
    </script>
    <script type="text/javascript">
        function insertSIGEParameter(editor)
        {
            var tag = "{gallery}";
            var paramsfolder = document.getElementById("paramsfolder").value;

            if(paramsfolder == '')
            {
                tag = tag + "<?php echo $folder; ?>";
            }
            else
            {
                tag = tag + paramsfolder;
            }

            <?php if($method != 2) : ?>
            var paramswidth = document.getElementById("paramswidth").value;

            if(paramswidth != '')
            {
                tag = tag + ",width=" + paramswidth;
            }

            var paramsheight = document.getElementById("paramsheight").value;

            if(paramsheight != '')
            {
                tag = tag + ",height=" + paramsheight;
            }

            var paramsgap_v = document.getElementById("paramsgap_v").value;

            if(paramsgap_v != '')
            {
                tag = tag + ",gap_v=" + paramsgap_v;
            }
            var paramsgap_h = document.getElementById("paramsgap_h").value;
            if(paramsgap_h != '')
            {
                tag = tag + ",gap_h=" + paramsgap_h;
            }

            var paramsquality = document.getElementById("paramsquality").value;
            if(paramsquality != '')
            {
                tag = tag + ",quality=" + paramsquality;
            }

            var paramsquality_png = document.getElementById("paramsquality_png").value;

            if(paramsquality_png != '')
            {
                tag = tag + ",quality_png=" + paramsquality_png;
            }

            if(document.sige_parameter.paramsdisplaynavtip[0].checked == true)
            {
                tag = tag + ",displaynavtip=1";
            }
            else if(document.sige_parameter.paramsdisplaynavtip[1].checked == true)
            {
                tag = tag + ",displaynavtip=0";
            }

            if(document.sige_parameter.paramsdisplayarticle[0].checked == true)
            {
                tag = tag + ",displayarticle=1";
            }
            else if(document.sige_parameter.paramsdisplayarticle[1].checked == true)
            {
                tag = tag + ",displayarticle=0";
            }

            if(document.sige_parameter.paramsthumbs[0].checked == true)
            {
                tag = tag + ",thumbs=1";
            }
            else if(document.sige_parameter.paramsthumbs[1].checked == true)
            {
                tag = tag + ",thumbs=0";
            }

            if(document.sige_parameter.paramslimit[0].checked == true)
            {
                tag = tag + ",limit=1";
            }
            else if(document.sige_parameter.paramslimit[1].checked == true)
            {
                tag = tag + ",limit=0";
            }

            var paramslimit_quantity = document.getElementById("paramslimit_quantity").value;

            if(paramslimit_quantity != '')
            {
                tag = tag + ",limit_quantity=" + paramslimit_quantity;
            }

            if(document.sige_parameter.paramsnoslim[0].checked == true)
            {
                tag = tag + ",noslim=1";
            }
            else if(document.sige_parameter.paramsnoslim[1].checked == true)
            {
                tag = tag + ",noslim=0";
            }

            if(document.sige_parameter.paramssort[0].checked == true)
            {
                tag = tag + ",sort=1";
            }
            else if(document.sige_parameter.paramssort[1].checked == true)
            {
                tag = tag + ",sort=2";
            }
            else if(document.sige_parameter.paramssort[2].checked == true)
            {
                tag = tag + ",sort=3";
            }
            else if(document.sige_parameter.paramssort[3].checked == true)
            {
                tag = tag + ",sort=4";
            }
            else if(document.sige_parameter.paramssort[4].checked == true)
            {
                tag = tag + ",sort=5";
            }
            else if(document.sige_parameter.paramssort[5].checked == true)
            {
                tag = tag + ",sort=6";
            }

            if(document.sige_parameter.paramsroot[0].checked == true)
            {
                tag = tag + ",root=1";
            }
            else if(document.sige_parameter.paramsroot[1].checked == true)
            {
                tag = tag + ",root=0";
            }

            if(document.sige_parameter.paramsratio[0].checked == true)
            {
                tag = tag + ",ratio=1";
            }
            else if(document.sige_parameter.paramsratio[1].checked == true)
            {
                tag = tag + ",ratio=0";
            }

            if(document.sige_parameter.paramsfileinfo[0].checked == true)
            {
                tag = tag + ",fileinfo=1";
            }
            else if(document.sige_parameter.paramsfileinfo[1].checked == true)
            {
                tag = tag + ",fileinfo=0";
            }

            if(document.sige_parameter.paramscaption[0].checked == true)
            {
                tag = tag + ",caption=1";
            }
            else if(document.sige_parameter.paramscaption[1].checked == true)
            {
                tag = tag + ",caption=0";
            }

            if(document.sige_parameter.paramsiptc[0].checked == true)
            {
                tag = tag + ",iptc=1";
            }
            else if(document.sige_parameter.paramsiptc[1].checked == true)
            {
                tag = tag + ",iptc=0";
            }

            if(document.sige_parameter.paramsiptcutf8[0].checked == true)
            {
                tag = tag + ",iptcutf8=1";
            }
            else if(document.sige_parameter.paramsiptcutf8[1].checked == true)
            {
                tag = tag + ",iptcutf8=0";
            }

            if(document.sige_parameter.paramsprint[0].checked == true)
            {
                tag = tag + ",print=1";
            }
            else if(document.sige_parameter.paramsprint[1].checked == true)
            {
                tag = tag + ",print=0";
            }

            var paramssingle = document.getElementById("paramssingle").value;

            if(paramssingle != '')
            {
                tag = tag + ",single=" + paramssingle;
            }

            var paramsscaption = document.getElementById("paramsscaption").value;

            if(paramsscaption != '')
            {
                tag = tag + ",scaption=" + paramsscaption;
            }

            if(document.sige_parameter.paramssingle_gallery[0].checked == true)
            {
                tag = tag + ",single_gallery=1";
            }
            else if(document.sige_parameter.paramssingle_gallery[1].checked == true)
            {
                tag = tag + ",single_gallery=0";
            }

            if(document.sige_parameter.paramssalign[0].checked == true)
            {
                tag = tag + ",salign=left";
            }
            else if(document.sige_parameter.paramssalign[1].checked == true)
            {
                tag = tag + ",salign=right";
            }
            else if(document.sige_parameter.paramssalign[2].checked == true)
            {
                tag = tag + ",salign=center";
            }

            var paramsconnect = document.getElementById("paramsconnect").value;

            if(paramsconnect != '')
            {
                tag = tag + ",connect=" + paramsconnect;
            }

            if(document.sige_parameter.paramsdownload[0].checked == true)
            {
                tag = tag + ",download=1";
            }
            else if(document.sige_parameter.paramsdownload[1].checked == true)
            {
                tag = tag + ",download=0";
            }

            if(document.sige_parameter.paramslist[0].checked == true)
            {
                tag = tag + ",list=1";
            }
            else if(document.sige_parameter.paramslist[1].checked == true)
            {
                tag = tag + ",list=0";
            }

            var paramsword = document.getElementById("paramsword").value;

            if(paramsword != '')
            {
                tag = tag + ",word=" + paramsword;
            }

            if(document.sige_parameter.paramscrop[0].checked == true)
            {
                tag = tag + ",crop=1";
            }
            else if(document.sige_parameter.paramscrop[1].checked == true)
            {
                tag = tag + ",crop=0";
            }

            var paramscrop_factor = document.getElementById("paramscrop_factor").value;

            if(paramscrop_factor != '')
            {
                tag = tag + ",crop_factor=" + paramscrop_factor;
            }

            if(document.sige_parameter.paramsthumbdetail[0].checked == true)
            {
                tag = tag + ",thumbdetail=1";
            }
            else if(document.sige_parameter.paramsthumbdetail[1].checked == true)
            {
                tag = tag + ",thumbdetail=2";
            }
            else if(document.sige_parameter.paramsthumbdetail[2].checked == true)
            {
                tag = tag + ",thumbdetail=3";
            }
            else if(document.sige_parameter.paramsthumbdetail[3].checked == true)
            {
                tag = tag + ",thumbdetail=4";
            }
            else if(document.sige_parameter.paramsthumbdetail[4].checked == true)
            {
                tag = tag + ",thumbdetail=5";
            }

            if(document.sige_parameter.paramscalcmaxthumbsize[0].checked == true)
            {
                tag = tag + ",calcmaxthumbsize=1";
            }
            else if(document.sige_parameter.paramscalcmaxthumbsize[1].checked == true)
            {
                tag = tag + ",calcmaxthumbsize=0";
            }

            if(document.sige_parameter.paramswatermark[0].checked == true)
            {
                tag = tag + ",watermark=1";
            }
            else if(document.sige_parameter.paramswatermark[1].checked == true)
            {
                tag = tag + ",watermark=0";
            }

            if(document.sige_parameter.paramswatermarkposition[0].checked == true)
            {
                tag = tag + ",watermarkposition=1";
            }
            else if(document.sige_parameter.paramswatermarkposition[1].checked == true)
            {
                tag = tag + ",watermarkposition=2";
            }
            else if(document.sige_parameter.paramswatermarkposition[2].checked == true)
            {
                tag = tag + ",watermarkposition=3";
            }
            else if(document.sige_parameter.paramswatermarkposition[3].checked == true)
            {
                tag = tag + ",watermarkposition=4";
            }
            else if(document.sige_parameter.paramswatermarkposition[4].checked == true)
            {
                tag = tag + ",watermarkposition=5";
            }

            var paramswatermarkimage = document.getElementById("paramswatermarkimage").value;

            if(paramswatermarkimage != '')
            {
                tag = tag + ",watermarkimage=" + paramswatermarkimage;
            }

            if(document.sige_parameter.paramsencrypt[0].checked == true)
            {
                tag = tag + ",encrypt=-1";
            }
            else if(document.sige_parameter.paramsencrypt[1].checked == true)
            {
                tag = tag + ",encrypt=1";
            }
            else if(document.sige_parameter.paramsencrypt[2].checked == true)
            {
                tag = tag + ",encrypt=2";
            }
            else if(document.sige_parameter.paramsencrypt[3].checked == true)
            {
                tag = tag + ",encrypt=3";
            }

            if(document.sige_parameter.paramsimage_info[0].checked == true)
            {
                tag = tag + ",image_info=1";
            }
            else if(document.sige_parameter.paramsimage_info[1].checked == true)
            {
                tag = tag + ",image_info=0";
            }

            var paramsimage_link = document.getElementById("paramsimage_link").value;

            if(paramsimage_link != '')
            {
                tag = tag + ",image_link=" + paramsimage_link;
            }

            if(document.sige_parameter.paramsimage_link_new[0].checked == true)
            {
                tag = tag + ",image_link_new=1";
            }
            else if(document.sige_parameter.paramsimage_link_new[1].checked == true)
            {
                tag = tag + ",image_link_new=0";
            }

            var paramscolumn_quantity = document.getElementById("paramscolumn_quantity").value;

            if(paramscolumn_quantity != '')
            {
                tag = tag + ",column_quantity=" + paramscolumn_quantity;
            }

            if(document.sige_parameter.paramscss_image[0].checked == true)
            {
                tag = tag + ",css_image=1";
            }
            else if(document.sige_parameter.paramscss_image[1].checked == true)
            {
                tag = tag + ",css_image=0";
            }

            if(document.sige_parameter.paramscss_image_half[0].checked == true)
            {
                tag = tag + ",css_image_half=1";
            }
            else if(document.sige_parameter.paramscss_image_half[1].checked == true)
            {
                tag = tag + ",css_image_half=0";
            }

            if(document.sige_parameter.paramsturbo[0].checked == true)
            {
                tag = tag + ",turbo=1";
            }
            else if(document.sige_parameter.paramsturbo[1].checked == true)
            {
                tag = tag + ",turbo=new";
            }
            else if(document.sige_parameter.paramsturbo[2].checked == true)
            {
                tag = tag + ",turbo=0";
            }

            if(document.sige_parameter.paramscopyright[0].checked == true)
            {
                tag = tag + ",copyright=1";
            }
            else if(document.sige_parameter.paramscopyright[1].checked == true)
            {
                tag = tag + ",copyright=0";
            }

            if(document.sige_parameter.paramsresize_images[0].checked == true)
            {
                tag = tag + ",resize_images=1";
            }
            else if(document.sige_parameter.paramsresize_images[1].checked == true)
            {
                tag = tag + ",resize_images=0";
            }

            var paramswidth_image = document.getElementById("paramswidth_image").value;

            if(paramswidth_image != '')
            {
                tag = tag + ",width_image=" + paramswidth_image;
            }

            var paramsheight_image = document.getElementById("paramsheight_image").value;

            if(paramsheight_image != '')
            {
                tag = tag + ",height_image=" + paramsheight_image;
            }

            if(document.sige_parameter.paramsratio_image[0].checked == true)
            {
                tag = tag + ",ratio_image=1";
            }
            else if(document.sige_parameter.paramsratio_image[1].checked == true)
            {
                tag = tag + ",ratio_image=0";
            }

            if(document.sige_parameter.paramsimages_new[0].checked == true)
            {
                tag = tag + ",images_new=1";
            }
            else if(document.sige_parameter.paramsimages_new[1].checked == true)
            {
                tag = tag + ",images_new=0";
            }
            <?php endif; ?>

            tag = tag + "{/gallery}";
            window.parent.jInsertEditorText(tag, '<?php echo preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $e_name); ?>');
            window.parent.SqueezeBox.close();
            return false;
        }
    </script>
</head>
<body style="font-family: Helvetica,Arial,sans-serif; font-size: 90%; width: 770px;">
<form name="sige_parameter" action="">
    <table width="100%" style="border-spacing: 15px">
        <tbody>
        <tr>
            <td width="25%">
                <label for="paramsfolder" class="tooltip" title="<?php echo $folder; ?> :: <?php echo $folderdesc; ?>">
                    <?php echo $folder; ?>
                </label>
            </td>
            <td>
                <?php if ($folder_input === 0) {
                    echo '<select name="paramsfolder" id="paramsfolder">';

                    foreach ($folders as $folder_array) {
                        echo '<option value="' . $path_prefix . str_replace($url . '/', '', $folder_array) . '">' . str_replace($url . '/', '', $folder_array) . '</option>';
                    }

                    echo '</select>';
                } else {
                    echo '<input name="paramsfolder" id="paramsfolder" value="" type="text" />';
                }
                ?>
            </td>
        </tr>
        </tbody>
    </table>
    <div style="margin-left: 10px">
        <?php if ($method != 2) : ?>
            <span class="toggle"><a id="toggle_size" href="#"><?php echo $size; ?></a></span>
            <span class="toggle"><a id="toggle_gallery" href="#"><?php echo $gallery; ?></a></span>
            <span class="toggle"><a id="toggle_jsview" href="#"><?php echo $js_view; ?></a></span>
            <span class="toggle"><a id="toggle_thumbnail" href="#"><?php echo $thumbnail; ?></a></span>
            <span class="toggle"><a id="toggle_single" href="#"><?php echo $single_image; ?></a></span>
        <?php endif; ?>
        <button class="btn btn-success" onclick="insertSIGEParameter();"><?php echo $insert; ?></button>
    </div>
    <?php if ($method != 2) : ?>
        <div id="size">
            <table width="100%" style="border-spacing:15px">
                <tbody>
                <tr>
                    <td width="25%">
                        <label for="paramswidth" class="tooltip" title="width :: <?php echo $widththumbsdesc; ?>">
                            width
                        </label>
                    </td>
                    <td><input name="paramswidth" id="paramswidth" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label for="paramsheight" class="tooltip" title="height :: <?php echo $heightthumbsdesc; ?>">
                            height
                        </label>
                    </td>
                    <td><input name="paramsheight" id="paramsheight" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsgap_v-lbl" for="paramsgap_v" class="tooltip" title="gap_v :: <?php echo $gapvdesc; ?>">
                            gap_v
                        </label>
                    </td>
                    <td><input name="paramsgap_v" id="paramsgap_v" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsgap_h-lbl" for="paramsgap_h" class="tooltip" title="gap_h :: <?php echo $gapvdesc; ?>">
                            gap_h
                        </label>
                    </td>
                    <td><input name="paramsgap_h" id="paramsgap_h" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsquality-lbl" for="paramsquality" class="tooltip" title="quality :: <?php echo $qualityjpgdesc; ?>">
                            quality
                        </label>
                    </td>
                    <td><input name="paramsquality" id="paramsquality" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsquality_png-lbl" for="paramsquality_png" class="tooltip" title="quality_png :: <?php echo $qualitypngdesc; ?>">
                            quality_png
                        </label>
                    </td>
                    <td><input name="paramsquality_png" id="paramsquality_png" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsresize_images-lbl" for="paramsresize_images" class="tooltip" title="resize_images :: <?php echo $resize_imagesdesc; ?>">
                            resize_images
                        </label>
                    </td>
                    <td>
                        <input name="paramsresize_images" id="paramsresize_images" value="1" type="radio"/>
                        <label for="paramsresize_images"><?php echo $yes; ?></label>
                        <input name="paramsresize_images" id="paramsresize_images2" value="2" type="radio"/>
                        <label for="paramsresize_images2"><?php echo $no; ?></label>
                        <input name="paramsresize_images" id="paramsresize_images0" value="0" checked="checked" type="radio"/>
                        <label for="paramsresize_images0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label for="paramswidth_image" class="tooltip" title="width_image :: <?php echo $width_imagedesc; ?>">
                            width_image
                        </label>
                    </td>
                    <td><input name="paramswidth_image" id="paramswidth_image" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label for="paramsheight_image" class="tooltip" title="height_image :: <?php echo $height_imagedesc; ?>">
                            height_image
                        </label>
                    </td>
                    <td><input name="paramsheight_image" id="paramsheight_image" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsratio_image-lbl" for="paramsratio_image" class="tooltip" title="ratio_image :: <?php echo $ratio_imagedesc; ?>">
                            ratio_image
                        </label>
                    </td>
                    <td>
                        <input name="paramsratio_image" id="paramsratio_image" value="1" type="radio"/>
                        <label for="paramsratio_image"><?php echo $yes; ?></label>
                        <input name="paramsratio_image" id="paramsratio_image2" value="2" type="radio"/>
                        <label for="paramsratio_image2"><?php echo $no; ?></label>
                        <input name="paramsratio_image" id="paramsratio_image0" value="0" checked="checked" type="radio"/>
                        <label for="paramsratio_image0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsimages_new-lbl" for="paramsimages_new" class="tooltip" title="images_new :: <?php echo $images_newdesc; ?>">
                            images_new
                        </label>
                    </td>
                    <td>
                        <input name="paramsimages_new" id="paramsimages_new" value="1" type="radio"/>
                        <label for="paramsimages_new"><?php echo $yes; ?></label>
                        <input name="paramsimages_new" id="paramsimages_new2" value="2" type="radio"/>
                        <label for="paramsimages_new2"><?php echo $no; ?></label>
                        <input name="paramsimages_new" id="paramsimages_new0" value="0" checked="checked" type="radio"/>
                        <label for="paramsimages_new0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="gallery">
            <table width="100%" style="border-spacing:15px">
                <tbody>
                <tr>
                    <td width="25%">
                        <label id="paramsturbo-lbl" for="paramsturbo" class="tooltip" title="turbo :: <?php echo $turbodesc; ?>">
                            turbo
                        </label>
                    </td>
                    <td>
                        <input name="paramsturbo" id="paramsturbo" value="1" type="radio"/>
                        <label for="paramsturbo"><?php echo $yes; ?></label>
                        <input name="paramsturbo" id="paramsturbo2" value="2" type="radio"/>
                        <label for="paramsturbo2">New</label>
                        <input name="paramsturbo" id="paramsturbo3" value="3" type="radio"/>
                        <label for="paramsturbo3"><?php echo $no; ?></label>
                        <input name="paramsturbo" id="paramsturbo0" value="0" checked="checked" type="radio"/>
                        <label for="paramsturbo0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsroot-lbl" for="paramsroot" class="tooltip" title="root :: <?php echo $rootdesc; ?>">
                            root
                        </label>
                    </td>
                    <td>
                        <input name="paramsroot" id="paramsroot" value="1" type="radio"/>
                        <label for="paramsroot"><?php echo $yes; ?></label>
                        <input name="paramsroot" id="paramsroot2" value="2" type="radio"/>
                        <label for="paramsroot2"><?php echo $no; ?></label>
                        <input name="paramsroot" id="paramsroot0" value="0" checked="checked" type="radio"/>
                        <label for="paramsroot0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramssort-lbl" for="paramssort" class="tooltip" title="sort :: <?php echo $sortdesc; ?>">
                            sort
                        </label>
                    </td>
                    <td>
                        <input name="paramssort" id="paramssort" value="1" type="radio"/>
                        <label for="paramssort"><?php echo $sortrandom; ?></label>
                        <input name="paramssort" id="paramssort2" value="2" type="radio"/>
                        <label for="paramssort2"><?php echo $noasc; ?></label>
                        <input name="paramssort" id="paramssort3" value="3" type="radio"/>
                        <label for="paramssort3"><?php echo $nodesc; ?></label>
                        <input name="paramssort" id="paramssort4" value="4" type="radio"/>
                        <label for="paramssort4"><?php echo $timeasc; ?></label>
                        <input name="paramssort" id="paramssort5" value="5" type="radio"/>
                        <label for="paramssort5"><?php echo $timedesc; ?></label>
                        <input name="paramssort" id="paramssort6" value="6" type="radio"/>
                        <label for="paramssort6"><?php echo $sortfromfile; ?></label>
                        <input name="paramssort" id="paramssort0" value="0" checked="checked" type="radio"/>
                        <label for="paramssort0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscolumn_quantity-lbl" for="paramscolumn_quantity" class="tooltip" title="column_quantity :: <?php echo $columnquantitydesc; ?>">
                            column_quantity
                        </label>
                    </td>
                    <td><input name="paramscolumn_quantity" id="paramscolumn_quantity" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscss_image-lbl" for="paramscss_image" class="tooltip" title="css_image :: <?php echo $cssimagedesc; ?>">
                            css_image
                        </label>
                    </td>
                    <td>
                        <input name="paramscss_image" id="paramscss_image" value="1" type="radio"/>
                        <label for="paramscss_image"><?php echo $yes; ?></label>
                        <input name="paramscss_image" id="paramscss_image2" value="2" type="radio"/>
                        <label for="paramscss_image2"><?php echo $no; ?></label>
                        <input name="paramscss_image" id="paramscss_image0" value="0" checked="checked" type="radio"/>
                        <label for="paramscss_image0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscss_image_half-lbl" for="paramscss_image_half" class="tooltip" title="css_image_half :: <?php echo $cssimagehalfdesc; ?>">
                            css_image_half
                        </label>
                    </td>
                    <td>
                        <input name="paramscss_image_half" id="paramscss_image_half" value="1" type="radio"/>
                        <label for="paramscss_image_half"><?php echo $yes; ?></label>
                        <input name="paramscss_image_half" id="paramscss_image_half2" value="2" type="radio"/>
                        <label for="paramscss_image_half2"><?php echo $no; ?></label>
                        <input name="paramscss_image_half" id="paramscss_image_half0" value="0" checked="checked" type="radio"/>
                        <label for="paramscss_image_half0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsfileinfo-lbl" for="paramsfileinfo" class="tooltip" title="fileinfo :: <?php echo $fileinfodesc; ?>">
                            fileinfo
                        </label>
                    </td>
                    <td>
                        <input name="paramsfileinfo" id="paramsfileinfo" value="1" type="radio"/>
                        <label for="paramsfileinfo"><?php echo $yes; ?></label>
                        <input name="paramsfileinfo" id="paramsfileinfo2" value="2" type="radio"/>
                        <label for="paramsfileinfo2"><?php echo $no; ?></label>
                        <input name="paramsfileinfo" id="paramsfileinfo0" value="0" checked="checked" type="radio"/>
                        <label for="paramsfileinfo0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscaption-lbl" for="paramscaption" class="tooltip" title="caption :: <?php echo $captiondesc; ?>">
                            caption
                        </label>
                    </td>
                    <td>
                        <input name="paramscaption" id="paramscaption" value="1" type="radio"/>
                        <label for="paramscaption"><?php echo $yes; ?></label>
                        <input name="paramscaption" id="paramscaption2" value="2" type="radio"/>
                        <label for="paramscaption2"><?php echo $no; ?></label>
                        <input name="paramscaption" id="paramscaption0" value="0" checked="checked" type="radio"/>
                        <label for="paramscaption0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsnoslim-lbl" for="paramsnoslim" class="tooltip" title="noslim :: <?php echo $noslimdesc; ?>">
                            noslim
                        </label>
                    </td>
                    <td>
                        <input name="paramsnoslim" id="paramsnoslim" value="1" type="radio"/>
                        <label for="paramsnoslim"><?php echo $yes; ?></label>
                        <input name="paramsnoslim" id="paramsnoslim2" value="2" type="radio"/>
                        <label for="paramsnoslim2"><?php echo $no; ?></label>
                        <input name="paramsnoslim" id="paramsnoslim0" value="0" checked="checked" type="radio"/>
                        <label for="paramsnoslim0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramslist-lbl" for="paramslist" class="tooltip" title="list :: <?php echo $listdesc; ?>">
                            list
                        </label>
                    </td>
                    <td>
                        <input name="paramslist" id="paramslist" value="1" type="radio"/>
                        <label for="paramslist"><?php echo $yes; ?></label>
                        <input name="paramslist" id="paramslist2" value="2" type="radio"/>
                        <label for="paramslist2"><?php echo $no; ?></label>
                        <input name="paramslist" id="paramslist0" value="0" checked="checked" type="radio"/>
                        <label for="paramslist0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsword-lbl" for="paramsword" class="tooltip" title="word :: <?php echo $worddesc; ?>">
                            word
                        </label>
                    </td>
                    <td><input name="paramsword" id="paramsword" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramslimit-lbl" for="paramslimit" class="tooltip" title="limit :: <?php echo $limitdesc; ?>">
                            limit
                        </label>
                    </td>
                    <td>
                        <input name="paramslimit" id="paramslimit" value="1" type="radio"/>
                        <label for="paramslimit"><?php echo $yes; ?></label>
                        <input name="paramslimit" id="paramslimit2" value="2" type="radio"/>
                        <label for="paramslimit2"><?php echo $no; ?></label>
                        <input name="paramslimit" id="paramslimit0" value="0" checked="checked" type="radio"/>
                        <label for="paramslimit0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramslimit_quantity-lbl" for="paramslimit_quantity" class="tooltip" title="limit_quantity :: <?php echo $limitquantitydesc; ?>">
                            limit_quantity
                        </label>
                    </td>
                    <td><input name="paramslimit_quantity" id="paramslimit_quantity" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsconnect-lbl" for="paramsconnect" class="tooltip" title="connect :: <?php echo $connectdesc; ?>">
                            connect
                        </label>
                    </td>
                    <td><input name="paramsconnect" id="paramsconnect" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsimage_link-lbl" for="paramsimage_link" class="tooltip" title="image_link :: <?php echo $imagelinkdesc; ?>">
                            image_link
                        </label>
                    </td>
                    <td><input name="paramsimage_link" id="paramsimage_link" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsimage_link_new-lbl" for="paramsimage_link_new" class="tooltip" title="image_link_new :: <?php echo $imagelinknewdesc; ?>">
                            image_link_new
                        </label>
                    </td>
                    <td>
                        <input name="paramsimage_link_new" id="paramsimage_link_new" value="1" type="radio"/>
                        <label for="paramsimage_link_new"><?php echo $yes; ?></label>
                        <input name="paramsimage_link_new" id="paramsimage_link_new2" value="2" type="radio"/>
                        <label for="paramsimage_link_new2"><?php echo $no; ?></label>
                        <input name="paramsimage_link_new" id="paramsimage_link_new0" value="0" checked="checked" type="radio"/>
                        <label for="paramsimage_link_new0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscopyright-lbl" for="paramscopyright" class="tooltip" title="copyright :: <?php echo $copyrightdesc; ?>">
                            copyright
                        </label>
                    </td>
                    <td>
                        <input name="paramscopyright" id="paramscopyright" value="1" type="radio"/>
                        <label for="paramscopyright"><?php echo $yes; ?></label>
                        <input name="paramscopyright" id="paramscopyright2" value="2" type="radio"/>
                        <label for="paramscopyright2"><?php echo $no; ?></label>
                        <input name="paramscopyright" id="paramscopyright0" value="0" checked="checked" type="radio"/>
                        <label for="paramscopyright0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="jsview">
            <table width="100%" style="border-spacing:15px">
                <tbody>
                <tr>
                    <td width="25%">
                        <label id="paramsimage_info-lbl" for="paramsimage_info" class="tooltip" title="image_info :: <?php echo $imageinfodesc; ?>">
                            image_info
                        </label>
                    </td>
                    <td>
                        <input name="paramsimage_info" id="paramsimage_info" value="1" type="radio"/>
                        <label for="paramsimage_info"><?php echo $yes; ?></label>
                        <input name="paramsimage_info" id="paramsimage_info2" value="2" type="radio"/>
                        <label for="paramsimage_info2"><?php echo $no; ?></label>
                        <input name="paramsimage_info" id="paramsimage_info0" value="0" checked="checked" type="radio"/>
                        <label for="paramsimage_info0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsiptc-lbl" for="paramsiptc" class="tooltip" title="iptc :: <?php echo $iptcdesc; ?>">
                            iptc
                        </label>
                    </td>
                    <td>
                        <input name="paramsiptc" id="paramsiptc" value="1" type="radio"/>
                        <label for="paramsiptc"><?php echo $yes; ?></label>
                        <input name="paramsiptc" id="paramsiptc2" value="2" type="radio"/>
                        <label for="paramsiptc2"><?php echo $no; ?></label>
                        <input name="paramsiptc" id="paramsiptc0" value="0" checked="checked" type="radio"/>
                        <label for="paramsiptc0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsiptcutf8-lbl" for="paramsiptcutf8" class="tooltip" title="iptcutf8 :: <?php echo $iptcutf8desc; ?>">
                            iptcutf8
                        </label>
                    </td>
                    <td>
                        <input name="paramsiptcutf8" id="paramsiptcutf8" value="1" type="radio"/>
                        <label for="paramsiptcutf8"><?php echo $yes; ?></label>
                        <input name="paramsiptcutf8" id="paramsiptcutf82" value="2" type="radio"/>
                        <label for="paramsiptcutf82"><?php echo $no; ?></label>
                        <input name="paramsiptcutf8" id="paramsiptcutf80" value="0" checked="checked" type="radio"/>
                        <label for="paramsiptcutf80"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsprint-lbl" for="paramsprint" class="tooltip" title="print :: <?php echo $printdesc; ?>">
                            print
                        </label>
                    </td>
                    <td>
                        <input name="paramsprint" id="paramsprint" value="1" type="radio"/>
                        <label for="paramsprint"><?php echo $yes; ?></label>
                        <input name="paramsprint" id="paramsprint2" value="2" type="radio"/>
                        <label for="paramsprint2"><?php echo $no; ?></label>
                        <input name="paramsprint" id="paramsprint0" value="0" checked="checked" type="radio"/>
                        <label for="paramsprint0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsdownload-lbl" for="paramsdownload" class="tooltip" title="download :: <?php echo $downloaddesc; ?>">
                            download
                        </label>
                    </td>
                    <td>
                        <input name="paramsdownload" id="paramsdownload" value="1" type="radio"/>
                        <label for="paramsdownload"><?php echo $yes; ?></label>
                        <input name="paramsdownload" id="paramsdownload2" value="2" type="radio"/>
                        <label for="paramsdownload2"><?php echo $no; ?></label>
                        <input name="paramsdownload" id="paramsdownload0" value="0" checked="checked" type="radio"/>
                        <label for="paramsdownload0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsdisplaynavtip-lbl" for="paramsdisplaynavtip" class="tooltip" title="displaynavtip :: <?php echo $displaynavtipdesc; ?>">
                            displaynavtip
                        </label>
                    </td>
                    <td>
                        <input name="paramsdisplaynavtip" id="paramsdisplaynavtip" value="1" type="radio"/>
                        <label for="paramsdisplaynavtip"><?php echo $yes; ?></label>
                        <input name="paramsdisplaynavtip" id="paramsdisplaynavtip2" value="2" type="radio"/>
                        <label for="paramsdisplaynavtip2"><?php echo $no; ?></label>
                        <input name="paramsdisplaynavtip" id="paramsdisplaynavtip0" value="0" checked="checked" type="radio"/>
                        <label for="paramsdisplaynavtip0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsdisplayarticle-lbl" for="paramsdisplayarticle" class="tooltip" title="displayarticle :: <?php echo $displaymessagedesc; ?>">
                            displayarticle
                        </label>
                    </td>
                    <td>
                        <input name="paramsdisplayarticle" id="paramsdisplayarticle" value="1" type="radio"/>
                        <label for="paramsdisplayarticle"><?php echo $yes; ?></label>
                        <input name="paramsdisplayarticle" id="paramsdisplayarticle2" value="2" type="radio"/>
                        <label for="paramsdisplayarticle2"><?php echo $no; ?></label>
                        <input name="paramsdisplayarticle" id="paramsdisplayarticle0" value="0" checked="checked" type="radio"/>
                        <label for="paramsdisplayarticle0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="thumbnail">
            <table width="100%" style="border-spacing:15px">
                <tbody>
                <tr>
                    <td width="25%">
                        <label id="paramsthumbs-lbl" for="paramsthumbs" class="tooltip" title="thumbs :: <?php echo $thumbsdesc; ?>">
                            thumbs
                        </label>
                    </td>
                    <td>
                        <input name="paramsthumbs" id="paramsthumbs" value="1" type="radio"/>
                        <label for="paramsthumbs"><?php echo $yes; ?></label>
                        <input name="paramsthumbs" id="paramsthumbs2" value="2" type="radio"/>
                        <label for="paramsthumbs2"><?php echo $no; ?></label>
                        <input name="paramsthumbs" id="paramsthumbs0" value="0" checked="checked" type="radio"/>
                        <label for="paramsthumbs0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsthumbdetail-lbl" for="paramsthumbdetail" class="tooltip" title="thumbdetail :: <?php echo $thumbnaildetaildesc; ?>">
                            thumbdetail
                        </label>
                    </td>
                    <td>
                        <input name="paramsthumbdetail" id="paramsthumbdetail" value="1" type="radio"/>
                        <label for="paramsthumbdetail">1:1</label>
                        <input name="paramsthumbdetail" id="paramsthumbdetail2" value="2" type="radio"/>
                        <label for="paramsthumbdetail2"><?php echo $topleft; ?></label>
                        <input name="paramsthumbdetail" id="paramsthumbdetail3" value="3" type="radio"/>
                        <label for="paramsthumbdetail3"><?php echo $topright; ?></label>
                        <input name="paramsthumbdetail" id="paramsthumbdetail4" value="4" type="radio"/>
                        <label for="paramsthumbdetail4"><?php echo $bottomleft; ?></label>
                        <input name="paramsthumbdetail" id="paramsthumbdetail5" value="5" type="radio"/>
                        <label for="paramsthumbdetail5"><?php echo $bottomright; ?></label>
                        <input name="paramsthumbdetail" id="paramsthumbdetail0" value="0" checked="checked"
                               type="radio"/>
                        <label for="paramsthumbdetail0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscalcmaxthumbsize-lbl" for="paramscalcmaxthumbsize" class="tooltip" title="calcmaxthumbsize :: <?php echo $calcmaxthumbsizedesc; ?>">
                            calcmaxthumbsize
                        </label>
                    </td>
                    <td>
                        <input name="paramscalcmaxthumbsize" id="paramscalcmaxthumbsize" value="1" type="radio"/>
                        <label for="paramscalcmaxthumbsize"><?php echo $yes; ?></label>
                        <input name="paramscalcmaxthumbsize" id="paramscalcmaxthumbsize2" value="2" type="radio"/>
                        <label for="paramscalcmaxthumbsize2"><?php echo $no; ?></label>
                        <input name="paramscalcmaxthumbsize" id="paramscalcmaxthumbsize0" value="0"
                               checked="checked" type="radio"/>
                        <label for="paramscalcmaxthumbsize0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsratio-lbl" for="paramsratio" class="tooltip" title="ratio :: <?php echo $ratiodesc; ?>">
                            ratio
                        </label>
                    </td>
                    <td>
                        <input name="paramsratio" id="paramsratio" value="1" type="radio"/>
                        <label for="paramsratio"><?php echo $yes; ?></label>
                        <input name="paramsratio" id="paramsratio2" value="2" type="radio"/>
                        <label for="paramsratio2"><?php echo $no; ?></label>
                        <input name="paramsratio" id="paramsratio0" value="0" checked="checked" type="radio"/>
                        <label for="paramsratio0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscrop-lbl" for="paramscrop" class="tooltip" title="crop :: <?php echo $cropdesc; ?>">
                            crop
                        </label>
                    </td>
                    <td>
                        <input name="paramscrop" id="paramscrop" value="1" type="radio"/>
                        <label for="paramscrop"><?php echo $yes; ?></label>
                        <input name="paramscrop" id="paramscrop2" value="2" type="radio"/>
                        <label for="paramscrop2"><?php echo $no; ?></label>
                        <input name="paramscrop" id="paramscrop0" value="0" checked="checked" type="radio"/>
                        <label for="paramscrop0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramscrop_factor-lbl" for="paramscrop_factor" class="tooltip" title="crop_factor :: <?php echo $cropfactordesc; ?>">
                            crop_factor
                        </label>
                    </td>
                    <td><input name="paramscrop_factor" id="paramscrop_factor" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramswatermark-lbl" for="paramswatermark" class="tooltip" title="watermark :: <?php echo $watermarkdesc; ?>">
                            watermark
                        </label>
                    </td>
                    <td>
                        <input name="paramswatermark" id="paramswatermark" value="1" type="radio"/>
                        <label for="paramswatermark"><?php echo $yes; ?></label>
                        <input name="paramswatermark" id="paramswatermark2" value="2" type="radio"/>
                        <label for="paramswatermark2"><?php echo $no; ?></label>
                        <input name="paramswatermark" id="paramswatermark0" value="0" checked="checked"
                               type="radio"/>
                        <label for="paramswatermark0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramswatermarkposition-lbl" for="paramswatermarkposition" class="tooltip" title="watermarkposition :: <?php echo $watermarkpositiondesc; ?>">
                            watermarkposition
                        </label>
                    </td>
                    <td>
                        <input name="paramswatermarkposition" id="paramswatermarkposition" value="1" type="radio"/>
                        <label for="paramswatermarkposition"><?php echo $center; ?></label>
                        <input name="paramswatermarkposition" id="paramswatermarkposition2" value="2" type="radio"/>
                        <label for="paramswatermarkposition2"><?php echo $topleft; ?></label>
                        <input name="paramswatermarkposition" id="paramswatermarkposition3" value="3" type="radio"/>
                        <label for="paramswatermarkposition3"><?php echo $topright; ?></label>
                        <input name="paramswatermarkposition" id="paramswatermarkposition4" value="4" type="radio"/>
                        <label for="paramswatermarkposition4"><?php echo $bottomleft; ?></label>
                        <input name="paramswatermarkposition" id="paramswatermarkposition5" value="5" type="radio"/>
                        <label for="paramswatermarkposition5"><?php echo $bottomright; ?></label>
                        <input name="paramswatermarkposition" id="paramswatermarkposition0" value="0"
                               checked="checked" type="radio"/>
                        <label for="paramswatermarkposition0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramswatermarkimage-lbl" for="paramswatermarkimage" class="tooltip" title="watermarkimage :: <?php echo $watermarkimagedesc; ?>">
                            watermarkimage
                        </label>
                    </td>
                    <td><input name="paramswatermarkimage" id="paramswatermarkimage" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsencrypt-lbl" for="paramsencrypt" class="tooltip" title="encrypt :: <?php echo $encryptdesc; ?>">
                            encrypt
                        </label>
                    </td>
                    <td>
                        <input name="paramsencrypt" id="paramsencrypt-1" value="-1" type="radio"/>
                        <label for="paramsencrypt-1"><?php echo $encryptNoneDesc; ?></label>
                        <input name="paramsencrypt" id="paramsencrypt" value="1" type="radio"/>
                        <label for="paramsencrypt">ROT13</label>
                        <input name="paramsencrypt" id="paramsencrypt2" value="2" type="radio"/>
                        <label for="paramsencrypt2">MD5</label>
                        <input name="paramsencrypt" id="paramsencrypt3" value="3" type="radio"/>
                        <label for="paramsencrypt3">SHA1</label>
                        <input name="paramsencrypt" id="paramsencrypt0" value="0" checked="checked" type="radio"/>
                        <label for="paramsencrypt0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="single">
            <table width="100%" style="border-spacing:15px">
                <tbody>
                <tr>
                    <td width="25%">
                        <label id="paramssingle-lbl" for="paramssingle" class="tooltip" title="single :: <?php echo $singledesc; ?>">
                            single
                        </label>
                    </td>
                    <td><input name="paramssingle" id="paramssingle" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramsscaption-lbl" for="paramsscaption" class="tooltip" title="scaption :: <?php echo $scaptiondesc; ?>">
                            scaption
                        </label>
                    </td>
                    <td><input name="paramsscaption" id="paramsscaption" value="" type="text"/></td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramssingle_gallery-lbl" for="paramssingle_gallery" class="tooltip" title="single_gallery :: <?php echo $singlegallerydesc; ?>">
                            single_gallery
                        </label>
                    </td>
                    <td>
                        <input name="paramssingle_gallery" id="paramssingle_gallery" value="1" type="radio"/>
                        <label for="paramssingle_gallery"><?php echo $yes; ?></label>
                        <input name="paramssingle_gallery" id="paramssingle_gallery2" value="2" type="radio"/>
                        <label for="paramssingle_gallery2"><?php echo $no; ?></label>
                        <input name="paramssingle_gallery" id="paramssingle_gallery0" value="0" checked="checked" type="radio"/>
                        <label for="paramssingle_gallery0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                        <label id="paramssalign-lbl" for="paramssalign" class="tooltip" title="salign :: <?php echo $saligndesc; ?>">
                            salign
                        </label>
                    </td>
                    <td>
                        <input name="paramssalign" id="paramssalign" value="1" type="radio"/>
                        <label for="paramssalign"><?php echo $left; ?></label>
                        <input name="paramssalign" id="paramssalign2" value="2" type="radio"/>
                        <label for="paramssalign2"><?php echo $right; ?></label>
                        <input name="paramssalign" id="paramssalign3" value="3" type="radio"/>
                        <label for="paramssalign3"><?php echo $center; ?></label>
                        <input name="paramssalign" id="paramssalign0" value="0" checked="checked" type="radio"/>
                        <label for="paramssalign0"><?php echo $notuse; ?></label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</form>
</body>
</html>