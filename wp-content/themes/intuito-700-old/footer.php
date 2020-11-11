	</div><!--End Wrapper-->
	<div class=pre-footer>
		<?php include ('mailchimp.php'); ?>
	</div>
    <div class="footer">
		<div class=footer-top>

				<?php
					if( have_rows('social_links','option') ):
						echo '<ul class=social-links>';
						while ( have_rows('social_links','option') ) : the_row(); ?>
							<li>
								<a href="<?php the_sub_field('link'); ?>" title="<?php the_sub_field('name'); ?>">
									<i class="<?php the_sub_field('icon'); ?>" aria-hidden="true"></i>
								</a>
							</li>
						<?php endwhile;
						echo '</ul>';
					endif;
				?>

		</div>
		<div class="footer-middle">
				<a href="/lwu-login/" class="login">ACCOUNT LOGIN</a>
		</div>
		<div class=footer-bottom>
			<div class=inner>
				<div class=footer-logo>
					<a href="<?php bloginfo('url'); ?>">
						<img src="<?php bloginfo('template_url'); ?>/images/logo-footer.png" alt="Lummi Island Wild Logo" />
					</a>
				</div>

				<div class=footer-col>
					<h4>Account</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_six',
							'container' => false,
							'menu_id' => 'footer_six'
						));
					?>

					<a href="/buyers-club/" class="gold-button">Buyers Club</a>

				</div>

				<div class=footer-col>
					<h4>Our Seafood</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_five',
							'container' => false,
							'menu_id' => 'footer_five'
						));
					?>
				</div>

				<div class=footer-col>
					<h4>The Product</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_four',
							'container' => false,
							'menu_id' => 'footer_four'
						));
					?>

					<h4>The Purpose</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_three',
							'container' => false,
							'menu_id' => 'footer_three'
						));
					?>

				</div><!-- Footer Col End -->

				<div class=footer-col>
					<h4>The People</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_one',
							'container' => false,
							'menu_id' => 'footer_one'
						));
					?>

					<h4>The Place</h4>
					<?php
						wp_nav_menu( array(
							'theme_location' => 'footer_two',
							'container' => false,
							'menu_id' => 'footer_two'
						));
					?>
				</div><!-- Footer Col End -->
				<div class=clear></div>
			</div>
		</div>
    </div><!--End Footer-->
</div>
	<?php wp_footer(); ?>
	<script src="<?php bloginfo('template_directory'); ?>/js/lightbox.js"></script>
		<!-- Start of LiveChat (www.livechatinc.com) code -->
		<script type="text/javascript">
		window.__lc = window.__lc || {};
		window.__lc.license = 7017201;
		(function() {
		  var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
		  lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
		  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
		})();
	</script>
	<!-- End of LiveChat code -->

</body>
</html>
