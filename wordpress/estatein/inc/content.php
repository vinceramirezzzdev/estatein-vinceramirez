<?php
/**
 * Design copy registry.
 *
 * Every piece of text from the Figma file lives here once. It is used as:
 *   - the default value of the matching ACF field / Customizer setting,
 *   - the fallback when a field is empty (so the theme renders the design
 *     even before ACF or the demo content are installed).
 *
 * Links may use theme tokens resolved by estatein_link():
 *   "@about", "@properties#portfolio", "@home#faqs", "@testimonials" …
 *
 * Copy that changes per breakpoint wraps the extra words in
 * <span class="hide-mobile"> / <span class="only-desktop"> etc., exactly like the
 * static build.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Editable page content, grouped by page template and tab.
 * Each field: key => array( type, label, default ).
 *
 * @return array
 */
function estatein_page_fields() {
	$stats = function ( $prefix ) {
		return array(
			"{$prefix}_stat1_value" => array( 'text', 'Stat 1 value', '200+' ),
			"{$prefix}_stat1_label" => array( 'text', 'Stat 1 label', 'Happy Customers' ),
			"{$prefix}_stat2_value" => array( 'text', 'Stat 2 value', '10k+' ),
			"{$prefix}_stat2_label" => array( 'text', 'Stat 2 label', 'Properties For Clients' ),
			"{$prefix}_stat3_value" => array( 'text', 'Stat 3 value', '16+' ),
			"{$prefix}_stat3_label" => array( 'text', 'Stat 3 label', 'Years of Experience' ),
		);
	};

	return array(
		'front-page' => array(
			'title'    => 'Home page content',
			'location' => array( 'page_type', 'front_page' ),
			'tabs'     => array(
				'Hero'                 => array_merge(
					array(
						'home_hero_title'  => array( 'text', 'Title', 'Discover Your Dream Property with Estatein' ),
						'home_hero_copy'   => array( 'textarea', 'Intro', 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.' ),
						'home_hero_image'  => array( 'image', 'Image (leave empty for the design image)', '' ),
						'home_badge_text'  => array( 'text', 'Circular badge text', '✨Discover Your Dream Property ' ),
						'home_btn1_label'  => array( 'text', 'Button 1 label', 'Learn More' ),
						'home_btn1_link'   => array( 'text', 'Button 1 link', '@about' ),
						'home_btn2_label'  => array( 'text', 'Button 2 label', 'Browse Properties' ),
						'home_btn2_link'   => array( 'text', 'Button 2 link', '@properties' ),
					),
					$stats( 'home' )
				),
				'Featured Properties'  => array(
					'home_featured_title' => array( 'text', 'Title', 'Featured Properties' ),
					'home_featured_copy'  => array( 'textarea', 'Intro', 'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein.<span class="hide-mobile"> Click "View Details" for more information.</span>' ),
					'home_featured_btn'   => array( 'text', 'Button label', 'View All Properties' ),
				),
				'Testimonials'         => array(
					'home_testimonials_title' => array( 'text', 'Title', 'What Our Clients Say' ),
					'home_testimonials_copy'  => array( 'textarea', 'Intro', 'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.' ),
					'home_testimonials_btn'   => array( 'text', 'Button label', 'View All Testimonials' ),
				),
			),
		),

		'about'      => array(
			'title'    => 'About page content',
			'location' => array( 'page_template', 'page-templates/about.php' ),
			'tabs'     => array(
				'Our Journey'          => array_merge(
					array(
						'about_journey_title' => array( 'text', 'Title', 'Our Journey' ),
						'about_journey_copy'  => array( 'textarea', 'Intro', "Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we've expanded our reach, forged valuable partnerships, and gained the trust of countless clients." ),
						'about_journey_image' => array( 'image', 'Image (leave empty for the design image)', '' ),
					),
					$stats( 'about' )
				),
				'Our Values'           => array(
					'about_values_title' => array( 'text', 'Title', 'Our Values' ),
					'about_values_copy'  => array( 'textarea', 'Intro', 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.' ),
					'about_value1_title' => array( 'text', 'Value 1 title', 'Trust' ),
					'about_value1_copy'  => array( 'textarea', 'Value 1 text', 'Trust is the cornerstone of every successful real estate transaction.' ),
					'about_value2_title' => array( 'text', 'Value 2 title', 'Excellence' ),
					'about_value2_copy'  => array( 'textarea', 'Value 2 text', 'We set the bar high for ourselves. From the properties we list to the services we provide.' ),
					'about_value3_title' => array( 'text', 'Value 3 title', 'Client-Centric' ),
					'about_value3_copy'  => array( 'textarea', 'Value 3 text', 'Your dreams and needs are at the center of our universe. We listen, understand.' ),
					'about_value4_title' => array( 'text', 'Value 4 title', 'Our Commitment' ),
					'about_value4_copy'  => array( 'textarea', 'Value 4 text', 'We are dedicated to providing you with the highest level of service, professionalism<span class="only-desktop">, and support</span><span class="hide-mobile">.</span>' ),
				),
				'Our Achievements'     => array(
					'about_achievements_title' => array( 'text', 'Title', 'Our Achievements' ),
					'about_achievements_copy'  => array( 'textarea', 'Intro', 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.' ),
					'about_achievement1_title' => array( 'text', 'Achievement 1 title', '3+ Years of Excellence' ),
					'about_achievement1_copy'  => array( 'textarea', 'Achievement 1 text', "With over 3 years in the industry, we've amassed a wealth of knowledge and experience<span class=\"only-desktop\">, becoming a go-to resource for all things real estate</span>." ),
					'about_achievement2_title' => array( 'text', 'Achievement 2 title', 'Happy Clients' ),
					'about_achievement2_copy'  => array( 'textarea', 'Achievement 2 text', 'Our greatest achievement is the satisfaction of our clients. Their success stories fuel our passion for what we do.' ),
					'about_achievement3_title' => array( 'text', 'Achievement 3 title', 'Industry Recognition' ),
					'about_achievement3_copy'  => array( 'textarea', 'Achievement 3 text', "We've earned the respect of our peers and industry leaders, with accolades and awards that reflect our commitment to excellence." ),
				),
				'How It Works'         => array(
					'about_steps_title' => array( 'text', 'Title', 'Navigating the Estatein Experience' ),
					'about_steps_copy'  => array( 'textarea', 'Intro', "At Estatein, we've designed a straightforward process to help you find and purchase your dream property with ease. Here's a step-by-step guide to how it all works." ),
					'about_step1_title' => array( 'text', 'Step 1 title', 'Discover a World of Possibilities' ),
					'about_step1_copy'  => array( 'textarea', 'Step 1 text', 'Your journey begins with exploring our carefully curated property listings. Use our intuitive search tools to filter properties based on your preferences, including location,<span class="only-desktop"> type, size, and budget.</span>' ),
					'about_step2_title' => array( 'text', 'Step 2 title', 'Narrowing Down Your Choices' ),
					'about_step2_copy'  => array( 'textarea', 'Step 2 text', "Once you've found properties that catch your eye, save them to your account or make a shortlist. This allows you to compare and revisit your favorites as you make your decision." ),
					'about_step3_title' => array( 'text', 'Step 3 title', 'Personalized Guidance' ),
					'about_step3_copy'  => array( 'textarea', 'Step 3 text', 'Have questions about a property or need more information? Our dedicated team of real estate experts is just a call or message away.' ),
					'about_step4_title' => array( 'text', 'Step 4 title', 'See It for Yourself' ),
					'about_step4_copy'  => array( 'textarea', 'Step 4 text', "Arrange viewings of the properties you're interested in. We'll coordinate with the property owners and accompany you to ensure you get a firsthand look at your potential new home." ),
					'about_step5_title' => array( 'text', 'Step 5 title', 'Making Informed Decisions' ),
					'about_step5_copy'  => array( 'textarea', 'Step 5 text', 'Before making an offer, our team will assist you with due diligence, including property inspections, legal checks, and market analysis. We want you to be fully informed<span class="hide-laptop"> and confident in your choice</span>.' ),
					'about_step6_title' => array( 'text', 'Step 6 title', 'Getting the Best Deal' ),
					'about_step6_copy'  => array( 'textarea', 'Step 6 text', "We'll help you negotiate the best terms and prepare your offer. Our goal is to secure the property at the right price and on favorable terms." ),
				),
				'Team & Clients'       => array(
					'about_team_title'    => array( 'text', 'Team title', 'Meet the Estatein Team' ),
					'about_team_copy'     => array( 'textarea', 'Team intro', 'At Estatein, our success is driven by the dedication and expertise of our team. Get to know the people behind our mission to make your real estate dreams a reality.' ),
					'about_clients_title' => array( 'text', 'Clients title', 'Our Valued Clients' ),
					'about_clients_copy'  => array( 'textarea', 'Clients intro', "At Estatein, we have had the privilege of working with a diverse range of clients across various industries. Here are some of the clients we've had the pleasure of serving" ),
				),
			),
		),

		'properties' => array(
			'title'    => 'Properties page content',
			'location' => array( 'page_template', 'page-templates/properties.php' ),
			'tabs'     => array(
				'Hero & search' => array(
					'props_hero_title'         => array( 'text', 'Title', 'Find Your Dream Property' ),
					'props_hero_copy'          => array( 'textarea', 'Intro', 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey' ),
					'props_search_placeholder' => array( 'text', 'Search placeholder', 'Search For A Property' ),
					'props_search_btn'         => array( 'text', 'Search button', 'Find Property' ),
				),
				'Listings'      => array(
					'props_portfolio_title' => array( 'text', 'Title', 'Discover a World of Possibilities' ),
					'props_portfolio_copy'  => array( 'textarea', 'Intro', 'Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home' ),
				),
				'Form'          => array(
					'props_form_title' => array( 'text', 'Title', "Let's Make it Happen" ),
					'props_form_copy'  => array( 'textarea', 'Intro', "Ready to take the first step toward your dream property? Fill out the form below, and our real estate wizards will work their magic to find your perfect match. Don't wait; let's embark on this exciting journey together." ),
				),
			),
		),

		'services'   => array(
			'title'    => 'Services page content',
			'location' => array( 'page_template', 'page-templates/services.php' ),
			'tabs'     => array(
				'Hero'                  => array(
					'services_hero_title' => array( 'text', 'Title', 'Elevate Your Real Estate Experience' ),
					'services_hero_copy'  => array( 'textarea', 'Intro', 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.' ),
				),
				'Unlock Property Value' => array(
					'services_selling_title' => array( 'text', 'Title', 'Unlock Property Value' ),
					'services_selling_copy'  => array( 'textarea', 'Intro', 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is.<span class="hide-mobile"> Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible. Explore the categories below to see how we can help you at every step of your selling journey</span>' ),
					'services_promo1_title'  => array( 'text', 'Promo title', 'Unlock the Value of Your Property Today' ),
					'services_promo1_copy'   => array( 'textarea', 'Promo text', 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.' ),
					'services_promo1_btn'    => array( 'text', 'Promo button', 'Learn More' ),
					'services_promo1_link'   => array( 'text', 'Promo link', '@contact#contact-form' ),
				),
				'Property Management'   => array(
					'services_management_title' => array( 'text', 'Title', 'Effortless Property Management' ),
					'services_management_copy'  => array( 'textarea', 'Intro', 'Owning a property should be a pleasure, not a hassle. Estatein\'s Property Management Service takes the stress out of property ownership<span class="hide-mobile">, offering comprehensive solutions tailored to your needs. Explore the categories below to see how we can make property management effortless for you</span><span class="only-mobile">.</span>' ),
					'services_promo2_title'     => array( 'text', 'Promo title', 'Experience Effortless Property Management' ),
					'services_promo2_copy'      => array( 'textarea', 'Promo text', 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.' ),
					'services_promo2_btn'       => array( 'text', 'Promo button', 'Learn More' ),
					'services_promo2_link'      => array( 'text', 'Promo link', '@contact#contact-form' ),
				),
				'Smart Investments'     => array(
					'services_investment_title' => array( 'text', 'Title', 'Smart Investments, Informed Decisions' ),
					'services_investment_copy'  => array( 'textarea', 'Intro', 'Building a real estate portfolio requires a strategic approach.<span class="hide-mobile"> Estatein\'s Investment Advisory Service empowers you to make smart investments and informed decisions.</span>' ),
					'services_promo3_title'     => array( 'text', 'Promo title', 'Unlock Your Investment Potential' ),
					'services_promo3_copy'      => array( 'textarea', 'Promo text', 'Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.' ),
					'services_promo3_btn'       => array( 'text', 'Promo button', 'Learn More' ),
					'services_promo3_link'      => array( 'text', 'Promo link', '@contact#contact-form' ),
				),
			),
		),

		'contact'    => array(
			'title'    => 'Contact page content',
			'location' => array( 'page_template', 'page-templates/contact.php' ),
			'tabs'     => array(
				'Hero'    => array(
					'contact_hero_title' => array( 'text', 'Title', 'Get in Touch with Estatein' ),
					'contact_hero_copy'  => array( 'textarea', 'Intro', 'Welcome to Estatein\'s Contact Us page. We\'re here to assist you with any inquiries, requests, or feedback you may have.<span class="hide-mobile"> Whether you\'re looking to buy or sell a property, explore investment opportunities, or simply want to connect, we\'re just a message away. Reach out to us, and let\'s start a conversation.</span>' ),
				),
				'Form'    => array(
					'contact_form_title' => array( 'text', 'Title', "Let's Connect" ),
					'contact_form_copy'  => array( 'textarea', 'Intro', 'We\'re excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein.<span class="hide-mobile"> Whether you\'re a prospective client, partner, or simply curious about our services, we\'re here to answer your questions and provide the assistance you need.</span>' ),
				),
				'Offices' => array(
					'contact_offices_title' => array( 'text', 'Title', 'Discover Our Office Locations' ),
					'contact_offices_copy'  => array( 'textarea', 'Intro', 'Estatein is here to serve you across multiple locations. Whether you\'re looking to meet our team<span class="hide-mobile">, discuss real estate opportunities, or simply drop by for a chat, we have offices conveniently located to serve your needs. Explore the categories below to find the Estatein office nearest to you</span><span class="only-mobile">.</span>' ),
				),
				'Gallery' => array(
					'contact_gallery_title'  => array( 'text', 'Title', "Explore Estatein's World" ),
					'contact_gallery_copy'   => array( 'textarea', 'Intro', 'Step inside the world of Estatein, where professionalism meets warmth, and expertise meets passion. Our gallery offers a glimpse into our team and workspaces, inviting you to get to know us better.' ),
					'contact_gallery_image1' => array( 'image', 'Image 1 (wide)', '' ),
					'contact_gallery_image2' => array( 'image', 'Image 2 (wide)', '' ),
					'contact_gallery_image3' => array( 'image', 'Image 3 (wide)', '' ),
					'contact_gallery_image4' => array( 'image', 'Image 4 (small)', '' ),
					'contact_gallery_image5' => array( 'image', 'Image 5 (small)', '' ),
					'contact_gallery_image6' => array( 'image', 'Image 6 (beside the text)', '' ),
				),
			),
		),
	);
}

/**
 * Site-wide settings (Customizer) and their design defaults.
 *
 * @return array key => array( type, label, default, section )
 */
function estatein_global_fields() {
	return array(
		'banner_enabled'     => array( 'checkbox', 'Show the announcement banner', true, 'header' ),
		'banner_text'        => array( 'text', 'Banner text', '✨Discover Your Dream Property with Estatein', 'header' ),
		'banner_link_label'  => array( 'text', 'Banner link label', 'Learn More', 'header' ),
		'banner_link'        => array( 'text', 'Banner link', '@properties', 'header' ),
		'header_cta_label'   => array( 'text', 'Header button label', 'Contact Us', 'header' ),

		'feature1_title'     => array( 'text', 'Feature tile 1', 'Find Your Dream Home', 'features' ),
		'feature1_link'      => array( 'text', 'Feature tile 1 link', '@properties', 'features' ),
		'feature2_title'     => array( 'text', 'Feature tile 2', 'Unlock Property Value', 'features' ),
		'feature2_link'      => array( 'text', 'Feature tile 2 link', '@services#unlock-property-value', 'features' ),
		'feature3_title'     => array( 'text', 'Feature tile 3', 'Effortless Property Management', 'features' ),
		'feature3_link'      => array( 'text', 'Feature tile 3 link', '@services#property-management', 'features' ),
		'feature4_title'     => array( 'text', 'Feature tile 4', 'Smart Investments, Informed Decisions', 'features' ),
		'feature4_link'      => array( 'text', 'Feature tile 4 link', '@services#smart-investments', 'features' ),

		'faq_title'          => array( 'text', 'FAQ section title', 'Frequently Asked Questions', 'faq' ),
		'faq_copy'           => array( 'textarea', 'FAQ section intro', "Find answers to common questions about Estatein's services, property listings, and the real estate process. We're here to provide clarity and assist you every step of the way.", 'faq' ),
		'faq_btn'            => array( 'text', 'FAQ button label', 'View All FAQ’s', 'faq' ),

		'cta_title'          => array( 'text', 'Title', 'Start Your Real Estate Journey Today', 'cta' ),
		'cta_copy'           => array( 'textarea', 'Text', "Your dream property is just a click away. Whether you're looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.", 'cta' ),
		'cta_btn_label'      => array( 'text', 'Button label', 'Explore Properties', 'cta' ),
		'cta_btn_link'       => array( 'text', 'Button link', '@properties', 'cta' ),

		'details_inquire_title' => array( 'text', 'Enquiry title ({property} = property name)', 'Inquire About {property}', 'details' ),
		'details_inquire_copy'  => array( 'textarea', 'Enquiry intro', 'Interested in this property? Fill out the form below, and our real estate experts will get back to you with more details, including scheduling a viewing and answering any questions you may have.', 'details' ),
		'details_pricing_title' => array( 'text', 'Pricing title', 'Comprehensive Pricing Details', 'details' ),
		'details_pricing_copy'  => array( 'textarea', 'Pricing intro ({property} = property name)', 'At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing for {property} to help you make an informed decision', 'details' ),
		'details_note'          => array( 'textarea', 'Pricing note', 'The figures provided above are estimates and may vary depending on the property, location, and individual circumstances.', 'details' ),

		'contact_email'      => array( 'text', 'Email', 'info@estatein.com', 'contact' ),
		'contact_phone'      => array( 'text', 'Phone', '+1 (123) 456-7890', 'contact' ),
		'contact_hq_label'   => array( 'text', 'Headquarters tile label', 'Main Headquarters', 'contact' ),
		'enquiry_email'      => array( 'text', 'Send form submissions to', '', 'contact' ),

		'social_facebook'    => array( 'url', 'Facebook URL', 'https://www.facebook.com/', 'social' ),
		'social_linkedin'    => array( 'url', 'LinkedIn URL', 'https://www.linkedin.com/', 'social' ),
		'social_twitter'     => array( 'url', 'X (Twitter) URL', 'https://x.com/', 'social' ),
		'social_youtube'     => array( 'url', 'YouTube URL', 'https://www.youtube.com/', 'social' ),
		'social_instagram'   => array( 'url', 'Instagram URL', 'https://www.instagram.com/', 'social' ),

		'footer_copyright'   => array( 'text', 'Copyright', '@2023 Estatein. All Rights Reserved.', 'footer' ),
		'footer_terms_label' => array( 'text', 'Terms link label', 'Terms & Conditions', 'footer' ),
	);
}

/**
 * Design default for a page field or global setting.
 *
 * @param string $key Field / setting name.
 * @return mixed
 */
function estatein_default( $key ) {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( estatein_page_fields() as $group ) {
			foreach ( $group['tabs'] as $fields ) {
				foreach ( $fields as $name => $field ) {
					$defaults[ $name ] = $field[2];
				}
			}
		}
		foreach ( estatein_global_fields() as $name => $field ) {
			$defaults[ $name ] = $field[2];
		}
		$defaults = array_merge( $defaults, estatein_post_field_defaults() );
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Defaults for per-post fields (used when a property has no value yet).
 *
 * @return array
 */
function estatein_post_field_defaults() {
	return array(
		'rating' => 5,
	);
}
