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
	<div class="main page">
		<article class="article">
			<header class="headerbox">
				<h1><?php the_title(); ?></h1>
			</header>
			<div class="content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php comments_template('', true);?>
	</div>
	<?php endwhile; ?>	
	<?php endif; ?>
</main>
<?php get_footer(); ?>