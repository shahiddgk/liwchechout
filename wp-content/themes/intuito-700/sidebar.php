<div class=product-sidebar>
	<ul>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Products Sidebar') ) : ?><?php endif; ?>
		<li style="display:none">
			<?php include 'php/mailchimp_form.php'; ?>
		</li>
	</ul>
</div>