<?php
/**
 * Four feature tiles with ring icons (Home and Services; Contact passes its own items).
 *
 * @package Estatein
 *
 * @var array $args {
 *     @type string $id    Optional id attribute.
 *     @type string $label aria-label of the list.
 *     @type array  $items Optional items: array( 'title', 'url', 'icon', 'links' => array( label => url ) ).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'id'    => '',
		'label' => __( 'What Estatein offers', 'estatein' ),
		'items' => array(),
	)
);

if ( ! $args['items'] ) {
	$icons = array( 'feature-home', 'feature-value', 'feature-management', 'feature-investment' );
	foreach ( $icons as $i => $icon ) {
		$n               = $i + 1;
		$args['items'][] = array(
			'title' => estatein_option( "feature{$n}_title" ),
			'url'   => estatein_link( estatein_option( "feature{$n}_link" ) ),
			'icon'  => $icon,
		);
	}
}
?>
<ul class="feature-strip"<?php echo $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?> aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<?php foreach ( $args['items'] as $item ) : ?>
		<?php
		$ring  = '<span class="icon-ring"><span class="icon-ring__inner">' . estatein_icon( $item['icon'] ) . '</span></span>';
		$arrow = estatein_icon( 'tile-arrow', 'feature-tile__arrow' );
		?>
		<?php if ( ! empty( $item['links'] ) ) : ?>
			<li class="feature-tile feature-tile--static">
				<?php echo $ring; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="feature-tile__title feature-tile__links">
					<?php foreach ( $item['links'] as $label => $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</span>
				<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</li>
		<?php else : ?>
			<li class="feature-tile">
				<a class="feature-tile__link" href="<?php echo esc_url( $item['url'] ); ?>">
					<?php echo $ring; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="feature-tile__title"><?php echo esc_html( $item['title'] ); ?></span>
					<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
