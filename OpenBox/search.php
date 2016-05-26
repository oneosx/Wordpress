<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<?php get_header(); ?>
<main id="main">
	<?php get_sidebar(); ?>
	<div class="main">

		<?php if ( !have_posts() ) : ?>
		<div class="something">
			<h1>有关 <strong><?php echo htmlspecialchars($s); ?></strong> 的搜索结果</h1>
			<p>抱歉，没有找到有关<strong><?php echo htmlspecialchars($s); ?></strong>的结果，请换个关键词找找看吧！</p>
			<h2>建议您：</h2>
			<ol>
				<li>简化关键词；如：搜索“什么是欧频优艺”，为了获得更多的结果，可以简化为：“欧频优艺”。</li>
				<li>换个关键词。</li>
			</ol>
		</div>
		
		<?php else: ?>
		<div class="something">
			<h1>有关 <strong><?php echo htmlspecialchars($s); ?></strong> 的搜索结果</h1>
		</div>
		<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
		<section class="section">
			<div class="headbox">
				<a href="<?php echo site_url( '/post/author/', $scheme );?><?php the_author_login(); ?>" title="<?php echo $authordata->display_name;?>" class="author">
					<?php echo get_avatar( get_the_author_email(), 24, $default, $alt=get_comment_author($id) ); ?>
				</a>
				<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				<div class="meta">
					<span class="time"><?php the_time(); ?></span>
					<cite class="cite">
						<?php $category = get_the_category(); 
							if($category[0]){
							echo '<a href="'.get_category_link($category[0]->term_id ).'" title="'.$category[0]->cat_name.'">'.$category[0]->cat_name.'</a>';
						} ?>
					</cite>
					<span class="read"><?php post_views('', ''); ?>人围观</span>
					<span class="says"><?php comments_popup_link( '0', '1', '%', '', '' ); ?>条评论</span>
				</div>
			</div>
			<p class="contbox">
				<a href="<?php the_permalink(); ?>"><img src="<?php bloginfo('template_url');?>/timthumb.php?src=<?php echo post_thumbnail_src(); ?>&h=140&w=190&zc=1" class="thumb" alt="<?php the_title(); ?>" /></a>
				<?php echo mb_strimwidth(strip_tags(apply_filters('the_content', $post->post_content)), 0, 280,"···"); ?>
			</p>
		</section>
		<?php endwhile; ?>
		<?php endif; ?>
		<?php endif; ?>
		<nav class="navigation">
			<ul>
				<?php pagenavi(); ?>
			</ul>
		</nav>
	</div>
</main>
<?php get_footer(); ?>