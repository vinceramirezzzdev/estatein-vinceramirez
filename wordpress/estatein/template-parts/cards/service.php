<?php
/**
 * Service card (Services page). The card id is the service slug, so footer
 * links such as /services/#valuation-mastery land on it.
 *
 * @package Estatein
 *
 * @var array $args { @type bool $filled Use the filled (investment grid) style. }
 */

defined( 'ABSPATH' ) || exit;

$estatein_icon = estatein_field( 'icon', get_the_ID() );
$estatein_icon = $estatein_icon ? $estatein_icon : 'service-valuation';
?>
<li class="service-card<?php echo ! empty( $args['filled'] ) ? ' service-card--filled' : ''; ?>" id="<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">
	<div class="service-card__head">
		<span class="icon-ring"><span class="icon-ring__inner"><?php echo estatein_icon( $estatein_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
		<h3 class="service-card__title"><?php the_title(); ?></h3>
	</div>
	<p class="service-card__copy"><?php echo estatein_kses( get_post_field( 'post_content' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
</li>
