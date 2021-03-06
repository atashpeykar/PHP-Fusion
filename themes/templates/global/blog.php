<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: blog.php
| Author: Frederick MC Chan (Hien)
| Version : 9.00
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/

if (!function_exists('render_main_blog')) {
	function render_main_blog($info) {
		echo render_breadcrumbs();
		echo "<div class='row'>\n";
		echo "<div class='col-xs-12 col-sm-9 overflow-hide'>\n";
		if (isset($_GET['readmore'])) {
			echo display_blog_item($info);
		} else {
			echo display_blog_index($info);
		}
		echo "</div><div class='col-xs-12 col-sm-3'>\n";
		echo display_blog_menu($info);
		echo "</div>\n";
		echo "</div>\n";
	}
}

if (!function_exists('display_blog_item')) {
	function display_blog_item($info) {
		global $locale;
		ob_start();
		$data = $info['blog_item'];
		if ($data['admin_link']) {
			$admin_actions = $data['admin_link'];
			echo "<div class='btn-group pull-right'>\n";
			echo "<a class='btn btn-default btn-sm' href='".$admin_actions['edit']."'>".$locale['edit']."</a>\n";
			echo "<a class='btn btn-default btn-sm' href='".$admin_actions['delete']."'>".$locale['delete']."</a>\n";
			echo "</div>\n";
		}
		echo "<h2 class='strong m-t-0 m-b-20'>".$data['blog_subject']."</h2>";
		echo "<div class='m-b-20'>".$data['blog_post_author']." ".$data['blog_post_time']." ".$data['blog_post_cat']."</div>\n";
		echo "<div class='clearfix m-b-20'>\n";
		if ($data['blog_image']) {
			echo "<div class='m-10 m-l-0 ".$data['blog_ialign']."'>".$data['blog_thumb_2']."</div>";
		}
		echo $data['blog_extended'];
		echo "</div>\n";
		if ($info['blog_nav']) {
			echo "<div class='clearfix m-b-20'><div class='pull-right'>";
			echo $info['blog_nav'];
			echo "</div>\n</div>\n";
		}
		echo "<div class='m-b-20 well'>".$data['blog_author_info']."</div>";
		if ($data['blog_allow_comments']) {
			showcomments("B", DB_BLOG, "blog_id", $_GET['readmore'], BASEDIR."blog.php?readmore=".$_GET['readmore']);
		}

		if ($data['blog_allow_ratings']) {
			showratings("B", $_GET['readmore'], BASEDIR."blog.php?readmore=".$_GET['readmore']);
		}
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}
}

if (!function_exists('display_blog_index')) {
	function display_blog_index($info) {
		global $locale;
		ob_start();
		if (!empty($info['blog_item'])) {
			foreach($info['blog_item'] as $blog_id => $data) {
				echo "
					<div class='clearfix m-b-20'>
						<div class='row'>
							<div class='col-xs-12 col-sm-4'>
								<div class='pull-left m-r-5'>".$data['blog_user_avatar']."</div>
								<div class='overflow-hide'>
									".$data['blog_user_link']." <br/>
									<span class='m-r-10 text-lighter'><i class='fa fa-comment-o fa-fw'></i> ".$data['blog_comments']."</span><br/>
									<span class='m-r-10 text-lighter'><i class='fa fa-star-o fa-fw'></i> ".$data['blog_count_votes']."</span><br/>
									<span class='m-r-10 text-lighter'><i class='fa fa-eye fa-fw'></i> ".$data['blog_reads']."</span><br/>
								</div>
							</div>
							<div class='col-xs-12 col-sm-8'>
								<h2 class='strong m-b-20 m-t-0'><a class='text-dark' href='".$data['blog_link']."'>".$data['blog_subject']."</a></h2>
								<i class='fa fa-clock-o m-r-5'></i> ".$locale['global_049']." ".timer($data['blog_datestamp'])." ".$locale['in']." ".$data['blog_category_link']."
								".($data['blog_cat_image'] ? "<div class='blog-image m-10 ".$data['blog_ialign']."'>".$data['blog_cat_image']."</div>" : '')."
								<div class='m-t-20'>".$data['blog_blog']."<br/>".$data['blog_readmore_link']."</div>
							</div>
						</div>
						<hr>
					</div>
				";
			}
		} else {
			echo "<div class='well text-center'>".$locale['blog_3000']."</div>\n";
		}
		?>
		<?php
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}
}

