<?php if (function_exists('get_field')) {
	$show_hero 		= get_field('show_hero_section');
	$show_statement = get_field('show_statement_section');
	$show_quote 	= get_field('show_quote_section');
	$show_history 	= get_field('show_history_section');
} ?>
<div class="bl-about-us">
	<?php if ($show_hero): ?>
		<div class="hero-title-copy text-center bg-gray" style="background-image: url(<?php echo esc_url( get_field('hero_image') ); ?>);">
			<div class="columns">
				<div class="column col-xs-12 col-sm-12 col-10 col-mx-auto">
					<h1 class="hero-title-copy-header"><?php echo esc_html( get_field('hero_title') ); ?></h1>
				</div>
			</div>
		</div>
	<?php endif ?>
	<?php if ($show_statement): ?>
		<div class="hero-title-copy text-center  bg-white  ">
			<div class="columns">
				<div class="column col-xs-12 col-sm-12 col-10 col-mx-auto">
					<h1 class="hero-title-copy-header"><?php echo esc_html( get_field('statement_title') ); ?></h1>
					<p class="hero-title-copy-copy">
						<?php echo esc_html( get_field('statement_description') ); ?>
					</p>
					<span id="bl-animated-gif" class="bl-animated-gif"></span>
				</div>
			</div>
		</div>
	<?php endif ?>
	<?php if ($show_quote): ?>
		<div class="hero-title-copy text-center  bg-gray hero-title-copy-quote" style="background-image: url(<?php echo esc_url( get_field('quote_section_background') ); ?>)">
			<div class="columns">
				<div class="column col-xs-12 col-sm-12 col-10 col-mx-auto">
					<p class="hero-title-quote">
						<?php echo esc_html( get_field('quote_text') ); ?>
					</p>
					<p class="hero-title-quote"><?php echo esc_html( get_field('quote_author') ); ?></p>
				</div>
			</div>
		</div>
	<?php endif ?>
	<?php if ($show_history): ?>
		<div class="hero-title-copy text-center bg-gray">
			<div class="columns">
				<div class="column col-xs-12 col-sm-12 col-10 col-mx-auto">
					<h1 class="hero-title-copy-header"><?php echo esc_html( get_field('history_title') ); ?></h1>
					<p class="hero-title-copy-copy">
						<?php echo esc_html( get_field('history_description') ); ?>
					</p>
				</div>
			</div>
		</div>
	<?php endif ?>
</div>