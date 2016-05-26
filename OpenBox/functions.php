<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<?php
//禁用工具条
show_admin_bar(false);

//禁止代码标点转换
remove_filter('the_content', 'OpenUE');

/*Disable the emoji's*/
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
add_action( 'init', 'disable_emojis' );
function disable_emojis_tinymce( $plugins ) {
	return array_diff( $plugins, array( 'wpemoji' ) );
}
 
/*Removes RSD, XMLRPC, WLW, WP Generator, ShortLink and Comment Feed links*/
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'feed_links_extra', 3 );

/*Removes prev and next article links*/
add_filter( 'index_rel_link', '__return_false' );
add_filter( 'parent_post_rel_link', '__return_false' );
add_filter( 'start_post_rel_link', '__return_false' );
add_filter( 'previous_post_rel_link', '__return_false' );
add_filter( 'next_post_rel_link', '__return_false' );

/*禁用Google字体，拖慢后台速度*/
add_filter( 'gettext_with_context', 'wpdx_disable_open_sans', 888, 4 );
function wpdx_disable_open_sans( $translations, $text, $context, $domain ) {
  if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
    $translations = 'off';
  }
  return $translations;
}

/*让分类和标签的描述支持 HTML */
remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'pre_link_description', 'wp_filter_kses' );
remove_filter( 'pre_link_notes', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );

/*Gravater for SSL*/
function get_ssl_avatar($avatar) {
	$avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="https://cn.gravatar.com/avatar/$1?s=$2" alt="avatar" class="avatar avatar-$2" height="$2" width="$2">',$avatar);
	return $avatar;
}

/*默认头像*/
add_filter( 'avatar_defaults', 'newgravatar' );   
function newgravatar ($avatar_defaults) {  
    $myavatar = get_bloginfo('template_directory') . '/style/img/author.png';  
    $avatar_defaults[$myavatar] = "自定义默认头像";  
    return $avatar_defaults;  
}

/*Wordpress thumbnail for timthumb*/
add_filter('get_avatar', 'get_ssl_avatar');												//添加特色缩略图支持
	if ( function_exists('add_theme_support') )add_theme_support('post-thumbnails');	//输出缩略图地址
	function post_thumbnail_src(){
	    global $post;
		if( $values = get_post_custom_values("thumb") ) {								//输出自定义域图片地址
			$values = get_post_custom_values("thumb");
			$post_thumbnail_src = $values [0];
		} elseif( has_post_thumbnail() ){												//如果有特色缩略图，则输出缩略图地址
	        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID),'full');
			$post_thumbnail_src = $thumbnail_src [0];
	    } else {
			$post_thumbnail_src = '';
			ob_start();
			ob_end_clean();
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			$post_thumbnail_src = $matches [1] [0];										//获取该图片URL
			if(empty($post_thumbnail_src)){
				$random = mt_rand(1, 10);
				echo get_bloginfo('template_url');
				//echo '/style/img/'.$random.'.jpg';									//如果文章中没有图片，则显示自定义随机图片
				echo '/style/img/thumbnail.png';										//如果日志中没有图片，则显示默认图片，二选一
			}
		};
		echo $post_thumbnail_src;
}

