<?php
/**
 * One-click demo content: recreates the Figma design's pages, menus and posts.
 *
 * Runs once when the theme is activated (and again from Tools › Estatein Demo).
 * It is idempotent: existing items (matched by slug) are reused, never duplicated.
 * Images are copied from the theme's assets into the Media Library.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Run the importer after activation (only the first time).
 */
function estatein_maybe_import_demo() {
	if ( ! get_option( 'estatein_demo_imported' ) && current_user_can( 'manage_options' ) ) {
		estatein_import_demo();
	}
}
add_action( 'after_switch_theme', 'estatein_maybe_import_demo', 20 );

/**
 * Tools › Estatein Demo page (re-run the importer).
 */
function estatein_demo_admin_page() {
	add_management_page(
		__( 'Estatein demo content', 'estatein' ),
		__( 'Estatein Demo', 'estatein' ),
		'manage_options',
		'estatein-demo',
		function () {
			$done = false;
			if ( isset( $_POST['estatein_demo_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estatein_demo_nonce'] ) ), 'estatein_demo' ) ) {
				estatein_import_demo();
				$done = true;
			}
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Estatein demo content', 'estatein' ); ?></h1>
				<?php if ( $done ) : ?>
					<div class="notice notice-success"><p><?php esc_html_e( 'Demo content is in place.', 'estatein' ); ?></p></div>
				<?php endif; ?>
				<p><?php esc_html_e( 'Creates the Home, About Us, Properties, Services and Contact pages, the menus, and the properties, testimonials, FAQs, team members, clients, services and offices from the design. Items that already exist are left untouched.', 'estatein' ); ?></p>
				<form method="post">
					<?php wp_nonce_field( 'estatein_demo', 'estatein_demo_nonce' ); ?>
					<?php submit_button( __( 'Import demo content', 'estatein' ) ); ?>
				</form>
			</div>
			<?php
		}
	);
}
add_action( 'admin_menu', 'estatein_demo_admin_page' );

/**
 * Copy one of the theme's bundled images into the Media Library (once).
 *
 * @param string $name  Image name in the manifest.
 * @param string $alt   Alt text.
 * @return int Attachment ID (0 on failure).
 */
function estatein_demo_image( $name, $alt = '' ) {
	$map = (array) get_option( 'estatein_demo_images', array() );
	if ( ! empty( $map[ $name ] ) && get_post( $map[ $name ] ) ) {
		return (int) $map[ $name ];
	}

	$manifest = estatein_image_manifest();
	if ( empty( $manifest[ $name ] ) ) {
		return 0;
	}
	$width = end( $manifest[ $name ]['widths'] );
	$file  = ESTATEIN_DIR . "/assets/images/{$name}-{$width}.webp";
	if ( ! file_exists( $file ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $name . '.webp' );
	copy( $file, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => $name . '.webp',
			'tmp_name' => $tmp,
		),
		0
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	$map[ $name ] = $id;
	update_option( 'estatein_demo_images', $map, false );
	return (int) $id;
}

/**
 * Save a custom field the way ACF does (value + field-key reference), so values
 * imported here also appear in the ACF editor.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Field name.
 * @param mixed  $value   Value.
 */
function estatein_demo_field( $post_id, $name, $value ) {
	update_post_meta( $post_id, $name, $value );
	update_post_meta( $post_id, '_' . $name, estatein_field_key( $name ) );
}

/**
 * Create (or find) a post by slug.
 *
 * @param string $type  Post type.
 * @param string $slug  Slug.
 * @param array  $args  wp_insert_post arguments.
 * @return int Post ID.
 */
function estatein_demo_post( $type, $slug, $args ) {
	$existing = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	return (int) wp_insert_post(
		array_merge(
			array(
				'post_type'   => $type,
				'post_name'   => $slug,
				'post_status' => 'publish',
			),
			$args
		)
	);
}

/**
 * Get (or create) a term and return its ID.
 *
 * @param string $taxonomy Taxonomy.
 * @param string $name     Name.
 * @param string $slug     Slug.
 * @param string $desc     Description.
 * @return int
 */
function estatein_demo_term( $taxonomy, $name, $slug, $desc = '' ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term ) {
		return (int) $term->term_id;
	}
	$term = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug, 'description' => $desc ) );
	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/**
 * Import everything.
 */
