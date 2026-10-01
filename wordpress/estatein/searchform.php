<?php
/**
 * Search form (blog / 404), styled like the Properties search bar.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form property-search__bar" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="visually-hidden" for="<?php echo esc_attr( $estatein_id ); ?>"><?php esc_html_e( 'Search for:', 'estatein' ); ?></label>
	<input class="property-search__input" id="<?php echo esc_attr( $estatein_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'estatein' ); ?>">
	<button class="btn btn--primary property-search__btn" type="submit"><?php echo estatein_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="property-search__btn-label"><?php esc_html_e( 'Search', 'estatein' ); ?></span></button>
</form>