/* 访问计数<?php post_views(' ', ' 次'); ?> */
function record_visitors() {
	if (is_singular())
	{
		global $post;
		$post_ID = $post->ID;
		if($post_ID)
		{
			$post_views = (int)get_post_meta($post_ID, 'views', true);
			if(!update_post_meta($post_ID, 'views', ($post_views+1)))
			{
				add_post_meta($post_ID, 'views', 1, true);
			}
		}
	}
}
add_action('wp_head', 'record_visitors');
function post_views($before = '(点击 ', $after = ' 次)', $echo = 1) {
	global $post;
	$post_ID = $post->ID;
	$views = (int)get_post_meta($post_ID, 'views', true);
	if ($echo) echo $before, number_format($views), $after;
	else 
		return $views;
}
/* 访问总排行 <?php get_most_viewed_format(); ?> */
function get_most_viewed_format($mode = '', $limit = 5, $show_date = 0, $term_id = 0) {
	global $wpdb, $post;
	$output = '';
	$mode = ($mode == '') ? 'post' : $mode;
	$type_sql = ($mode != 'both') ? "AND post_type='$mode'" : '';
	$term_sql = (is_array($term_id)) ? "AND $wpdb->term_taxonomy.term_id IN (" . join(',', $term_id) . ')' : ($term_id != 0 ? "AND $wpdb->term_taxonomy.term_id = $term_id" : '');
	$term_sql.= $term_id ? " AND $wpdb->term_taxonomy.taxonomy != 'link_category'" : '';
	$inr_join = $term_id ? "INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)" : '';
	// database query
	$most_viewed = $wpdb->get_results("SELECT ID, post_date, post_title, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) $inr_join WHERE post_status = 'publish' AND post_password = '' $term_sql $type_sql AND meta_key = 'views' GROUP BY ID ORDER BY views DESC LIMIT $limit");
	if ($most_viewed) {
		foreach ($most_viewed as $viewed) {
		$post_ID    = $viewed->ID;
		$post_views = number_format($viewed->views);//浏览量
		$post_title = esc_attr($viewed->post_title);//文章标题
		$get_permalink = esc_attr(get_permalink($post_ID));//文章链接
		if ($show_date) {//是否显示日期
			$posted = date(get_option('date_format'), strtotime($viewed->post_date));
			$output .= "$posted";
		}
		$output .= "<li><a href='$get_permalink' title='$post_title'>$post_title</a></li>";
		}
	} else {
		$output = "<span>N/A</span>n";
	 }
	echo $output;
}

/*最新/热评/随机*/
//最新日志
//some_posts( $orderby = 'date', $plusmsg = 'post_date', 10 );
//热评日志
//some_posts( $orderby = 'comment_count', $plusmsg = 'commentcount', 10 );
//随机日志
//some_posts( $orderby = 'rand', $plusmsg = 'post_date', 10 );
function filter_where($where = '') {
    $where .= " AND post_date > '" . date('Y-m-d', strtotime('-90 days')) . "'";
    return $where;
}
function some_posts($orderby = '', $plusmsg = '',$limit = 5) {
    add_filter('posts_where', 'filter_where');
    $some_posts = query_posts('posts_per_page='.$limit.'&caller_get_posts=1&orderby='.$orderby);
    foreach ($some_posts as $some_post) {
            $output = '';
            $post_date = mysql2date('y年m月d日', $some_post->post_date);
            $commentcount = ''.$some_post->comment_count.'';
            $post_title = htmlspecialchars(stripslashes($some_post->post_title));
            $permalink = get_permalink($some_post->ID);
            $output .= '<li><a href="' . $permalink . '" title="'.$post_title.'">' . $post_title . '</a></li>';
            echo $output;
        }
    wp_reset_query();
}

/*Changing the_time*/
add_filter('the_time', 'past_date');
function past_date(){
	$suffix='前';
	$endtime='2419200';
	$day = '天';
	$hour = '小时';
	$minute = '分钟';
	$second = '秒';
	if ($_SERVER['REQUEST_TIME'])
			$now_time = $_SERVER['REQUEST_TIME'];
	else
			$now_time = time();
	$m = 60;  // 一分钟
	$h = 3600;  //一小时有3600秒
	$d = 86400;  // 一天有86400秒
	$endtime = (int)$endtime;  // 结束时间
	$post_time = get_post_time('U', true);
	$past_time = $now_time - $post_time;  // 文章发表至今经过多少秒
	if($past_time < $m){ //小于1分钟
			$past_date = $past_time . $second;
	}else if ($past_time < $h){ //小于1小时
			$past_date = $past_time / $m;
			$past_date = floor($past_date);
			$past_date .= $minute;
	}else if ($past_time < $d){ //小于1天
			$past_date = $past_time / $h;
			$past_date = floor($past_date);
			$past_date .= $hour;
	}else if ($past_time < $d*10){
			$past_date = $past_time / $d;
			$past_date = floor($past_date);
			$past_date .= $day;
	}else{
			echo get_post_time('Y-m-d');
			return;
	}
	echo $past_date . $suffix;
}

