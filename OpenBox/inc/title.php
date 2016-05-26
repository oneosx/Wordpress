<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<?php 
	if(is_front_page() || is_home()) {
		bloginfo('name');
		echo " - ";
		bloginfo('description');
	} else if(is_single() || is_page()) {
		wp_title('');
	} else if(is_category()) {
		printf('《%1$s》的所有文章', single_cat_title('', false));
	} else if(is_search()) {
		printf('《%1$s》的搜索结果', wp_specialchars($s, 1));
	} else if(is_tag()) {
		printf('《%1$s》的所有文章', single_tag_title('', false));
	} else if(is_date()) {
		$title = '';
		if(is_day()) {
			$title = get_the_time('Y年n月j日');
		} else if(is_year()) {
			$title = get_the_time('Y年');
		} else {
			$title = get_the_time('Y年n月');
		}
		printf('《%1$s》的所有文章', $title);
	} else if(is_author()) {
		$author = the_author();
		printf('发表的所有文章', $author);
	} else {
		bloginfo('name');
	}
?>