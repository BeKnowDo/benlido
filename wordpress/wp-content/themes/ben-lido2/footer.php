<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>
		<i id="dimmed-overlay" class="dimmed-overlay"></i>
		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'storefront_before_footer' ); ?>
	<?php
			/**
			 * NOTE: unhooked storefront_footer action
			 *
			 * @UNHOOKED storefront_footer_widgets - 10
			 * @UNHOOKED storefront_credit         - 20
			 */
			do_action( 'storefront_footer' ); 
			get_template_part( 'template-parts/partials/global/footer' );
	?>

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
