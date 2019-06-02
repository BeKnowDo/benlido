<?php if (function_exists('get_field')) {
	$show_hero 						= get_field('show_hero_section');
	$show_building_a_kit_section 	= get_field('show_building_a_kit_section');
	$show_shipping_section 			= get_field('show_shipping_section');
	$show_returns_section 			= get_field('show_returns_section');
	$show_hours_section 			= get_field('show_hours_section');
} ?>

<div class="bl-help">
	<?php if ($show_hero ): ?>
		<div class="hero hero-home full-background triangle-background" style="background-image: url(<?php echo esc_url( get_field('hero_image') ); ?>)">
			<div class="max-width-xl">
				<div class="columns">
					<div class="column col-12 col-mx-auto">
						<p class="hero-sub-header"></p>
						<h1 class="hero-header"><?php echo esc_html( get_field('hero_title') ); ?></h1>
						<p class="hero-copy"></p>
						<div class="hero-action-buttons columns">
							<div class="column col-xs-12 col-sm-12 col-6 col-mx-auto">
								<a href="mailto:<?php echo esc_attr( get_field('questions_email') ); ?>" class="btn btn-primary btn-lg btn-block ">Questions</a>
							</div>
							<div class="column col-xs-12 col-sm-12 col-6 col-mx-auto">
								<a href="mailto:<?php echo esc_attr( get_field('business_email') ); ?>" class="btn btn-primary btn-lg btn-block ">Business Inquiries</a>
							</div>
							</div>
						</div>
						<div class="column col-md-12 col-xl-5 col-5 col-ml-auto d-none">
						<div class="hero-image-container">
							<img class="hero-image hero-image" src="/wp-content/uploads/2018/09/help_hero.jpg" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif ?>
	<div class="max-width-lg">
		<div class="container bl-help-container">
			<div class="columns">
				<div class="column col-xs-12 col-sm-12 col-md-7 col-lg-8 col-8">
					<div class="columns">
						<?php if ($show_building_a_kit_section ): ?>
						<div class="column col-xs-12 col-sm-12 col-11">
							<div class="hero hero-help">
								<h1 class="hero-help-header"><?php echo esc_html( get_field('bak_section_title') ); ?></h1>
								<p class="hero-help-copy">
									<?php echo esc_html( get_field('bak_description') ); ?>
									<br/>
								</p>
								<ul class="hero-help-list">
									<li>You can:</li>
									<?php if( have_rows('list_of_what_you_can_do') ):

									 	// loop through the rows of data
									    while ( have_rows('list_of_what_you_can_do') ) : the_row(); ?>

									        <li class="hero-help-list-ordered">
												<?php echo the_sub_field('text'); ?>
											</li>

									    <?php endwhile;

									endif; ?>
								</ul>
								<br>
								<?php echo get_field('bak_observatons'); ?>
							</div>
						</div>
						<?php endif ?>
						<?php if ($show_shipping_section ): ?>
						<div class="column col-xs-12 col-sm-12 col-11">
							<div class="hero hero-help">
								<h1 class="hero-help-header"><?php echo esc_html( get_field('s_section_title') ); ?></h1>
								<ul class="hero-help-list">
									<li class="hero-help-sentinel">
										<div><?php echo esc_html( get_field('s_title') ); ?></div>
										<?php echo esc_html( get_field('s_subtitle') ); ?>
									</li>
									<?php if( have_rows('shipping_list') ):

									 	// loop through the rows of data
									    while ( have_rows('shipping_list') ) : the_row(); ?>

											<li>
												<div><?php echo the_sub_field('title'); ?></div>
												<?php echo the_sub_field('subtitle'); ?>
											</li>

									    <?php endwhile;

									endif; ?>
								</ul>
								<br>
								<p class="hero-help-disclaimer">
									<?php echo esc_html( get_field('s_observations') ); ?>
								</p>
							</div>
						</div>
						<?php endif ?>
						<?php if ($show_returns_section ): ?>
						<div class="column col-xs-12 col-sm-12 col-11">
							<div class="hero hero-help">
								<h1 class="hero-help-header"><?php echo esc_html( get_field('r_section_title') ); ?></h1>
								<ul class="hero-help-list">
									<li>
										<?php echo esc_html( get_field('r_description') ); ?>
									</li>
								</ul>
							</div>
						</div>
						<?php endif ?>
					</div>
				</div>
				<div class="column col-xs-12 col-sm-12 col-md-5 col-lg-4 col-4">
					<div class="bg-grey">
						<?php if ($show_hours_section ): ?>
						<div class="bl-hours">
							<h1 class="bl-hours-header"><?php echo esc_html( get_field('h_section_title') ); ?></h1>
							<ul class="bl-hours-list">
								<li>
									Monday - Friday <?php echo esc_html( get_field('monday_friday') ); ?>
								</li>
								<li>
									Saturday - Sunday <?php echo esc_html( get_field('saturday_sunday') ); ?>
								</li>
							</ul>
							<div class="bl-contact-list">
								<?php echo get_field('h_description'); ?>
							</div>
						</div>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>