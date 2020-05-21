<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagenavigation
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$lang = Factory::getLanguage();
$app = Factory::getApplication();
$templateParams = $app->getTemplate(true)->params;

$themeColor = $templateParams->get('themecolor');
$btnColor = '';

switch ($themeColor) {
	case 'red':
		$btnColor = 'danger';
		break;
	case 'green':
		$btnColor = 'success';
		break;
	default:
		$btnColor = 'primary';
		break;
}

?>


<nav class="pager pagenav text-center">
	<?php if ($row->prev) :
		$direction = $lang->isRtl() ? 'right' : 'left'; ?>
		<span class="previous">
			<a class="hasTooltip btn btn-outline-<?php echo $btnColor ?> btn-sm" title="<?php echo htmlspecialchars($rows[$location - 1]->title); ?>" aria-label="<?php echo JText::sprintf('JPREVIOUS_TITLE', htmlspecialchars($rows[$location - 1]->title)); ?>" href="<?php echo $row->prev; ?>" rel="prev">
				<?php echo '<span class="icon-arrow-' . $direction . '" aria-hidden="true"></span> <span aria-hidden="true">' . $row->prev_label . '</span>'; ?>
			</a>
		</span>
	<?php endif; ?>
	<?php if ($row->next) :
		$direction = $lang->isRtl() ? 'left' : 'right'; ?>
		<span class="next">
			<a class="hasTooltip btn btn-outline-<?php echo $btnColor ?> btn-sm" title="<?php echo htmlspecialchars($rows[$location + 1]->title); ?>" aria-label="<?php echo JText::sprintf('JNEXT_TITLE', htmlspecialchars($rows[$location + 1]->title)); ?>" href="<?php echo $row->next; ?>" rel="next">
				<?php echo '<span aria-hidden="true">' . $row->next_label . '</span> <span class="icon-arrow-' . $direction . '" aria-hidden="true"></span>'; ?>
			</a>
		</span>
	<?php endif; ?>
</nav>