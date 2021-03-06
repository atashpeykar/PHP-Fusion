<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: breadcrumbs.php
| Author: JoiNNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined('IN_FUSION')) { die('Access Denied'); }

/**
 * Add a link to the breadcrumb
 *
 * @param array $link Keys: link, title
 */
function add_breadcrumb(array $link = array()) {
	$breadcrumbs = &get_breadcrumbs();

	$link += array(
				'title' => '',
				'link' => ''
			);
	$link['title'] = trim($link['title']);
	if (!empty($link['title'])) {
		$breadcrumbs[] = $link;
	}
}

/**
 * Get breadcrumbs
 *
 * @return array Keys of elements: title, link
 */
function &get_breadcrumbs() {
	static $breadcrumbs = array();

	return $breadcrumbs;
}

function catFullPath($cat_id, $cat_tbl, $col_id, $col_parent, $col_title) {
	$tmp_id = $cat_id;
	while ($tmp_id > 0) {
		$result = dbquery("SELECT ".$col_id.", ".$col_parent.", ".$col_title." FROM ".$cat_tbl." WHERE ".$col_id."='".$tmp_id."'");
		$tmp_id = 0;
		if (dbrows($result)) {
			$data = dbarray($result);
			$cat_item = array('id' => $data[$col_id], 'parent' => $data[$col_parent], 'title' => $data[$col_title]);
			$cat_list[] = $cat_item;
			$tmp_id = $data[$col_parent];
		} else {
			return false;
		}
	}
	return array_reverse($cat_list);
}

?>