<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php get_template_part( 'inc/title' );?></title>
<?php /*--link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style/css/app.css"--*/?>
<link rel="stylesheet" href="http://cdn.openue.com/css/app.css">
<link href="<?php echo get_template_directory_uri(); ?>/style/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<!--[if lt IE 9]>
<script src="http://cdn.openue.com/js/html5shiv.min.js"></script>
<script src="http://cdn.openue.com/js/respond.min.js"></script>
<![endif]-->
<?php wp_head(); ?>
</head>
<body>

<header id="header">
	<nav class="header">
		<div class="logo">
			<a href="/"></a>
		</div>
		<div class="topbar">
			<div class="leftbar">
				<?php wp_nav_menu( array( 'theme_location' => 'headernav-menu' )); ?>
			</div>
			<div class="rightbar">
				<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
					<input type="text" id="sobox" name="s" value="<?php the_search_query(); ?>" placeholder="输入搜索内容" />
					<button type="submit" id="sbt"></button>
				</form>
			</div>
		</div>
		<div id="menubox"></div>
		<div class="menubox">
			<?php wp_nav_menu( array( 'theme_location' => 'asidemenu-menu' )); ?>
			<?php wp_nav_menu( array( 'theme_location' => 'headernav-menu' )); ?>
		</div>
	</nav>
</header>
