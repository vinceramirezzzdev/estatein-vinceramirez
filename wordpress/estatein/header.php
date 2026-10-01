<?php
/**
 * Site header: announcement banner + navigation bar.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'estatein' ); ?></a>

<header class="site-header">
	<?php if ( estatein_option( 'banner_enabled' ) ) : ?>
		<div class="banner" data-banner>
			<div class="banner__pattern" aria-hidden="true"></div>
			<p class="banner__text">
				<span><?php echo esc_html( estatein_option( 'banner_text' ) ); ?></span>
				<?php if ( estatein_option( 'banner_link_label' ) ) : ?>
					<a class="banner__link" href="<?php echo esc_url( estatein_link( estatein_option( 'banner_link' ) ) ); ?>"><?php echo esc_html( estatein_option( 'banner_link_label' ) ); ?></a>
				<?php endif; ?>
			</p>
			<button class="banner__close" type="button" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'estatein' ); ?>" data-banner-close>
				<?php echo estatein_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	<?php endif; ?>

	<div class="navbar">
		<div class="navbar__inner">
			<a class="navbar__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'home', 'estatein' ) ); ?>">
				<?php
				if ( has_custom_logo() ) {
					echo wp_get_attachment_image( (int) get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'logo', 'alt' => '' ) );
				} else {
					echo estatein_symbol( 'logo', 'logo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</a>

			<nav class="navbar__nav" id="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'estatein' ); ?>">
				<?php estatein_primary_menu(); ?>
				<a class="btn btn--darker navbar__cta" href="<?php echo esc_url( estatein_page_url( 'contact' ) ); ?>"<?php echo is_page_template( 'page-templates/contact.php' ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( estatein_option( 'header_cta_label' ) ); ?></a>
			</nav>

			<button class="navbar__toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
				<span class="visually-hidden"><?php esc_html_e( 'Menu', 'estatein' ); ?></span>
				<?php echo estatein_icon( 'menu', 'icon--menu' ) . estatein_icon( 'close', 'icon--close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</header>
