<?php

/**
 * Filter settings code snippet
 *
 * @copyright 2010 Pukunui Technology
 * @author S.Elliott, Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package filter-langflip
 */

$settings->add(new admin_setting_configcheckbox('filter_langflip_reverse', get_string('reversestring','filter_langflip'), '', 1));

$settings->add(new admin_setting_configcheckbox('filter_langflip_upsidedown', get_string('upsidedownstring','filter_langflip'), '', 1));

?>
