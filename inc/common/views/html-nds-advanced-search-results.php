<?php

$transient_name = $this->transient_search_cpt['autosuggest_transient'];

// retrieve the post types to search.
$plugin_options = get_option( $this->plugin_name );
$post_types = array_keys( $plugin_options, "1" );

// check if cached posts are available.
$cached_posts = get_transient( $transient_name );
if ( false === $cached_posts ) {

	// retrieve posts by running a new loop and cache the posts the transients as well.
	$cached_posts = $this->cache_posts_in_post_types();
}

/**
 * Extract the cached post ids from the transient into an array.
 * the post ids were cached when the auto suggest was invoked.
 */
$cached_post_ids = array_column( $cached_posts, 'id' );
// run a loop to search against post ids for the cpts.
$args = array(
	'post_type'           => $post_types,
	'posts_per_page'      => -1,
	'post__in'            => $cached_post_ids,
	'ignore_sticky_posts' => true,
	's'                   => $search_term,
);
$search_query = new \WP_Query( $args );
?>
<div class="nds-search-results">
<ul class="flex-grid-container">
	<!-- Start the Loop. -->
	<?php
	if ( $search_query->have_posts() ) :
		while ( $search_query->have_posts() ) :
			$search_query->the_post();
	?>

	<li class="flex-grid-item">

		<!-- the thumbnail -->
		<p>
			<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
			<?php endif; ?>
		</p>
		<!-- title -->
		<p class="card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</p>
		<!-- excerpt -->
		<p class="card-excerpt">
			<?php esc_html_e( substr( get_the_content(), 0, 150 ) . ' ... ' ); ?>
		</p>

		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>

		<?php else : ?>
		<?php echo __( 'Nothing Found ...', $this->plugin_text_domain ); ?>
		<?php endif; ?>

	</li> <!-- flex-grid-item -->

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>></div>
</ul> <!-- flex-grid-container -->
</div> <!-- nds-search-results -->