function estatein_import_demo() {
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
	}
	estatein_register_post_types();

	// --- Taxonomies -------------------------------------------------------
	$cat = array(
		'coastal'     => estatein_demo_term( 'property_category', 'Coastal Escapes', 'coastal-escapes', 'Where Waves Beckon' ),
		'urban'       => estatein_demo_term( 'property_category', 'Urban Oasis', 'urban-oasis', 'Life in the Heart of the City' ),
		'countryside' => estatein_demo_term( 'property_category', 'Countryside Charm', 'countryside-charm', "Escape to Nature's Embrace" ),
	);
	$malibu = estatein_demo_term( 'property_location', 'Malibu, California', 'malibu-california' );
	$groups = array(
		'unlock-property-value' => estatein_demo_term( 'service_group', 'Unlock Property Value', 'unlock-property-value' ),
		'property-management'   => estatein_demo_term( 'service_group', 'Effortless Property Management', 'property-management' ),
		'smart-investments'     => estatein_demo_term( 'service_group', 'Smart Investments', 'smart-investments' ),
	);
	$offices = array(
		'headquarters'  => estatein_demo_term( 'office_type', 'Headquarters', 'headquarters' ),
		'regional'      => estatein_demo_term( 'office_type', 'Regional', 'regional' ),
		'international' => estatein_demo_term( 'office_type', 'International', 'international' ),
	);

	// --- Properties ---------------------------------------------------------
	$properties = array(
		'seaside-serenity-villa' => array(
			'title'    => 'Seaside Serenity Villa',
			'content'  => 'Discover your own piece of paradise with the Seaside Serenity Villa. With an open floor plan, breathtaking ocean views from every room, and direct access to a pristine sandy beach, this property is the epitome of coastal living.',
			'image'    => array( 'seaside-serenity-villa', 'Seaside Serenity Villa — white modern villa with a swimming pool' ),
			'cat'      => $cat['coastal'],
			'location' => $malibu,
			'fields'   => array(
				'price'        => 1250000,
				'featured'     => 1,
				'bedrooms'     => 4,
				'bathrooms'    => 3,
				'property_type' => 'Villa',
				'area'         => '2,500 Square Feet',
				'size_sqft'    => 2500,
				'card_text'    => 'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood...',
				'listing_text' => 'Wake up to the soothing melody of waves. This beachfront villa offers...',
				'features'     => implode(
					"\n",
					array(
						'Expansive oceanfront terrace for outdoor entertaining',
						'Gourmet kitchen with top-of-the-line appliances',
						'Private beach access for morning strolls and sunset views',
						'Master suite with a spa-inspired bathroom and ocean-facing balcony',
						'Private garage and ample storage space',
					)
				),
				'cost_transfer_tax_value'      => '$25,000',
				'cost_transfer_tax_note'       => 'Based on the sale price and local regulations',
				'cost_legal_fees_value'        => '$3,000',
				'cost_legal_fees_note'         => 'Approximate cost for legal services, including title transfer',
				'cost_home_inspection_value'   => '$500',
				'cost_home_inspection_note'    => 'Recommended for due diligence',
				'cost_insurance_value'         => '$1,200',
				'cost_insurance_note'          => 'Annual cost for comprehensive property insurance',
				'cost_mortgage_fees_value'     => 'Varies',
				'cost_mortgage_fees_note'      => 'If applicable, consult with your lender for specific details',
				'cost_monthly_tax_value'       => '$1,250',
				'cost_monthly_tax_note'        => 'Approximate monthly property tax based on the sale price and local rates',
				'cost_monthly_hoa_value'       => '$300',
				'cost_monthly_hoa_note'        => 'Monthly fee for common area maintenance and security',
				'cost_initial_listing_value'   => '$1,250,000',
				'cost_initial_fees_value'      => '$29,700',
				'cost_initial_fees_note'       => 'Property transfer tax, legal fees, inspection, insurance',
				'cost_down_payment_value'      => '$250,000',
				'cost_down_payment_note'       => '20%',
				'cost_mortgage_amount_value'   => '$1,000,000',
				'cost_mortgage_amount_note'    => 'If applicable',
				'cost_expense_tax_value'       => '$1,250',
				'cost_expense_hoa_value'       => '$300',
				'cost_expense_mortgage_value'  => 'Varies based on terms and interest rate',
				'cost_expense_mortgage_note'   => 'If applicable',
				'cost_expense_insurance_value' => '$100',
				'cost_expense_insurance_note'  => 'Approximate monthly cost',
			),
			'gallery'  => array(
				array( 'villa-gallery-01', 'Seaside Serenity Villa exterior with infinity pool' ),
				array( 'villa-gallery-02', 'Open-plan living and dining area' ),
				array( 'villa-gallery-03', 'Resort-style swimming pool' ),
				array( 'villa-gallery-04', 'Poolside loungers under parasols' ),
				array( 'villa-gallery-05', 'Bright living room with a blue feature wall' ),
				array( 'villa-gallery-06', 'Living room with timber floors' ),
				array( 'villa-gallery-07', 'Dining area beside the kitchen' ),
				array( 'villa-gallery-08', 'Lounge and dining area' ),
				array( 'villa-gallery-09', 'Master bedroom suite' ),
			),
		),
		'metropolitan-haven'     => array(
			'title'   => 'Metropolitan Haven',
			'content' => 'A chic and fully-furnished 2-bedroom apartment with panoramic city views.',
			'image'   => array( 'metropolitan-haven', 'Metropolitan Haven — city apartment towers behind a green park' ),
			'cat'     => $cat['urban'],
			'fields'  => array(
				'price'         => 650000,
				'featured'      => 1,
				'bedrooms'      => 2,
				'bathrooms'     => 2,
				'property_type' => 'Villa',
				'card_text'     => 'A chic and fully-furnished 2-bedroom apartment with panoramic city views...',
				'listing_text'  => 'Immerse yourself in the energy of the city. This modern apartment in the heart...',
			),
		),
		'rustic-retreat-cottage' => array(
			'title'   => 'Rustic Retreat Cottage',
			'content' => 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community.',
			'image'   => array( 'rustic-retreat-cottage', 'Rustic Retreat Cottage — glass skyscrapers at dusk' ),
			'cat'     => $cat['countryside'],
			'fields'  => array(
				'price'         => 350000,
				'featured'      => 1,
				'bedrooms'      => 3,
				'bathrooms'     => 3,
				'property_type' => 'Villa',
				'card_text'     => 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community...',
				'listing_text'  => 'Find tranquility in the countryside. This charming cottage is nestled amidst rolling hills...',
			),
		),
	);
	$order = 0;
	foreach ( $properties as $slug => $p ) {
		$id = estatein_demo_post( 'property', $slug, array( 'post_title' => $p['title'], 'post_content' => $p['content'], 'menu_order' => ++$order ) );
		if ( ! $id ) {
			continue;
		}
		set_post_thumbnail( $id, estatein_demo_image( $p['image'][0], $p['image'][1] ) );
		wp_set_object_terms( $id, array( $p['cat'] ), 'property_category' );
		if ( ! empty( $p['location'] ) ) {
			wp_set_object_terms( $id, array( $p['location'] ), 'property_location' );
		}
		foreach ( $p['fields'] as $name => $value ) {
			estatein_demo_field( $id, $name, $value );
		}
		if ( ! empty( $p['gallery'] ) ) {
			$ids = array();
			foreach ( $p['gallery'] as $image ) {
				$ids[] = estatein_demo_image( $image[0], $image[1] );
			}
			update_post_meta( $id, 'gallery', implode( ',', array_filter( $ids ) ) );
		}
	}

	// --- Testimonials -------------------------------------------------------
	$testimonials = array(
		array( 'exceptional-service', 'Exceptional Service!', "Our experience with Estatein was outstanding. Their team's dedication and professionalism made finding our dream home a breeze. Highly recommended!", 'Wade Warren', 'USA, California', 'client-wade-warren' ),
		array( 'efficient-and-reliable', 'Efficient and Reliable', "Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We couldn't be happier with the results.", 'Emelie Thomson', 'USA, Florida', 'client-emelie-thomson' ),
		array( 'trusted-advisors', 'Trusted Advisors', 'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!', 'John Mans', 'USA, Nevada', 'client-john-mans' ),
	);
	foreach ( $testimonials as $i => $t ) {
		$id = estatein_demo_post( 'testimonial', $t[0], array( 'post_title' => $t[1], 'post_content' => $t[2], 'menu_order' => $i + 1 ) );
		estatein_demo_field( $id, 'client_name', $t[3] );
		estatein_demo_field( $id, 'client_location', $t[4] );
		estatein_demo_field( $id, 'rating', 5 );
		set_post_thumbnail( $id, estatein_demo_image( $t[5], $t[3] ) );
	}

	// --- FAQs -----------------------------------------------------------------
	$faqs = array(
		array( 'search-properties', 'How do I search for properties on Estatein?', 'Learn how to use our user-friendly search tools to find properties that match your criteria.' ),
		array( 'documents', 'What documents do I need to sell my property through Estatein?', 'Find out about the necessary documentation for listing your property with us.' ),
		array( 'contact-agent', 'How can I contact an Estatein agent?', 'Discover the different ways you can get in touch with our experienced agents.' ),
	);
	foreach ( $faqs as $i => $f ) {
		estatein_demo_post( 'faq', $f[0], array( 'post_title' => $f[1], 'post_excerpt' => $f[2], 'post_content' => $f[2], 'menu_order' => $i + 1 ) );
	}

	// --- Team -----------------------------------------------------------------
	$team = array(
		array( 'max-mitchell', 'Max Mitchell', 'Founder', 'team-max-mitchell' ),
		array( 'sarah-johnson', 'Sarah Johnson', 'Chief Real Estate Officer', 'team-sarah-johnson' ),
		array( 'david-brown', 'David Brown', 'Head of Property Management', 'team-david-brown' ),
		array( 'michael-turner', 'Michael Turner', 'Legal Counsel', 'team-michael-turner' ),
	);
	foreach ( $team as $i => $m ) {
		$id = estatein_demo_post( 'team_member', $m[0], array( 'post_title' => $m[1], 'menu_order' => $i + 1 ) );
		estatein_demo_field( $id, 'role', $m[2] );
		estatein_demo_field( $id, 'social_url', 'https://x.com/' );
		set_post_thumbnail( $id, estatein_demo_image( $m[3], $m[1] ) );
	}

	// --- Clients --------------------------------------------------------------
	$clients = array(
		array( 'abc-corporation', 'ABC Corporation', 'Since 2019', 'Commercial Real Estate', 'Luxury Home Development', "Estatein's expertise in finding the perfect office space for our expanding operations was invaluable. They truly understand our business needs." ),
		array( 'greentech-enterprises', 'GreenTech Enterprises', 'Since 2018', 'Commercial Real Estate', 'Retail Space', "Estatein's ability to identify prime retail locations helped us expand our brand presence. They are a trusted partner in our growth." ),
	);
	foreach ( $clients as $i => $c ) {
		$id = estatein_demo_post( 'client', $c[0], array( 'post_title' => $c[1], 'menu_order' => $i + 1 ) );
		estatein_demo_field( $id, 'since', $c[2] );
		estatein_demo_field( $id, 'website', 'https://example.com/' );
		estatein_demo_field( $id, 'domain', $c[3] );
		estatein_demo_field( $id, 'category', $c[4] );
		estatein_demo_field( $id, 'quote', $c[5] );
	}

	// --- Services ---------------------------------------------------------------
	$services = array(
		'unlock-property-value' => array(
			array( 'valuation-mastery', 'Valuation Mastery', 'Discover the true worth of your property with our expert valuation services.', 'service-valuation' ),
			array( 'strategic-marketing', 'Strategic Marketing', 'Selling a property requires more than just a listing; it demands a strategic marketing<span class="only-desktop"> approach</span>.', 'service-marketing' ),
			array( 'negotiation-wizardry', 'Negotiation Wizardry', 'Negotiating the best deal is an art, and our negotiation experts are masters of it.', 'service-negotiation' ),
			array( 'closing-success', 'Closing Success', 'A successful sale is not complete until the closing. We guide you through the intricate closing process.', 'service-closing' ),
		),
		'property-management'   => array(
			array( 'tenant-harmony', 'Tenant Harmony', 'Our Tenant Management services ensure that your tenants have a smooth and reducing vacancies.', 'service-tenant' ),
			array( 'maintenance-ease', 'Maintenance Ease', 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.', 'service-maintenance' ),
			array( 'financial-peace-of-mind', 'Financial Peace of Mind', 'Managing property finances can be complex. Our financial experts take care of rent collection', 'service-finance' ),
			array( 'legal-guardian', 'Legal Guardian', 'Stay compliant with property laws and regulations effortlessly.', 'feature-investment' ),
		),
		'smart-investments'     => array(
			array( 'market-insight', 'Market Insight', 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions', 'service-valuation' ),
			array( 'roi-assessment', 'ROI Assessment', 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments', 'service-roi' ),
			array( 'customized-strategies', 'Customized Strategies', 'Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs', 'service-strategy' ),
			array( 'diversification-mastery', 'Diversification Mastery', 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations', 'feature-investment' ),
		),
	);
	foreach ( $services as $group => $items ) {
		foreach ( $items as $i => $s ) {
			$id = estatein_demo_post( 'service', $s[0], array( 'post_title' => $s[1], 'post_content' => $s[2], 'menu_order' => $i + 1 ) );
			wp_set_object_terms( $id, array( $groups[ $group ] ), 'service_group' );
			estatein_demo_field( $id, 'icon', $s[3] );
		}
	}

	// --- Offices -----------------------------------------------------------------
	$office_items = array(
		array( 'main-headquarters', 'headquarters', 'Main Headquarters', '123 Estatein Plaza, City Center, Metropolis', 'info@estatein.com', '+1 (123) 456-7890', 'Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.' ),
		array( 'regional-office', 'regional', 'Regional Offices', '456 Urban Avenue, Downtown District, Metropolis', 'info@restatein.com', '+1 (123) 628-7890', "Estatein's presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets." ),
	);
	foreach ( $office_items as $i => $o ) {
		$id = estatein_demo_post( 'office', $o[0], array( 'post_title' => $o[2], 'post_content' => $o[6], 'menu_order' => $i + 1 ) );
		wp_set_object_terms( $id, array( $offices[ $o[1] ] ), 'office_type' );
		estatein_demo_field( $id, 'office_label', $o[2] );
		estatein_demo_field( $id, 'address', $o[3] );
		estatein_demo_field( $id, 'email', $o[4] );
		estatein_demo_field( $id, 'phone', $o[5] );
		estatein_demo_field( $id, 'city', 'Metropolis' );
	}

	// --- Pages + reading settings ------------------------------------------------
	$pages = array(
		'home'                 => array( 'Home', '' ),
		'about-us'             => array( 'About Us', 'page-templates/about.php' ),
		'properties'           => array( 'Properties', 'page-templates/properties.php' ),
		'services'             => array( 'Services', 'page-templates/services.php' ),
		'contact-us'           => array( 'Contact Us', 'page-templates/contact.php' ),
		'terms-and-conditions' => array( 'Terms & Conditions', '' ),
		'blog'                 => array( 'Blog', '' ),
	);
	$page_ids = array();
	foreach ( $pages as $slug => $page ) {
		$page_ids[ $slug ] = estatein_demo_post( 'page', $slug, array( 'post_title' => $page[0] ) );
		if ( $page[1] ) {
			update_post_meta( $page_ids[ $slug ], '_wp_page_template', $page[1] );
		}
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	update_option( 'page_for_posts', $page_ids['blog'] );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( 'Just another WordPress site' === get_option( 'blogdescription' ) || '' === get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', 'Discover Your Dream Property' );
	}

	// --- Menus ---------------------------------------------------------------------
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu = wp_create_nav_menu( 'Primary' );
		if ( ! is_wp_error( $menu ) ) {
			foreach ( array( 'home', 'about-us', 'properties', 'services' ) as $i => $slug ) {
				wp_update_nav_menu_item(
					$menu,
					0,
					array(
						'menu-item-object-id' => $page_ids[ $slug ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $i + 1,
					)
				);
			}
			$locations['primary'] = $menu;
		}
	}
	if ( empty( $locations['footer'] ) ) {
		$menu = wp_create_nav_menu( 'Footer' );
		if ( ! is_wp_error( $menu ) ) {
			$position = 0;
			foreach ( estatein_default_footer_columns() as $title => $links ) {
				$parent = wp_update_nav_menu_item(
					$menu,
					0,
					array(
						'menu-item-title'    => $title,
						'menu-item-url'      => '#',
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => ++$position,
					)
				);
				foreach ( $links as $label => $token ) {
					wp_update_nav_menu_item(
						$menu,
						0,
						array(
							'menu-item-title'     => $label,
							'menu-item-url'       => estatein_link( $token ),
							'menu-item-type'      => 'custom',
							'menu-item-status'    => 'publish',
							'menu-item-parent-id' => $parent,
							'menu-item-position'  => ++$position,
						)
					);
				}
			}
			$locations['footer'] = $menu;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	flush_rewrite_rules();
	update_option( 'estatein_demo_imported', time() );
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * `wp estatein import-demo` — run the importer from the command line.
	 */
	WP_CLI::add_command(
		'estatein import-demo',
		function () {
			estatein_import_demo();
			WP_CLI::success( 'Estatein demo content imported.' );
		}
	);
}
