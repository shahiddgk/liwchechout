<div class="subs-popup">
	<div class="mailchimp-form-container">
		<div class="email-close">
			<i class="fa fa-times-circle button" aria-hidden="true"></i>
		</div>
		<div class="subscribe-header">
			<h1 class="subscribe-title">
				<?php the_field('email_signup_title', 'option'); ?>
			</h1>
			<p>
				<?php the_field('email_signup_text', 'option'); ?>
			</p>
		</div>
		<div class="subscribe-form">
			<form method="post" action="https://LummiislandWild.us13.list-manage.com/subscribe/post">
				<input type="hidden" name="u" value="16b2dd7a422c7997d52f3ac68">
				<input type="hidden" name="id" value="386713dc82">
				<p>
					<label for="mailchimp-first">First Name</label><br />
					<input id="mailchimp-fist" type="text" name="MERGE1" />
				</p>
				<p>
					<label for="mailchimp-last">Last Name</label><br />
					<input id="mailchimp-last" type="text" name="MERGE2" />
				</p>
				<p>
					<label for="mailchimp-email">Email</label><br />
					<input id="mailchimp-email" type="email" name="MERGE0" />
				</p>
				<p style=display:none>	
					<label for="mailchimp-interest">Most Interested In:</label>
					<select id="mailchimp-interest" name="MERGE4">
						<option></option>
						<option>Wild Salmon</option>
						<option>Kaviar</option>
						<option>Shellfish</option>
						<option>Smoked Salmon</option>
						<option>Tuna - Halibut - Black Cod</option>
						<option>Everything</option>
					</select>
				</p>
				<p>
					<input type="hidden" name="MERGE3" value="General Interest" />
					<input type="submit" name="submit" value="Submit" />
				</p>
			</form>
		</div>
	</div>
</div>

<style>
	/*
	.mailchimp-form-container h2 { display:block!important; }
	.mailchimp-form-container h2 + p { padding-bottom:12px; } */
	.subscribe-header p { color:white; } 
	.mailchimp-form-container {position: relative; width: 550px; height: auto; margin: 100px auto; box-shadow: 0px 0px 30px 5px rgb(0,0,0); background: #FFFFFF; }
	.subscribe-header { display: block; width: 100%; height: auto; padding: 40px; background: #002e6d url('<?php bloginfo('template_directory'); ?>/images/email-bkgnd.jpg'); }
	.subscribe-form { padding:40px; }
	h1.subscribe-title { font-family: Lora,TimesNewRoman,"Times New Roman",Times,serif; font-weight: 700; font-size: 2em; line-height: 1em; letter-spacing: 1px; text-stroke: 1px #FFFFFF; color: #FFFFFF;
		text-shadow: 3px 3px 0 rgba(139, 110, 75, 1); margin: 0px 0px 10px 0px; text-transform: uppercase; width: 100%; }
	.email-close { display: block; height: 27px; width: 27px; position: absolute; top: -7px; right: -7px; text-align: center; cursor: pointer; z-index: 999; border: 3px solid #8b6e4b; border-radius: 50px; }
	.email-close i { font-size: 1.5em; line-height: .9em; color: #FFFFFF; }
	.subscribe-form input { line-height: 22pt; border: 1px solid #eee; font-size: 13pt; height:33px; width:100%; background-color:#fcfdff; }
	.subscribe-form input[type=submit] { margin-top:12px; cursor:pointer; width: auto; background-color:#002e6d;     border: 1px solid #81694E;
    color: white;
    text-transform: uppercase; }
	.subscribe-form  select { line-height: 22pt; border: 1px solid #eee; font-size: 13pt; height:33px; }
	.subs-popup { top:0; }
	 @media screen and (max-width: 600px) {
		 .mailchimp-form-container {
			 width:90%;
			 margin:120px auto;
		 }
	}
</style>