/**
 * Recursive Menu Generator
 * @param     $info
 * @param int $cat_id
 * @param int $level
 * @return string
 */

if (!function_exists('blog_cat_menu')) {
	function blog_cat_menu($info, $cat_id = 0, $level = 0) {
		$html = '';
		if (!empty($info[$cat_id])) {
			foreach($info[$cat_id] as $blog_cat_id => $cdata) {
				$active = ($blog_cat_id == $_GET['cat_id'] && $_GET['cat_id'] !=='') ? 1 : 0;
				$html .= "<li ".($active ? "class='active strong'" : '')." >".str_repeat('&nbsp;', $level)." ".$cdata['blog_cat_link']."</li>\n";
				if ($active && $blog_cat_id !=0) {
					if (!empty($info[$blog_cat_id])) {
						$html .= blog_cat_menu($info, $blog_cat_id, $level++);
					}
				}
			}
		}
		return $html;
	}
}

if (!function_exists('display_blog_menu')) {
	function display_blog_menu($info) {
		global $locale;
		ob_start();
		echo "<ul class='m-b-40'>\n";
		foreach($info['blog_filter'] as $filter_key => $filter) {
			echo "<li ".(isset($_GET['type']) && $_GET['type'] == $filter_key ? "class='active strong'" : '')." ><a href='".$filter['link']."'>".$filter['title']."</a></li>\n";
		}
		echo "</ul>\n";

		echo "<div class='text-bigger strong text-dark m-b-20 m-t-20'><i class='fa fa-list m-r-10'></i> ".$locale['blog_1003']."</div>\n";
		echo "<ul class='m-b-40'>\n";
		$blog_cat_menu = blog_cat_menu($info['blog_categories']);
		if (!empty($blog_cat_menu)) {
			echo $blog_cat_menu;
		} else {
			echo "<li>".$locale['blog_3001']."</li>\n";
		}
		echo "</ul>\n";

		echo "<div class='text-bigger strong text-dark m-t-20 m-b-20'><i class='fa fa-calendar m-r-10'></i> ".$locale['blog_1004']."</div>\n";
		echo "<ul class='m-b-40'>\n";
		if (!empty($info['blog_archive'])) {
			$current_year = 0;
			foreach($info['blog_archive'] as $year => $archive_data) {
				if ($current_year !== $year) {
					echo "<li class='text-dark strong'>".$year."</li>\n";
				}
				if (!empty($archive_data)) {
					foreach($archive_data as $month => $a_data) {
						echo "<li ".($a_data['active'] ? "class='active strong'" : '').">
						<a href='".$a_data['link']."'>".$a_data['title']."</a> <span class='badge m-l-10'>".$a_data['count']."</span>
						</li>\n";
					}
				}
				$current_year = $year;
			}
		} else {
			echo "<li>".$locale['blog_3002']."</li>\n";
		}
		echo "</ul>\n";

		echo "<div class='text-bigger strong text-dark m-t-20 m-b-20'><i class='fa fa-users m-r-10'></i> ".$locale['blog_1005']."</div>\n";
		echo "<ul class='m-b-40'>\n";
		if (!empty($info['blog_author'])) {
			foreach($info['blog_author'] as $author_id => $author_info) {
				echo "<li ".($author_info['active'] ? "class='active strong'" : '').">
					<a href='".$author_info['link']."'>".$author_info['title']."</a> <span class='badge m-l-10'>".$author_info['count']."</span>
					</li>\n";
			}
		} else {
			echo "<li>".$locale['blog_3003']."</li>\n";
		}
		echo "</ul>\n";
		$str = ob_get_contents();
		ob_end_clean();
		return $str;
	}
}