/*图片自动增加属性*/
add_filter( 'the_content', 'image_alt');
	function image_alt($c) {
		global $post;
		$title = $post->post_title;
		$s = array('/src="(.+?.(jpg|bmp|png|jepg|gif))"/i'=> 'src="$1" alt="'.$title.'"');
		foreach($s as$p => $r){
			$c = preg_replace($p,$r,$c);
		}
		return$c;
}

/*去除文章图片width height属性*/
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}

/*分页*/
function pagenavi() {
	global $paged, $wp_query;
	if ( !$max_page ) {
		$max_page = $wp_query->max_num_pages;
	}
	if( $max_page > 1 ) {

		if( !$paged ) {
			$paged = 1;
		}
		if( $paged == 1 ) {
			echo '<li class="prev"><a class="not-allowed">上一页</a></li>';
			echo '<li class="total"><span>第';
			echo $paged.'/'.$max_page;
			echo '页</span></li>';
			echo '<li class="next">';
			echo next_posts_link(' 下一页 ');
			echo '</li>';
		}
		if( $paged > 1 && $paged != $max_page ) {
			echo '<li class="prev">';
			echo previous_posts_link(' 上一页 ');
			echo '</li>';
			echo '<li class="total"><span>第';
			echo $paged.'/'.$max_page;
			echo '页</span></li>';
			echo '<li class="next">';
			echo next_posts_link(' 下一页 ');
			echo '</li>';
		}
		if( $paged == $max_page ) {
			echo '<li class="prev">';
			echo previous_posts_link(' 上一页 ');
			echo '</li>';
			echo '<li class="total"><span>第';
			echo $paged.'/'.$max_page;
			echo '页</span></li>';
			echo '<li class="next"><a class="not-allowed">下一页</a></li>';
		}
	}
}

/*this menu*/
register_nav_menus( array(
	'headernav-menu' => __( 'topnav' ),
	'asidemenu-menu' => 'menu for aside'
));

/*Archives Page*/
function archives_list() {
	if( !$output = get_option('open_db_cache_archives_list') ){
		$output = '<div id="archives"><p><a id="al_expand_collapse" href="#">展开/收缩</a></p>';
		$args = array(
			'post_type' => 'post', //如果你有多个 post type，可以这样 array('post', 'product', 'news')  
			'posts_per_page' => -1, //全部 posts
			'ignore_sticky_posts' => 1 //忽略 sticky posts

		);
		$the_query = new WP_Query( $args );
		$posts_rebuild = array();
		$year = $mon = 0;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$post_year = get_the_time('Y');
			$post_mon = get_the_time('m');
			$post_day = get_the_time('d');
			if ($year != $post_year) $year = $post_year;
			if ($mon != $post_mon) $mon = $post_mon;
			$posts_rebuild[$year][$mon][] = '<li>'. get_the_time('d日: ') .'<a href="'. get_permalink() .'">'. get_the_title() .'</a> <em>('. get_comments_number('0', '1', '%') .')</em></li>';
		endwhile;
		wp_reset_postdata();

		foreach ($posts_rebuild as $key_y => $y) {
			$output .= '<h3 class="al_year">'. $key_y .' 年</h3><ul class="al_mon_list">'; //输出年份
			foreach ($y as $key_m => $m) {
				$posts = ''; $i = 0;
				foreach ($m as $p) {
					++$i;
					$posts .= $p;
				}
				$output .= '<li><span class="al_mon">'. $key_m .' 月 <em> ( '. $i .' 篇文章 )</em></span><ul class="al_post_list">'; //输出月份
				$output .= $posts; //输出 posts
				$output .= '</ul></li>';
			}
			$output .= '</ul>';
		}

		$output .= '</div>';
		update_option('open_db_cache_archives_list', $output);
	}
	echo $output;
}
function clear_db_cache_archives_list() {
	update_option('open_db_cache_archives_list', ''); // 清空 archives_list
}
add_action('save_post', 'clear_db_cache_archives_list'); // 新发表文章/修改文章时

