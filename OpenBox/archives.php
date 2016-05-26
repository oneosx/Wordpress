<?php 
/*
Template Name: Archives
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<?php get_header(); ?>
<main id="main">
	<?php get_sidebar(); ?>
	<div class="main">
		<?php if(have_posts()) : ?><?php while(have_posts()) : the_post(); ?>
		<?php archives_list(); ?>
		<?php endwhile; ?>
		<?php endif; ?>
		<nav class="navigation">
			<ul>
				<?php pagenavi(); ?>
			</ul>
		</nav>
	</div>
</main>
<?php get_footer(); ?>