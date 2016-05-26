<?php 
/*
Powered by OpenUE.com
version: V0.1 beta
 */
?>
		<div id="comments">
			<h5>文章评论</h5>
			<?php
		    if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		        die ('哎哟，客官走错路了，世上可木有那么多捷径哦！');
			?>
			
			<div id="respond">
				<fieldset>
				<?php if (function_exists('wp_list_comments')) : ?>
				<?php cancel_comment_reply_link(__('取消回复', 'OpenUCD')) ?>
				<?php endif; ?>
				<?php 
				if ( !comments_open() ) :
				elseif ( get_option('comment_registration') && !is_user_logged_in() ) : 
				?>
				<div class="secondary">你必须 <a href="<?php echo wp_login_url( get_permalink() ); ?>">登录</a> 才能发表评论！</div>
				<?php else  : ?>

						<form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
							<textarea id="comment" name="comment"></textarea>
							<div class="send">
							<?php if ( $user_ID ) : ?>
							<?php 
								if (function_exists('wp_logout_url')) {
									$logout_link = wp_logout_url();
								} else {
									$logout_link = get_option('siteurl') . '/wp-login.php?action=logout';
								}
							?>
							<span class="myuser">
								<?php _e('您已登录 »', 'OpenUCD'); ?>
								<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a>
								<small> | </small>
								<a href="<?php echo $logout_link; ?>" title="<?php _e('退出登录', 'OpenUCD'); ?>"><?php _e('退出登录', 'OpenUCD'); ?></a>
							</span>
							<?php else : ?>
								<div class="group">
									<span>ＱＱ</span>
									<input type="text" id="qq" />
									<input type="button" id="sendqq" value="快速填写" />
								</div>
								<div class="group">
									<span>昵称</span>
									<input type="text" id="author" name="author" placeholder="*必填" />
								</div>
								<div class="group">
									<span>邮箱</span>
									<input type="text" id="email" name="email" placeholder="*必填" />
								</div>
								<div class="group">
									<span>网址</span>
									<input type="text" id="url" name="url" placeholder="http://" />
								</div>
								<?php endif; ?>
								<input type="submit" id="submit" name="submit" value="提交" />
							</div>
							<?php comment_id_fields(); ?>
							<?php do_action('comment_form', $post->ID); ?>
						</form>

					<?php endif; ?>
				</fieldset>
			</div>

			<?php comments_popup_link( '还木有评论，来一发吧！', '共有 1位 小伙伴在此出没！', '共有 %位 小伙伴在此出没！', 'sce', '评论已关闭' ); ?>

			<?php 
		    if (!empty($post->post_password) && $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { 
		    ?>
		    <div class="msg">请输入密码再查看评论内容！</div>
		    <?php 
		        } else if ( !comments_open() ) {
		    ?>
			<div class="msg">对不起，评论功能已经关闭！</div>
		    <?php 
		        } else if ( !have_comments() ) {
		    ?>
			<?php 
	        } else {
	            echo '<ul class="comment-list">';
				echo wp_list_comments('type=comment&callback=OpenUCD_comment');
				echo '</ul>';
				echo '<div id="comments-nav">';
				echo paginate_comments_links('prev_text=上一页&next_text=下一页');
				echo '</div>';
	        }
	    	?>
		</div>