/** Comments **/
require get_template_directory() . '/ajax-comment/do.php';
//comments link New
add_filter( "comment_text", "filter_comment_content" );
function filter_comment_content( $comment_content ){
    return str_replace( "<a ", "<a target='_blank' rel='external nofollow' ", $comment_content );
}
add_filter( "get_comment_author_link", "change_comment_author_link" );
function change_comment_author_link( $author_link ){
    return str_replace( "<a ", "<a target='_blank' ", $author_link );
}
//评论添加@
function OpenUCD_comment_add_at( $comment_text, $comment = '') {
	if( $comment->comment_parent > 0) {
	$comment_text = '@<a class="url" href="#comment-' . $comment->comment_parent . '">'.get_comment_author( $comment->comment_parent ) . '</a>：' . $comment_text;
	}
	return $comment_text;
}
add_filter( 'comment_text' , 'OpenUCD_comment_add_at', 20, 2);
function OpenUCD_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;//主评论计数器
	global $commentcount, $page, $wpdb;
	if ( (int) get_option('page_comments') === 1 && (int) get_option('thread_comments') === 1 ) { //开启嵌套评论和分页才启用
		if(!$commentcount) { //初始化楼层计数器
			$page = ( !empty($in_comment_loop) ) ? get_query_var('cpage') : get_page_of_comment( $comment->comment_ID, $args ); //获取当前评论列表页码
			$cpp = get_option('comments_per_page'); //获取每页评论显示数量
			if ( get_option('comment_order') === 'desc' ) { //倒序
				/*$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND comment_type = 'all' AND comment_approved = '1' AND !comment_parent");*/
				$cnt = get_comments( array('status' => 'approve','parent' => '0','post_id' => get_the_ID(),'count' => true) ); //获取主评论总数量
				if (ceil($cnt / $cpp) == 1 || ($page > 1 && $page  == ceil($cnt / $cpp))) { //如果评论只有1页或者是最后一页，初始值为主评论总数
					$commentcount = $cnt + 1;
				} else {
					$commentcount = $cpp * $page + 1;
				}
			} else {
				$commentcount = $cpp * ($page - 1);
			}
		}
		if ( !$parent_id = $comment->comment_parent ) {
			$commentcountText = '<span class="reply-info">';
			if ( get_option('comment_order') === 'desc' ) { //倒序
				$commentcountText .= --$commentcount . '楼';
			} else {
				switch ($commentcount) {
					case 0:
						$commentcountText .= '<i>1楼</i>'; ++$commentcount;
						break;
					case 1:
						$commentcountText .= '<i>2楼</i>'; ++$commentcount;
						break;
					case 2:
						$commentcountText .= '<i>3楼</i>'; ++$commentcount;
						break;
					default:
						$commentcountText .= ++$commentcount . '楼';
						break;
				}
			}
			$commentcountText .= '</span>';
		}
	}
?>
    <li id="li-comment-<?php comment_ID(); ?>" class="comment-box">

		<div class="guest" title="<?php printf(__('%s'), get_comment_author()); ?>">
			<?php if (function_exists('get_avatar') && get_option('show_avatars')) { echo get_avatar( $comment, 24, $default, $alt=get_comment_author($id) ); } ?>
			<span class="nickname"><?php printf(__('%s'), get_comment_author_link()); ?></span>
		</div>

		<div class="sad">

			<?php comment_text(); ?>
			<?php if ($comment->comment_approved == '0') : ?>
            <span class="audit">你的评论正在审核，稍后会显示出来！</span>
        	<?php endif; ?>

        	<div id="comment-<?php comment_ID(); ?>" class="reply">
				<span class="reply-info"><?php echo $commentcountText; //主评论楼层号 ?></span>
				<span class="reply-info"><?php echo get_comment_time('Y-m-d H:i'); ?></span>
				<span class="reply-info"><?php comment_reply_link(array_merge( $args, array('reply_text' => '回复','depth' => $depth, 'max_depth' => $args['max_depth']))) ?></span>
				<?php $theedit   = edit_comment_link(__('编辑'), '<span class="reply-info">', '</span>');
				if( $theedit ) {
				echo $theedit;
				} else { echo ''; } ; ?>
			</div>

        </div>
		
		<div class="clear"></div>
	</li>
	<li class="childbox">
<?php
}

?>