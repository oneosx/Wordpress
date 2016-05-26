<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<?php get_header(); ?>
<main id="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php get_sidebar(); ?>
	<div class="main">
		<article class="article">
			<header class="headbox">
				<a href="<?php echo site_url( '/post/author/', $scheme );?><?php the_author_login(); ?>" title="<?php echo $authordata->display_name;?>" class="author"><?php echo get_avatar( get_the_author_email(), 24 ); ?></a>
				<h1><?php the_title(); ?></h1>
				<div class="meta">
					<span class="time" title="发表于<?php the_time(); ?>"><?php the_time(); ?></span>
					<?php if ( (get_the_modified_time('Ymd')) > (get_the_time('Ymd')) ) : ?>
					<time class="uptime" title="更新于<?php the_modified_time('Y-m-j'); ?>"><?php the_modified_time('Y-m-j'); ?></time>
					<?php else : null ?>
					<?php endif; ?>
					<cite class="cite">
						<?php $category = get_the_category(); 
							if($category[0]){
							echo '<a href="'.get_category_link($category[0]->term_id ).'" title="'.$category[0]->cat_name.'">'.$category[0]->cat_name.'</a>';
						} ?>
					</cite>
					<span class="tm"><i>&copy;</i> 本文由“<a href="<?php echo site_url( '/post/author/', $scheme );?><?php the_author_login(); ?>"><?php echo $authordata->display_name;?></a>” 
					<?php 
						$CopyRight = get_post_meta($post->ID, 'CopyRight', true);
						if($CopyRight) {
						echo '转载于'.'<a href="'.$CopyRight.'" target="blank" rel="nofollow">网络</a>，版权归原作者所有！';
						}
						else echo '原创，转载请注明出处！';
					?>
					</span>
				</div>
				<div class="reading"><?php post_views('', ''); ?><i>人围观</i></div>
			</header>
			<div class="content">
				<?php the_content(); ?>
			</div>
		</article>
		<div class="relaed">
			<h5><span class="tag"><?php the_tags( '', '', '' ); ?></span><span class="other">作者其他文章</span></h5>
			<div class="col-two">
				<?php if(is_single()){
				    $query = new WP_Query(
				        array(
				            'author' => $post->post_author,
				            'posts_per_page' => 3,
				            'post__not_in' => array($post->ID),
				        )
				    );
				    $posts = $query->posts;
				?>
				<ol>
				<?php foreach($posts as $k => $p): ?>
				    <li><a href="<?php echo get_permalink($p->ID); ?>" title="<?php echo $p->post_title ?>"><?php echo $p->post_title ?></a></li>
				<?php endforeach; ?>
				</ol>
				<?php
				}
				else {
					echo '<span>作者暂时没有发表其他文章！</span>';
				}
				wp_reset_query();
				?>
			</div>
			<div class="col-two">
				<div class="super"></div>
				<div class="share"></div>
			</div>
		</div>
		<div class="rela">
			<ul>
				<li class="prev">
				<?php $prev_post = get_previous_post();
				if (!empty( $prev_post )): ?>
					<a href="<?php echo get_permalink( $prev_post->ID ); ?>" rel="prev" title="<?php echo $prev_post->post_title; ?>">
						<i>&laquo; 上一篇</i>
						<strong><?php echo $prev_post->post_title; ?></strong>
					</a>
				<?php else : ?>
					<a>
						<i>&laquo; 上一篇</i>
						<strong>没有了，已经是最后文章</strong>
					</a>
				<?php endif; ?>
				</li>
				<li class="next">
				<?php $next_post = get_next_post();
				if (!empty( $next_post )): ?>
					<a href="<?php echo get_permalink( $next_post->ID ); ?>" rel="next" title="<?php echo $next_post->post_title; ?>">
						<i>下一篇 &raquo;</i>
						<strong><?php echo $next_post->post_title; ?></strong>
					</a>
				<?php else : ?>
					<a>
						<i>下一篇 &raquo;</i>
						<strong>没有了，已经是最新文章</strong>
					</a>
				<?php endif; ?>
				</li>
			</ul>
		</div>
		<div class="rela">
			<div class="col-two">
				<h5>相关文章</h5>
				<ol>
				<?php
				global $post, $wpdb;
				$post_tags = wp_get_post_tags($post->ID);
				if ($post_tags) {
				    $tag_list = '';
				    foreach ($post_tags as $tag) {
				        // 获取标签列表
				        $tag_list .= $tag->term_id.',';
				    }
				    $tag_list = substr($tag_list, 0, strlen($tag_list)-1);

				    $related_posts = $wpdb->get_results("
				        SELECT DISTINCT ID, post_title
				        FROM {$wpdb->prefix}posts, {$wpdb->prefix}term_relationships, {$wpdb->prefix}term_taxonomy
				        WHERE {$wpdb->prefix}term_taxonomy.term_taxonomy_id = {$wpdb->prefix}term_relationships.term_taxonomy_id
				        AND ID = object_id
				        AND taxonomy = 'post_tag'
				        AND post_status = 'publish'
				        AND post_type = 'post'
				        AND term_id IN (" . $tag_list . ")
				        AND ID != '" . $post->ID . "'
				        ORDER BY RAND()
				        LIMIT 5");

				    if ( $related_posts ) {
				        foreach ($related_posts as $related_post) {
				?>
				    <li><a href="<?php echo get_permalink($related_post->ID); ?>" rel="bookmark" title="<?php echo $related_post->post_title; ?>"><?php echo $related_post->post_title; ?></a></li>
				<?php   }
				    }
				    else {
				      echo '<span>暂无相关文章</span>';
				    }
				}
				else {
				  echo '<span>暂无相关文章</span>';
				}
				?>
				</ol>
			</div>
			<div class="col-two">
				<h5>热门文章</h5>
				<ol>
					
				<?php some_posts( $orderby = 'comment_count', $plusmsg = 'commentcount', 5 ); ?>

				</ol>
			</div>
		</div>
		<?php comments_template('', true);?>
	</div>
	<?php endwhile; ?>	
	<?php endif; ?>
</main>

<?php get_footer(); ?>