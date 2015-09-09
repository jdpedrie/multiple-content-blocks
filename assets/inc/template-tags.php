<?php
/**
 * Multiple content blocks template tags
 *
 * @package MCB
 * @subpackage Template_Tags
 */

/**
 * Display a block
 *
 * @param string $name The name of the block
 * @param array $args Optional. Additional arguments, see get_the_block for more information.
 *
 * @return string
 */
function the_block($name) {
	echo get_the_block( $name);
}

/**
 * Return a block
 *
 * @param string $name The name of the block
 *
 * @return string
 */
function get_the_block($name) {
	if( ! empty( $name ) ) {
		$post = mcb_get_post();

		$filename = explode('.', basename($post->page_template))[0];
		$manifest = sprintf('%s/mu-plugins/content-blocks/%s.json', WP_CONTENT_DIR, $filename);
		if (file_exists($manifest)) {
			$args = (array) json_decode(file_get_contents($manifest), true);
		}

		$meta = get_post_meta( $post->ID, '_mcb-' . sanitize_title( $name ), true );

		if( isset($args['apply_filters']) && $args['apply_filters'] )
			return apply_filters( 'the_content', $meta );

		if( $meta && 0 < count( $meta ) )
			return htmlentities( $meta, null, 'UTF-8', false );
	}

	return '';
}

/**
 * Check if the block has content
 *
 * @param string $name
 * @param array $args Optional. Additional arguments, see get_the_block for more information
 */
function has_block( $name ) {
	if( 0 < strlen( get_the_block( $name ) ) )
		return true;

	return false;
}

/**
 * Get current post
 */
function mcb_get_post() {
	global $post;
	$block_post = $post;

	if( 'page' == get_option( 'show_on_front' ) && is_home() && ! $GLOBALS['wp_query']->in_the_loop )
		$block_post = get_post( get_option( 'page_for_posts' ) );

	return $block_post;
}
