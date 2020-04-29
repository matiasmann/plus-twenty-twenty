<?php
/**
 * Twenty Twenty Plus Options Panel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add new Customizer parameters.
 */
function veintiv_customize_register( $wp_customize ) {

	// Prevent options from displaying when previewing another theme in the Customizer.
	if ( 'twentytwenty' !== get_template() ) {
		return;
	}

	class Veintiv_Customize_Control_Section_Title extends WP_Customize_Control {
		public $type = 'section-title';

		public function content_template() { ?>
<# if ( data.label ) { #>
    <h4 class="viv-customize-section-title">{{{ data.label }}}</h4>
    <# } #>
        <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
                <?php
		}
	}

	class Veintiv_Customize_Control_Checkbox_Multiple extends WP_Customize_Control {
		
		public $type = 'checkbox-multiple';
		
		public function render_content() {
			if ( empty( $this->choices ) )
				return; 
			?>
                <?php if ( ! empty( $this->label ) ) : ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php endif; ?>

                <?php if ( ! empty( $this->description ) ) : ?>
                <span
                    class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php endif; ?>
                <ul>
                    <?php foreach ( $this->choices as $value => $label ) : ?>
                    <li>
                        <label>
                            <input type="checkbox" value="<?php echo esc_attr( $value ); ?>"
                                <?php checked( in_array( $value, $this->value() ) ); ?> />
                            <?php echo esc_html( $label ); ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php
		}	
	}

	class Veintiv_Customize_Section_More extends WP_Customize_Section {

		public $type = 'veintiv-more';
		public $button_text = '';
		public $button_url = '';

		public function json() {
			$json = parent::json();

			$json['button_text'] = $this->button_text;
			$json['button_url'] = esc_url( $this->button_url );

			return $json;
		}

		protected function render_template() { ?>

                <li id="accordion-section-{{ data.id }}"
                    class="accordion-section control-section control-section-{{ data.type }} cannot-expand">

                    <h3 class="accordion-section-title">
                        {{ data.title }}

                        <# if ( data.button_text && data.button_url ) { #>
                            <a href="{{ data.button_url }}" class="button button-secondary alignright" target="_blank"
                                rel="external">{{ data.button_text }}</a>
                            <# } #>
                    </h3>
                </li>
                <?php }
	}

	$wp_customize->register_section_type( 'Veintiv_Customize_Control_Section_Title' );	
	$wp_customize->register_section_type( 'Veintiv_Customize_Section_More' );


	/*
	* Veintiv Layout Panel
	*/

	$wp_customize->add_panel( 'veintiv_panel', array(
		'title' 	=> __( 'Twenty Twenty Plus', 'veintiv' ),
		'priority'	=> 30,
	) );

	/*
	* Footer
	*/

	$wp_customize->add_section( 'veintiv_footer_section', array(
		'title' 	=> __( 'Footer', 'veintiv' ),
		'panel'		=> 'veintiv_panel',
		'priority' 	=> 15
	) );

	$wp_customize->add_setting( 'veintiv_footer_layout', array(
		'default'			=> '',
		'sanitize_callback' => 'veintiv_sanitize_choices',
	) );

	$wp_customize->add_control( new Veintiv_Customize_Control_Section_Title( $wp_customize, 'veintiv_footer_section_title_credit', array(
		'label'		=> __( 'Copyright options', 'veintiv' ),
		'section' 	=> 'veintiv_footer_section',
		'settings'	=> array(),
		'priority' 	=> 4,
	) ) );


	$wp_customize->add_setting( 'veintiv_footer_credit', array(
		'default'			=> '',
		'sanitize_callback'	=> 'veintiv_sanitize_choices',
	) );

	$wp_customize->add_control( 'veintiv_footer_credit', array(
		'label'		=> __( '', 'veintiv' ),
		'section'	=> 'veintiv_footer_section',
		'type'		=> 'select',
		'choices'	=> array(
			''					=> __( 'Default' ),	
			'copyright-only'	=> __( 'Remove Powered by WordPress', 'veintiv' ),
			'custom' 			=> __( 'Custom Text', 'veintiv' ),	
			
		),
		'priority'	=> 5,
	) );

	$wp_customize->add_setting( 'veintiv_footer_credit_text', array(
		'sanitize_callback'		=> 'veintiv_sanitize_credit',
	) );

	$wp_customize->add_control( 'veintiv_footer_credit_text', array(
		'label'			=> __( 'Custom Text to display', 'veintiv' ),
		'section'		=> 'veintiv_footer_section',
		'type'			=> 'textarea',
		'description'	=>__( 'No HTML is allowed. Use <code>[Y]</code> to display the current year', 'veintiv' ),
		'priority'		=> 6,
	) );

	$wp_customize->add_section( new Veintiv_Customize_Section_More( $wp_customize, 'veintiv_more', array(
		'title'			=> esc_html__( 'Premium Available', 'veintiv' ),
		'button_text'	=> esc_html__( 'Learn More', 'veintiv' ),
		'button_class'	=> 'button button-primary alignright',
		'button_url'	=> 'https://developress.org/twentytwentyplus/?premium',
		'priority'		=> 0,
	) ) );
}
add_action( 'customize_register', 'veintiv_customize_register', 11 );

/**
 * Sanitize select.
 */
function veintiv_sanitize_choices( $choice, $setting ) {
	$choice = sanitize_key( $choice );
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return ( array_key_exists( $choice, $choices ) ? $choice : $setting->default );
}

/**
 * Sanitize multiple choices.
 */
function veintiv_sanitize_multi_choices( $value ) {
    $value = ! is_array( $value ) ? explode( ',', $value ) : $value;
    return ( !empty( $value ) ? array_map( 'sanitize_text_field', $value ) : array() );
}

/**
 * Sanitize fonts choices (allow uppercase and space chars).
 */
function veintiv_sanitize_fonts( $choice, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	return ( array_key_exists( $choice, $choices ) ? $choice : $setting->default );
}

/**
 * Sanitizes font-weight value.
 */
function veintiv_sanitize_font_weight( $choice, $setting ) {
	$valid = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
	if ( in_array( $choice, $valid, true ) ) {
		return $choice;
	}
	return $setting->default;
}

/**
 * Sanitizes accessible colors array.
 */
function veintiv_sanitize_accessible_colors( $value ) {

	$value = is_array( $value ) ? $value : array();

	foreach ( $value as $area => $values ) {
		foreach ( $values as $context => $color_val ) {
			$value[ $area ][ $context ] = sanitize_hex_color( $color_val );
		}
	}

	return $value;
}

/**
 * Sanitizes integer.
 */
function veintiv_sanitize_integer( $value ) {
	if ( ! $value || is_null( $value ) ) {
		 return $value;
	}
	return intval( $value );
}

/**
 * Sanitizes credit content.
 */
function veintiv_sanitize_credit( $content ) {
	$kses_defaults = wp_kses_allowed_html( 'post' );
	$svg_args = array(
		'svg'	=> array(
			'class' => true,
			'aria-hidden' => true,
			'aria-labelledby' => true,
			'role' => true,
			'xmlns' => true,
			'width' => true,
			'height' => true,
			'viewbox' => true, 
			'style'	=> true,
		),
		'g'		=> array( 'fill' => true ),
		'title' => array( 'title' => true ),
		'path'	=> array( 'd' => true, 'fill' => true ),
	);

	$allowed_tags = array_merge( $kses_defaults, $svg_args );
	return wp_kses( $content, $allowed_tags );
}

/**
 * Enqueue scripts for customizer preview.
 */
function veintiv_customize_preview_init() {
	wp_enqueue_script( 'veintiv-customize-preview', VEINTIV_ASSETS_URI . '/js/veintivcustomize-preview.js', array( 'customize-preview' ), VEINTIV_VERSION, true );
}
add_action( 'customize_preview_init', 'veintiv_customize_preview_init', 11 );

/**
 * Enqueue scripts for customizer controls.
 */
function veintiv_customize_controls_enqueue_scripts() {
	wp_enqueue_script( 'veintiv-customize-controls', VEINTIV_ASSETS_URI . '/js/veintiv-customize-controls.js', array(), VEINTIV_VERSION, true );
	wp_localize_script( 'veintiv-customize-controls', 'veintivCustomizerSettings',
		array( 
			'colorVars'		=> array(
				'footer'	=> array( 'setting' => 'veintiv_footer_background_color' ),
			),
			'fonts'			=> veintiv_get_fonts(),
			'fontVariants'	=> veintiv_get_font_styles(),			
		)
	);
	wp_enqueue_style( 'veintiv-customize-controls', VEINTIV_ASSETS_URI . '/css/veintiv-customize-controls.css', VEINTIV_VERSION, true );
}
add_action( 'customize_controls_enqueue_scripts', 'veintiv_customize_controls_enqueue_scripts', 11 );


/**
 * Enqueue specific stylesheet for Twenty Twenty.
 */
function veintiv_theme_styles() {
	wp_enqueue_style( 'veintiv-twentytwenty', VEINTIV_ASSETS_URI . '/css/veintiv.css', array(), VEINTIV_VERSION );	
	wp_enqueue_style( 'veintiv-theme-fonts', veintiv_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'veintiv_theme_styles', 12 );	

/**
 * Add preconnect for Google Fonts.
 */
/* Not yet
function veintiv_load_fonts( $urls, $relation_type ) {
	if ( wp_style_is( 'veintiv-theme-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'veintiv_load_fonts', 10, 2 );
*/

/**
 * Add custom classes generated by Customizer settings to the array of body classes.
 */
function veintiv_body_class( $classes ) {

	if ( $text_width = get_theme_mod( 'veintiv_text_width' ) ) {
		$classes[] = 'viv-text-width-' . $text_width;
	}

	if ( get_theme_mod( 'veintiv_page_header_no_background', false ) ) {
 		$classes[] = 'viv-entry-header-no-bg'; 		
 	}

 	if ( get_theme_mod( 'veintiv_body_font' ) || get_theme_mod( 'veintiv_heading_font' ) ) {
 		$classes[] = 'viv-font-active'; 	
 	}

	if ( $h1_font_size = get_theme_mod( 'veintiv_h1_font_size' ) ) {
		$classes[] = 'viv-h1-font-' . $h1_font_size;
	}
	
	if ( $body_font_size = get_theme_mod( 'veintiv_body_font_size', veintiv_get_default_body_font_size() ) ) {
		$classes[] = 'viv-site-font-' . $body_font_size;
	}

	if ( $body_line_height = get_theme_mod( 'veintiv_body_line_height' ) ) {
		$classes[] = 'viv-site-lh-' . $body_line_height;
	}

	if ( 'normal' === get_theme_mod( 'veintiv_heading_letter_spacing' ) ) {
		$classes[] = 'viv-heading-ls-normal';
	}

	if ( is_page_template( 'viv-header-transparent-light.php' ) ) {
		$classes[] = 'overlay-header';
	}

	$header_width = get_theme_mod( 'veintiv_header_width' );
	if ( $header_width && 'wider' !== $header_width ) {
		$classes[] = 'viv-header-' . $header_width;
	} 

	if ( $menu_font_size = get_theme_mod( 'veintiv_menu_font_size' ) ) {
		$classes[] = 'viv-nav-size-' . $menu_font_size;
	}

	if ( $menu_spacing = get_theme_mod( 'veintiv_menu_spacing' ) ) {
		$classes[] = 'viv-nav-spacing-' . $menu_spacing;
	}

	$menu_hover = get_theme_mod( 'veintiv_menu_hover', 'underline' );
	if ( 'underline' !== $menu_hover ) {
		$classes[] = 'viv-nav-hover-' . $menu_hover;
	}

	if ( get_theme_mod( 'veintiv_burger_icon', false ) ) {
		$classes[] = 'viv-menu-burger';
	}

	if ( ! get_theme_mod( 'veintiv_toggle_label', true ) ) {
		$classes[] = 'viv-toggle-label-hidden';
	}

	if ( $footer_width = get_theme_mod( 'veintiv_footer_width' ) ) {
		$classes[] = 'viv-footer-' . $footer_width;
	} 

	if ( 'row' === get_theme_mod( 'veintiv_footer_widget_layout' ) ) {
		$classes[] = 'viv-footer-widgets-row';
	}

	if ( $footer_size = get_theme_mod( 'veintiv_footer_font_size' ) ) {
		$classes[] = 'viv-footer-size-' . $footer_size;
	}

	if ( $socials_style = get_theme_mod( 'veintiv_socials_style' ) ) {
		$classes[] = 'viv-socials-' . $socials_style;
 	}

 	if ( $separator_style = get_theme_mod( 'veintiv_separator_style' ) ) {
		$classes[] = 'viv-hr-' . $separator_style;
 	}

	$button_shape = get_theme_mod( 'veintiv_button_shape', 'square' );
	if ( 'square' !== $button_shape ) {
		$classes[] = 'viv-btn-' . $button_shape;
	}

 	if ( $button_hover = get_theme_mod( 'veintiv_button_hover' ) ) {
		$classes[] = 'viv-button-hover-' . $button_hover;
 	} 	

	if ( is_home() || is_archive() && ! is_post_type_archive() ) {

		$blog_layout = get_theme_mod( 'veintiv_blog_layout' );
		if ( $blog_layout ) {
			$classes[] = 'viv-blog-' . $blog_layout;
		}
	}

	elseif ( is_search() ) {
		if ( 'stack' === get_theme_mod( 'veintiv_page_search_layout' ) ) {
			$classes[] = 'viv-blog-stack';
		}
	}

	elseif ( is_page() ) {
		if ( is_page_template( 'templates/template-cover.php' ) ) {
			if ( $cover_height = get_theme_mod( 'veintiv_cover_page_height' ) ) {
				$classes[] = 'viv-cover-' . $cover_height;
			} elseif ( ! get_theme_mod( 'veintiv_cover_page_scroll_indicator', true ) ) {
				$classes[] = 'viv-cover-hide-arrow';
			}
			if ( 'center' === get_theme_mod( 'veintiv_cover_vertical_align' ) ) {
				$classes[] = 'viv-cover-center';
			}
		}

		if ( is_page_template( 'viv-no-title.php' ) || is_page_template( 'viv-no-header-footer.php' ) ) {
			$classes = array_diff( $classes, array( 'has-post-thumbnail', 'missing-post-thumbnail' ) );
		}
	}

	elseif ( is_singular( 'post' ) ) {

		if ( is_page_template( 'templates/template-cover.php' ) ) {
			if ( $cover_height = get_theme_mod( 'veintiv_cover_post_height' ) ) {
				$classes[] = 'viv-cover-' . $cover_height;
			}	
			if ( 'center' === get_theme_mod( 'veintiv_cover_vertical_align' ) ) {
				$classes[] = 'viv-cover-center';
			}		
		}
	
		if ( has_excerpt() && ! get_theme_mod( 'veintiv_post_excerpt', true ) ) {
			$classes[] = 'viv-no-excerpt';
		}

	}
	
	return $classes;
}
add_filter( 'body_class',  'veintiv_body_class', 11 );

/**
 * Add theme elements to colors array.
 */
function veintiv_get_elements_array_for_colors( $elements ) {
	
	$elements['header-footer']['accent']['background-color'][] = '.footer-widgets .faux-button, .footer-widgets .wp-block-button__link, .footer-widgets input[type="submit"]';
	$elements['header-footer']['borders']['border-color'][] = '.viv-footer-widgets-row .footer-widgets.column-two';

	// Change color if main background and header/footer background are not the same color.
	if ( get_theme_mod( 'veintiv_page_header_no_background', false ) ) {		
		// Get header/footer & content background color.
		$header_footer_background = get_theme_mod( 'header_footer_background_color', '#ffffff' );
		$header_footer_background = strtolower( '#' . ltrim( $header_footer_background, '#' ) );
		$background_color = get_theme_mod( 'background_color', 'f5efe0' );
		$background_color = strtolower( '#' . ltrim( $background_color, '#' ) );

		if ( $background_color !== $header_footer_background ) {
			$elements['content']['accent']['color'][] = '.viv-entry-header-no-bg.singular:not(.overlay-header) .entry-categories a';
			$elements['content']['accent']['color'][] = '.viv-entry-header-no-bg .archive-header .color-accent';
			$elements['content']['accent']['color'][] = '.viv-entry-header-no-bg .archive-header a';
			$elements['content']['text']['color'][] = '.singular.viv-entry-header-no-bg .entry-header';
			$elements['content']['text']['color'][] = '.viv-entry-header-no-bg .archive-header';
			$elements['content']['secondary']['color'][] = '.singular.viv-entry-header-no-bg .entry-header .post-meta';
		}
	}

	return $elements;
}
add_filter( 'twentytwenty_get_elements_array', 'veintiv_get_elements_array_for_colors' );

/**
 * Returns CSS generated for the footer colors.
 */
function veintiv_get_footer_colors_css() {

	$footer_elements = array(
		'accent'	=> array(
			'color'		=> array(),
			'background'=> array( '.footer-nav-widgets-wrapper .button', '.footer-nav-widgets-wrapper .faux-button', '.footer-nav-widgets-wrapper .wp-block-button__link','.footer-nav-widgets-wrapper input[type="submit"]' ),
		),
		'background' => array(
			'color'		=> array( '.footer-top .social-icons a', '#site-footer .social-icons a', '.footer-nav-widgets-wrapper button', '.footer-nav-widgets-wrapper .faux-button', '.footer-nav-widgets-wrapper .wp-block-button .wp-block-button__link', '.footer-nav-widgets-wrapper input[type="submit"]' ),
			'background'=> array( '.footer-nav-widgets-wrapper', '#site-footer' ),
		),
		'text'		=> array(
			'color'		=> array( '#site-footer', '.footer-nav-widgets-wrapper' ),
		),
		'secondary'	=> array(
			'color'		=> array( '.footer-nav-widgets-wrapper .widget .post-date', '.footer-nav-widgets-wrapper .widget .rss-date', '.footer-nav-widgets-wrapper .widget_archive li', '.footer-nav-widgets-wrapper .widget_categories li', '.footer-nav-widgets-wrapper .widget_pages li', '.footer-nav-widgets-wrapper .widget_nav_menu li', '.powered-by-wordpress', '.to-the-top' ),
		),
		'borders'	=> array(
			'border-color' => array( '.footer-nav-widgets-wrapper', '#site-footer', '.footer-widgets-outer-wrapper', '.footer-top', '.viv-footer-widgets-row .footer-widgets.column-two', '.footer-nav-widgets-wrapper input' ),
		),
	);

	$footer_link_color = get_theme_mod( 'veintiv_footer_link_color' );
	$selector_link_footer = '.footer-widgets a, .footer-menu a';
	if ( 'text' === $footer_link_color ) {
		$footer_elements['text']['color'][] = $selector_link_footer;
	} elseif ( 'secondary' === $footer_link_color ) {
		$footer_elements['secondary']['color'][] = $selector_link_footer;
	} else {
		$footer_elements['accent']['color'][] = $selector_link_footer;
	}

	$colors_settings = get_theme_mod( 'veintiv_accessible_colors' );

	ob_start();

	if ( isset( $colors_settings[ 'footer' ] ) ) {
		foreach ( $footer_elements as $key => $definitions ) {
			foreach ( $definitions as $property => $elements ) {				
				if ( isset( $colors_settings[ 'footer'][ $key ] ) ) {
					$val = $colors_settings[ 'footer' ][ $key ];
					twentytwenty_generate_css( implode( ',', $elements ), $property, $val );
				}
			}
		}	
	}
		
	return ob_get_clean();		
		
}
/**
 * Display custom CSS generated by the Customizer settings.
 */
function veintiv_print_customizer_css() {
	$css = '';

	$body_font 				= get_theme_mod( 'veintiv_body_font' );
	$heading_font 			= get_theme_mod( 'veintiv_heading_font' );
	$heading_font_weight 	= get_theme_mod( 'veintiv_heading_font_weight', '700' );
	$secondary_font 		= get_theme_mod( 'veintiv_secondary_font', 'heading' );
	$menu_font 				= get_theme_mod( 'veintiv_menu_font', 'heading' );
	$body_font_stack 		= $body_font ? veintiv_get_font_stack( $body_font ) : "'NonBreakingSpaceOverride', 'Hoefler Text', Garamond, 'Times New Roman', serif";
	$heading_font_stack 	= $heading_font ? veintiv_get_font_stack( $heading_font ) : "'Inter var', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Helvetica, sans-serif";

	if ( $body_font || $heading_font ) {
		if ( $body_font ) {
			$css .= '
			body,
			.entry-content,
			.entry-content p,
			.entry-content ol,
			.entry-content ul,
			.widget_text p,
			.widget_text ol,
			.widget_text ul,
			.widget-content .rssSummary,
			.comment-content p,			
			.entry-content .wp-block-latest-posts__post-excerpt,
			.entry-content .wp-block-latest-posts__post-full-content,
			.has-drop-cap:not(:focus):first-letter { font-family: ' . $body_font_stack . '; }';
		}

		$css .= 'h1, h2, h3, h4, h5, h6, .entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6, .faux-heading, .entry-content .faux-heading, .site-title, .pagination-single a { font-family: ' . $heading_font_stack . '; }';

		if ( 'heading' === $menu_font ) {
			$css .= 'ul.primary-menu, ul.modal-menu { font-family: ' . $heading_font_stack . '; }';
		}

		if ( 'heading' === $secondary_font ) {
			$css .= '
				.intro-text,
				input,
				textarea,
				select,
				button, 
				.button, 
				.faux-button, 
				.wp-block-button__link,
				.wp-block-file__button,
				.entry-content .wp-block-file,	
				.primary-menu li.menu-button > a,
				.entry-content .wp-block-pullquote,
				.entry-content .wp-block-quote.is-style-large,
				.entry-content .wp-block-quote.is-style-viv-large-icon,
				.entry-content cite,
				.entry-content figcaption,
				.wp-caption-text,
				.entry-content .wp-caption-text,
				.widget-content cite,
				.widget-content figcaption,
				.widget-content .wp-caption-text,
				.entry-categories,
				.post-meta,
				.comment-meta, 
				.comment-footer-meta,
				.author-bio,
				.comment-respond p.comment-notes, 
				.comment-respond p.logged-in-as,
				.entry-content .wp-block-archives,
				.entry-content .wp-block-categories,
				.entry-content .wp-block-latest-posts,
				.entry-content .wp-block-latest-comments,
				p.comment-awaiting-moderation,
				.pagination,
				#site-footer,							
				.widget:not(.widget-text),
				.footer-menu,
				label,
				.toggle .toggle-text,
				.entry-content ul.portfolio-filter,
				.portfolio-content .entry-categories,
				.pswp {
					font-family: ' . $heading_font_stack . ';
				}';
		} else {
			$css .= '
			input,
			textarea,			
			select,
			button, 
			.button, 
			.faux-button, 
			.wp-block-button__link,
			.wp-block-file__button,	
			.primary-menu li.menu-button > a,	
			.entry-content .wp-block-pullquote,
			.entry-content .wp-block-quote.is-style-large,
			.entry-content cite,
			.entry-content figcaption,
			.wp-caption-text,
			.entry-content .wp-caption-text,
			.widget-content cite,
			.widget-content figcaption,
			.widget-content .wp-caption-text,
			.entry-content .heading-eyebrow,
			.entry-content .wp-block-archives,
			.entry-content .wp-block-categories,
			.entry-content .wp-block-latest-posts,
			.entry-content .wp-block-latest-comments,
			p.comment-awaiting-moderation {
				font-family: ' . $body_font_stack . ';
			}';
		} 

		$css .= 'table {font-size: inherit;} ';
	}

	if ( 'body' === $menu_font ) {
		$css .= 'ul.primary-menu, ul.modal-menu { font-family: ' . $body_font_stack . '; }';
	}

	if ( $heading_font_weight && '700' !== $heading_font_weight ) {
		$css .= 'h1, .heading-size-1, h2, h3, h4, h5, h6, .faux-heading, .archive-title, .site-title, .pagination-single a { font-weight: ' . $heading_font_weight . ';}';
	} elseif ( $heading_font ) {
		$css .= 'h1, .heading-size-1 { font-weight: ' . $heading_font_weight . ';}';
	}

	/* Site title */
	if ( ! has_custom_logo() ) {
		$css_logo = '';
	
		if ( $heading_font ) {
			$css .= '.header-titles .site-title a { text-decoration: none; }';
		}	

		if ( $logo_font_size = get_theme_mod( 'veintiv_logo_font_size', false ) ) {
			$css_logo .= 'font-size:' . $logo_font_size . 'em;';
		}

		if ( $logo_letter_spacing = get_theme_mod( 'veintiv_logo_letter_spacing', false ) ) {
			$css_logo .= 'letter-spacing:' . $logo_letter_spacing . 'em;';
		}
		
		if ( $logo_transform = get_theme_mod( 'veintiv_logo_text_transform' ) ) {
			$css_logo .= 'text-transform: ' . esc_attr( $logo_transform ) . ';';
		}

		if ( $css_logo ) {
			$css .= '.header-titles .site-title { ' . $css_logo . '}';
		}

		if ( $logo_mobile_font_size = get_theme_mod( 'veintiv_logo_mobile_font_size' ) ) {
			$css .= '@media(max-width:699px) { .header-titles .site-title { font-size:' . intval( $logo_mobile_font_size ) . 'em; } }';
		}

	} else if ( $logo_responsive_width = get_theme_mod( 'veintiv_logo_mobile_width' ) ) {
		$css .= '@media(max-width:699px) { .site-logo .custom-logo-link img { width:' . intval( $logo_responsive_width ) . 'em; height:auto !important; max-height: none; } }';
	}

	/* Menu */

	if ( $menu_font_weight = get_theme_mod( 'veintiv_menu_font_weight', 500 ) ) {
		$css .= 'ul.primary-menu, ul.modal-menu > li .ancestor-wrapper a { font-weight:' . esc_attr( $menu_font_weight ) . ';}';
	}

	if ( $menu_transform = get_theme_mod( 'veintiv_menu_text_transform' ) ) {
		$css .= 'ul.primary-menu li a, ul.modal-menu li .ancestor-wrapper a { text-transform:' . esc_attr( $menu_transform ) . ';';
			if ( 'uppercase' === $menu_transform ) {
				$css .= 'letter-spacing: 0.0333em;';
			}			
		$css .= '}';
	}

	if ( ! get_theme_mod( 'veintiv_button_uppercase', true ) ) {
		$css .= 'button, .button, .faux-button, .wp-block-button__link, .wp-block-file__button, input[type="button"], input[type="submit"] { text-transform: none; letter-spacing: normal; }';
	}

	$menu_color = get_theme_mod( 'veintiv_menu_color', 'accent' );
	$menu_hover = get_theme_mod( 'veintiv_menu_hover', 'underline' );

	if ( 'text' === $menu_color ) {
		$css .= 'body:not(.overlay-header) .primary-menu > li > a, body:not(.overlay-header) .primary-menu > li > .icon, .modal-menu > li > .ancestor-wrapper > a { color: inherit; }';
	} elseif ( 'secondary' === $menu_color ) {
		$menu_secondary = sanitize_hex_color( twentytwenty_get_color_for_area( 'header-footer', 'secondary' ) );
		$css .= 'body:not(.overlay-header) .primary-menu > li > a, body:not(.overlay-header) .primary-menu > li > .icon, .modal-menu > li > .ancestor-wrapper > a { color: '. $menu_secondary . '; }';
	}

	if ( 'color' === $menu_hover ) {
		$menu_hover_color = 'inherit';
		if ( 'text' === $menu_color ) {
			$menu_hover_color = sanitize_hex_color( twentytwenty_get_color_for_area( 'header-footer', 'accent' ) );
		}
		$css .= 'body:not(.overlay-header) .primary-menu > li > a:hover, body:not(.overlay-header) .primary-menu > li > a:hover + .icon, 
		body:not(.overlay-header) .primary-menu > li.current-menu-item > a, body:not(.overlay-header) .primary-menu > li.current-menu-item > .icon, 
		body:not(.overlay-header) .primary-menu > li.current_page_parent > a, body:not(.overlay-header) .primary-menu > li.current_page_parent > .icon, 
		.modal-menu > li > .ancestor-wrapper > a:hover, .modal-menu > li > .ancestor-wrapper > a:hover + .toggle,
		.modal-menu > li.current-menu-item > .ancestor-wrapper > a, .modal-menu > li.current-menu-item > .ancestor-wrapper > .toggle, 
		.modal-menu > li.current_page_parent > .ancestor-wrapper > a, .modal-menu > li.current_page_parent > .ancestor-wrapper > .toggle { 
			color: ' . $menu_hover_color . ';}';
	}

	if ( $footer_bgcolor = get_theme_mod( 'veintiv_footer_background_color' ) ) {

		$css .= veintiv_get_footer_colors_css();

		$background_color = get_theme_mod( 'background_color', 'f5efe0' );
		$background_color = strtolower( '#' . ltrim( $background_color, '#' ) );

		if ( $background_color !== $footer_bgcolor ) {
			$css .= '.reduced-spacing.footer-top-visible .footer-nav-widgets-wrapper, .reduced-spacing.footer-top-hidden #site-footer{ border: 0; }';
		} else {
			$css .= '.footer-top-visible .footer-nav-widgets-wrapper, .footer-top-hidden #site-footer { border-top-width: 0.1rem; }';
		}

	} else {
		$footer_link_color = get_theme_mod( 'veintiv_footer_link_color' );
		if ( 'text' === $footer_link_color || 'secondary' === $footer_link_color ) {
			$footer_link_color_value = sanitize_hex_color( twentytwenty_get_color_for_area( 'header-footer', $footer_link_color ) );
			$css .= '.footer-widgets a, .footer-menu a{ color:' . $footer_link_color_value . ';}';
		} 
	}

	if ( 'hidden' === get_theme_mod( 'veintiv_footer_layout' ) ) {
		$css .= '.footer-widgets-outer-wrapper { border-bottom: 0; }';
	}

	$css = apply_filters( 'veintiv-customizer-css', $css );

	if ( $css ) : ?>
                <style type="text/css" id="veintiv-theme-custom-css">
                <?php echo veintiv_minify_css($css);
                ?>
                </style>
                <?php endif; ?>
                <?php 
}
add_action( 'wp_head', 'veintiv_print_customizer_css' );

/** 
 * Remove line breaks and white space chars. 
 * @see wp_strip_all_tags
 */
function veintiv_minify_css( $css ) {
	$css = preg_replace( '/[\r\n\t ]+/', ' ', $css );
	return trim( $css );
}

/**
 * Outputs an Underscore template that generates dynamically the CSS for instant display in the Customizer preview.
 */
function veintiv_customizer_font_css_template() {
	?>

                <script type="text/html" id="tmpl-veintiv-customizer-live-style">
                <# var body_font=data.veintiv_body_font; var
                    body_font_stack="'NonBreakingSpaceOverride', 'Hoefler Text', Garamond, 'Times New Roman', serif" ;
                    if ( body_font ) { if ( 'sans-serif'===body_font ) {
                    body_font_stack="-apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica Neue, Helvetica, sans-serif"
                    ; } else { body_font_stack="'" + body_font + "', sans-serif" ; } } var
                    heading_font=data.veintiv_heading_font; var
                    heading_font_stack="'Inter var', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Helvetica, sans-serif"
                    ; if ( heading_font ) { if ( 'sans-serif'===heading_font ) {
                    heading_font_stack="-apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica Neue, Helvetica, sans-serif"
                    ; } else { heading_font_stack="'" + heading_font + "', sans-serif" ; } } var
                    heading_font_weight=data.veintiv_heading_font_weight; var
                    secondary_font=data.veintiv_secondary_font; var secondary_font_stack='body'===secondary_font ?
                    body_font : heading_font; var menu_font=data.veintiv_menu_font; var
                    menu_font_weight=data.veintiv_menu_font_weight; var logo_font=data.veintiv_logo_font; var
                    logo_font_weight=data.veintiv_logo_font_weight; var logo_font_size=data.veintiv_logo_font_size; var
                    logo_letter_spacing=data.veintiv_logo_letter_spacing; #>

                    <# if ( body_font || heading_font ) { #>
                        body,
                        .entry-content,
                        .entry-content p,
                        .entry-content ol,
                        .entry-content ul,
                        .widget_text p,
                        .widget_text ol,
                        .widget_text ul,
                        .widget-content .rssSummary,
                        .comment-content p,
                        .has-drop-cap:not(:focus):first-letter { font-family: {{ body_font_stack }}; }

                        h1, h2, h3, h4, h5, h6, .entry-content h1, .entry-content h2, .entry-content h3, .entry-content
                        h4, .entry-content h5, .entry-content h6, .faux-heading, .entry-content .faux-heading,
                        .site-title, .pagination-single a { font-family: {{ heading_font_stack }}; }

                        table {font-size: inherit;}
                        <# } #>

                            .intro-text,
                            input,
                            textarea,
                            select,
                            button,
                            .button,
                            .faux-button,
                            .wp-block-button__link,
                            .wp-block-file__button,
                            .primary-menu li.menu-button > a,
                            .entry-content .wp-block-pullquote,
                            .entry-content .wp-block-quote.is-style-large,
                            .entry-content cite,
                            .entry-content figcaption,
                            .wp-caption-text,
                            .entry-content .wp-caption-text,
                            .widget-content cite,
                            .widget-content figcaption,
                            .widget-content .wp-caption-text,
                            .entry-categories,
                            .post-meta,
                            .comment-meta,
                            .comment-footer-meta,
                            .author-bio,
                            .comment-respond p.comment-notes,
                            .comment-respond p.logged-in-as,
                            .entry-content .wp-block-archives,
                            .entry-content .wp-block-categories,
                            .entry-content .wp-block-latest-posts,
                            .entry-content .wp-block-latest-comments,
                            .pagination,
                            #site-footer,
                            .widget:not(.widget-text),
                            .footer-menu,
                            label,
                            .toggle .toggle-text,
                            .entry-content ul.portfolio-filter,
                            .portfolio-content .entry-categories,
                            .pswp { font-family: {{ secondary_font_stack }}; }

                            <# if ( 'body'===menu_font ) { #>
                                ul.primary-menu, ul.modal-menu { font-family: {{ body_font_stack }}; }
                                <# } else { #>
                                    ul.primary-menu, ul.modal-menu { font-family: {{ heading_font_stack }}; }
                                    <# } #>

                                        <# if ( heading_font_weight ) { #>
                                            h1, .heading-size-1, h2, h3, h4, h5, h6, .faux-heading, .archive-title,
                                            .site-title, .pagination-single a { font-weight: {{ heading_font_weight }} ;
                                            }
                                            <# } #>

                                                <# if ( menu_font_weight ) { #>
                                                    ul.primary-menu, ul.modal-menu ul li a, ul.modal-menu > li
                                                    .ancestor-wrapper a { font-weight: {{ menu_font_weight }}; }
                                                    <# } #>

                                                        .header-titles .site-title {
                                                        <# if ( logo_font ) { #>
                                                            font-family: '{{ logo_font }}', sans-serif;
                                                            <# } #>
                                                                <# if ( logo_font_weight ) { #>
                                                                    font-weight: {{ logo_font_weight }};
                                                                    <# } #>
                                                                        <# if ( logo_font_size ) { #>
                                                                            font-size: {{ logo_font_size }}em;
                                                                            <# } #>
                                                                                <# if ( logo_letter_spacing ) { #>
                                                                                    letter-spacing:
                                                                                    {{ logo_letter_spacing }}em;
                                                                                    <# } #>
                                                                                        }
                </script>
                <?php
}
add_action( 'customize_controls_print_footer_scripts', 'veintiv_customizer_font_css_template' );

/**
 * Display custom CSS generated by the Customizer settings inside the block editor.
 */
function veintiv_print_editor_customizer_css() {

	wp_enqueue_style( 'veintiv-fonts', veintiv_fonts_url(), array(), null );
	
	$css = '';

	$body_font 				= get_theme_mod( 'veintiv_body_font' );
	$body_font_size 		= get_theme_mod( 'veintiv_body_font_size', veintiv_get_default_body_font_size() );
	$heading_font 			= get_theme_mod( 'veintiv_heading_font' );
	$heading_font_weight 	= get_theme_mod( 'veintiv_heading_font_weight', '700' );
	$secondary_font 		= get_theme_mod( 'veintiv_secondary_font' );
	$body_line_height 		= get_theme_mod( 'veintiv_body_line_height' );
	$heading_letter_spacing = get_theme_mod( 'veintiv_heading_letter_spacing' );
	$body_font_stack 		= veintiv_get_font_stack( $body_font );
	$heading_font_stack 	= veintiv_get_font_stack( $heading_font );
	$secondary_font_stack 	= 'body' === $secondary_font ? $body_font_stack : $heading_font_stack;
	$content_width 			= get_theme_mod( 'veintiv_text_width' );
	
	if ( 'medium' === $content_width ) {
		$css .= '.wp-block, 
		.wp-block .wp-block[data-type="core/group"]:not([data-align="full"]):not([data-align="wide"]):not([data-align="left"]):not([data-align="right"]),
		.wp-block .wp-block[data-type="core/cover"]:not([data-align="full"]):not([data-align="wide"]):not([data-align="left"]):not([data-align="right"]) {
			max-width: 700px; }';
	} elseif ( 'wide' === $content_width ) {
		$css .= '.wp-block, 
		.wp-block .wp-block[data-type="core/group"]:not([data-align="full"]):not([data-align="wide"]):not([data-align="left"]):not([data-align="right"]),
		.wp-block .wp-block[data-type="core/cover"]:not([data-align="full"]):not([data-align="wide"]):not([data-align="left"]):not([data-align="right"]) { max-width: 800px; }';
	}	

	if ( $body_font ) {
		$css .= '.editor-styles-wrapper > *,
			.editor-styles-wrapper p,
			.editor-styles-wrapper ol,
			.editor-styles-wrapper ul {
				font-family:' . $body_font_stack . ';
		}';
	}

	if ( 'small' === $body_font_size ) {
		$css .= '.editor-styles-wrapper.edit-post-visual-editor > * { font-size: 17px;}';
	} elseif ( 'medium' === $body_font_size ) {
		$css .= '.editor-styles-wrapper.edit-post-visual-editor > * { font-size: 19px;}';
	}

	if ( 'medium' == $body_line_height ) {
		$css .= '.editor-styles-wrapper p,.editor-styles-wrapper p.wp-block-paragraph { line-height: 1.6;}';
	} elseif ( 'loose' == $body_line_height ) {
		$css .= '.editor-styles-wrapper p,.editor-styles-wrapper p.wp-block-paragraph { line-height: 1.8;}';
	}

	$css .= '.editor-post-title__block .editor-post-title__input,
		.editor-styles-wrapper.edit-post-visual-editor h1,
		.editor-styles-wrapper.edit-post-visual-editor h2,
		.editor-styles-wrapper.edit-post-visual-editor h3,
		.editor-styles-wrapper.edit-post-visual-editor h4,
		.editor-styles-wrapper.edit-post-visual-editor h5,
		.editor-styles-wrapper.edit-post-visual-editor h6,
		.editor-styles-wrapper.edit-post-visual-editor .faux-heading {';

		if ( $heading_font ) {
			$heading_font_stack = veintiv_get_font_stack( $heading_font );
			$css .= 'font-family:' . $heading_font_stack . ';';
		}

		if ( $heading_font_weight ) {
			$css .= 'font-weight:' . $heading_font_weight . ';';
		}

		if ( 'normal' === $heading_letter_spacing ) {
			$css .= 'letter-spacing: normal;';
		} else {
			$css .= 'letter-spacing: -0.015em;';
		}

	$css .= ';} ';

	$css .= '.editor-styles-wrapper.edit-post-visual-editor h6 { letter-spacing: 0.03125em; }';
	
	$accent = sanitize_hex_color( twentytwenty_get_color_for_area( 'content', 'accent' ) );
	$css .= '.editor-styles-wrapper a { color: '. $accent . '}';

	$css .= '.editor-styles-wrapper .wp-block-button .wp-block-button__link,
		.editor-styles-wrapper .wp-block-file .wp-block-file__button,
		.editor-styles-wrapper .button,
		.editor-styles-wrapper .faux-button,
		.editor-styles-wrapper .wp-block-paragraph.has-drop-cap:not(:focus):first-letter,
		.editor-styles-wrapper .wp-block-pullquote, 
		.editor-styles-wrapper .wp-block-quote.is-style-large,
		.editor-styles-wrapper .wp-block-quote.is-style-viv-large-icon,
		.editor-styles-wrapper .wp-block-quote .wp-block-quote__citation,
		.editor-styles-wrapper .wp-block-pullquote .wp-block-pullquote__citation,				
		.editor-styles-wrapper figcaption,
		.editor-styles-wrapper .heading-eyebrow { font-family: ' . $secondary_font_stack . '; }';

	if ( $h1_font_size = get_theme_mod( 'veintiv_h1_font_size' ) ) {
		$css .= '@media (min-width: 1220px) {
			.editor-styles-wrapper .wp-block[data-type="core/pullquote"][data-align="wide"] blockquote p, 
			.editor-styles-wrapper .wp-block[data-type="core/pullquote"][data-align="full"] blockquote p {
				font-size: 48px;
			}
		}';

		if ( 'small' === $h1_font_size ) {
			$css .= '@media (min-width: 700px) {
				.editor-post-title__block .editor-post-title__input, .editor-styles-wrapper .wp-block h1, .editor-styles-wrapper .wp-block .heading-size-1 {
					font-size: 56px;
				}				
			}';
		} elseif ( 'medium' === $h1_font_size ) {
			$css .= '@media (min-width: 1220px) {
				.editor-post-title__block .editor-post-title__input, .editor-styles-wrapper .wp-block h1, .editor-styles-wrapper .wp-block .heading-size-1 {
					font-size: 64px;
				}
			}';
		} elseif ( 'large' === $h1_font_size ) {
			$css .= '@media (min-width: 1220px) {
				.editor-post-title__block .editor-post-title__input, .editor-styles-wrapper .wp-block h1, .editor-styles-wrapper .wp-block .heading-size-1 {
					font-size: 72px;
				}
			}';
		}		
	}
	
	// Button styling
	if ( ! get_theme_mod( 'veintiv_button_uppercase', true ) ) {
		$css .= '.editor-styles-wrapper.edit-post-visual-editor .wp-block-button .wp-block-button__link,
		.editor-styles-wrapper.edit-post-visual-editor .wp-block-file .wp-block-file__button,
		.editor-styles-wrapper.edit-post-visual-editor .button { text-transform: none; }';
	}

	$button_shape = get_theme_mod( 'veintiv_button_shape', 'square' );
	if ( 'rounded' === $button_shape ) {
		$css .= '.editor-styles-wrapper .wp-block-button__link { border-radius: 6px;}';
	} elseif ( 'pill' === $button_shape ) {
		$css .= '.editor-styles-wrapper .wp-block-button__link { border-radius: 50px; padding: 1.1em 1.8em;}';
	}

	// Separator styling
	if ( 'minimal' === get_theme_mod( 'veintiv_separator_style' ) ) {
		$css .= '.editor-styles-wrapper hr:not(.is-style-dots ){ 
			background: currentColor !important;
		}

		.editor-styles-wrapper hr:not(.has-background):not(.is-style-dots) {
			color: currentColor;
			opacity: 0.15;
		}	

		.editor-styles-wrapper hr:not(.is-style-dots)::before,
		.editor-styles-wrapper hr:not(.is-style-dots)::after {
			display: none;
		}';
 	}

	wp_add_inline_style( 'twentytwenty-block-editor-styles', $css );
}
add_action( 'enqueue_block_editor_assets', 'veintiv_print_editor_customizer_css', 20 );

/**
 * Set up theme defaults and register support for various features.
 */
function veintiv_theme_support() {

	// Set editor font sizes based on body font-size
	$body_font_size = get_theme_mod( 'veintiv_body_font_size', veintiv_get_default_body_font_size() );

	$font_sizes = current( (array) get_theme_support( 'editor-font-sizes' ) );

	// Add medium font size option in the editor dropdown
	$medium = array(
		'name'	=> _x( 'Medium', 'Name of the medium font size in the block editor', 'veintiv' ),
		'size'	=> 23,
		'slug'	=> 'medium',
	);
	array_splice( $font_sizes, 2, 0, array( $medium ) );

	if ( 'small' == $body_font_size || 'medium' == $body_font_size ) {
		$sizeS 		= 14;
		$sizeNormal = 17;
		$sizeM 		= 19;
		$sizeL 		= 21;
		$sizeXL 	= 25;

		if ( 'medium' == $body_font_size ) {
			$sizeS 		= 16;
			$sizeNormal = 19;
			$sizeM 		= 21;
			$sizeL 		= 24;
			$sizeXL 	= 28;
		}

		foreach ( $font_sizes as $index => $settings ) {
			if ( $settings['slug'] === 'small' ) {
				$font_sizes[ $index ]['size'] = $sizeS;
			} elseif ( $settings['slug'] === 'normal' ) {
				$font_sizes[ $index ]['size'] = $sizeNormal;
			} elseif ( $settings['slug'] === 'medium' ) {
				$font_sizes[ $index ]['size'] = $sizeM;
			} elseif ( $settings['slug'] === 'large' ) {
				$font_sizes[ $index ]['size'] = $sizeL;
			} elseif ( $settings['slug'] === 'larger' ) {
				$font_sizes[ $index ]['size'] = $sizeXL;
			}
		}

	}
	add_theme_support( 'editor-font-sizes', $font_sizes );

}
add_action( 'after_setup_theme', 'veintiv_theme_support', 12 );

/**
 * Display a different logo on Cover Template.
 */
function veintiv_logo_transparent( $html ) {

	$logo_id = get_theme_mod( 'custom_logo' );
	$custom_logo_id = get_theme_mod( 'veintiv_custom_logo_transparent' );

	if ( ! $logo_id || ! $custom_logo_id ) {
		return $html;
	}

	if ( is_page_template( array( 'templates/template-cover.php', 'viv-header-transparent-light.php', 'template-cover.php' ) ) ) {				
		$custom_logo_attr = array(
			'class'	=> 'custom-logo',
		);

		$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
		if ( empty( $image_alt ) ) {
			$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
		}

		$html = sprintf(
			'<a href="%1$s" class="custom-logo-link" rel="home">%2$s</a>',
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr )
		);		

	}

	return $html;
}
add_filter( 'get_custom_logo', 'veintiv_logo_transparent' );

/**
 * Hide the tagline by returning an empty string.
 */
function veintiv_hide_tagline( $html ) {
	if ( ! get_theme_mod( 'veintiv_header_tagline', true ) ) {
		return '';
	}
	return $html;
}
add_filter( 'twentytwenty_site_description', 'veintiv_hide_tagline' );

/**
 * Add support for blocks inside widgets.
 */
function veintiv_support_widget_block() {
	add_filter( 'widget_text', 'do_blocks', 9 );
}
add_action( 'init', 'veintiv_support_widget_block' );

/**
 * Add support for excerpt to page.
 */
function veintiv_support_page_excerpt() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'veintiv_support_page_excerpt' );

/**
 * Set template for page cover with excerpt.
 */
function veintiv_page_cover_excerpt( $template ) {
	if ( is_page_template( 'templates/template-cover.php' ) && is_page() && has_excerpt() ) {
		return VEINTIV_PATH . 'inc/templates/template-cover.php';
	}
	return $template;
}
add_filter( 'template_include', 'veintiv_page_cover_excerpt' );

/**
 * Set the excerpt more link.
 */
function veintiv_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'veintiv_excerpt_more' );

/**
 * Display Continue Reading link after the excerpt.
 */
function veintiv_add_more_to_excerpt( $excerpt, $post ) {
	if ( 'summary' === get_theme_mod( 'blog_content', 'full' ) && get_theme_mod( 'veintiv_blog_excerpt_more', false ) && 'post' === $post->post_type && ! is_singular() && ! is_search() ) {
		return $excerpt . '<a href="'. get_permalink( $post->ID ) . '" class="more-link"><span>' . __( 'Continue reading', 'twentytwenty' ) . '</span><span class="screen-reader-text">'. $post->post_title . '</span></a>';
	}
	return $excerpt;	
}
add_filter( 'get_the_excerpt', 'veintiv_add_more_to_excerpt', 10, 2 );

/**
 * Sets excerpt length.
 */
function veintiv_custom_excerpt_length( $length ) {
	if ( is_home() || is_archive() ) {
		if ( $newlength = get_theme_mod( 'veintiv_blog_excerpt_length' ) ) {
			return $newlength;
		}
	}
	return $length;
}
add_filter( 'excerpt_length', 'veintiv_custom_excerpt_length' );

/**
 * Determines if post thumbnail should be displayed.
 */
function veintiv_display_featured_image( $has_thumbnail ) {

	if ( ! get_theme_mod( 'veintiv_blog_image', true ) && ( is_home() || is_archive() || is_post_type_archive( 'post' ) ) ) {
		return false;
	}

	return $has_thumbnail;
}
add_filter( 'has_post_thumbnail', 'veintiv_display_featured_image', 12 );

/**
 * Change the read more button style to a normal link when changing blog layout.
 */
function veintiv_read_more_tag( $more_link_element ) {
	if ( '' === get_theme_mod( 'veintiv_blog_layout' ) ) {
		return $more_link_element;
	}
	return str_replace( 'faux-button', 'link-button', $more_link_element );
}
add_filter( 'the_content_more_link', 'veintiv_read_more_tag', 20 );

/**
 * Add link to featured image on archives page.
 */
function veintiv_add_link_to_featured_image( $html, $postID ) {
	if ( ( is_home() || is_archive() || is_post_type_archive( 'post' ) ) ) {
		return '<a href="' . esc_url( get_permalink( $postID ) ) . '" tabindex="-1" aria-hidden="true">' . $html . '</a>';
	}
	return $html;
}
add_filter( 'post_thumbnail_html', 'veintiv_add_link_to_featured_image', 10, 2 );

/**
 * Hide the top categories.
 */
function veintiv_hide_categories_in_entry_header() {
	if ( is_singular() ) {
		$post_metas = get_theme_mod( 'veintiv_post_meta', array( 'top-categories', 'author', 'post-date', 'comments', 'tags' ) );
		if ( ! in_array( 'top-categories', $post_metas ) ) {
			return false;
		}
	} else {
		$post_metas = get_theme_mod( 'veintiv_blog_meta', array( 'top-categories', 'author', 'post-date', 'comments', 'tags' ) );
		if ( ! in_array( 'top-categories', $post_metas ) ) {
			return false;
		}
	}
	return true;
}
add_filter( 'twentytwenty_show_categories_in_entry_header', 'veintiv_hide_categories_in_entry_header' );

/**
 * Display the post top meta.
 */		
function veintiv_post_meta_top( $metas ) {
	$post_metas = is_singular() ? get_theme_mod( 'veintiv_post_meta', $metas ) : get_theme_mod( 'veintiv_blog_meta', $metas );

	$tags_key = array_search( 'tags', $post_metas );
	if ( false !== $tags_key ) {
		unset( $post_metas[ $tags_key ] );
	}

	if ( ! is_singular() ) {
		$post_metas[] = 'sticky';
	}

	$metas = $post_metas;
	return $metas;
}
add_filter( 'twentytwenty_post_meta_location_single_top', 'veintiv_post_meta_top' );

/**
 * Display the post bottom meta.
 */		
function veintiv_post_meta_bottom( $metas ) {
	$post_metas = is_singular() ? get_theme_mod( 'veintiv_post_meta', $metas ) : get_theme_mod( 'veintiv_blog_meta', $metas );
	if ( ! in_array( 'tags', $post_metas ) ) {
		$metas = array();
	}	

	return $metas;
}
add_filter( 'twentytwenty_post_meta_location_single_bottom', 'veintiv_post_meta_bottom' );

/**
 * Adds custom classes to the array of post classes.
 */
function veintiv_post_class( $classes, $class, $post_id ) {
	$post = get_post( $post_id );

	if ( 'post' === $post->post_type && ! get_theme_mod( 'veintiv_blog_meta_icon', true ) ) {
		$classes[] = 'viv-meta-no-icon';
	}

	return $classes;
}
add_filter( 'post_class', 'veintiv_post_class', 10, 3 );

/**
 * Removes the single navigation by excluding all the terms.
 */
function veintiv_filter_navigation( $content ) {	
	if ( 'none' === get_theme_mod( 'veintiv_post_navigation' ) ) {		
		add_filter( 'get_next_post_excluded_terms', 'veintiv_exclude_terms' );
		add_filter( 'get_previous_post_excluded_terms', 'veintiv_exclude_terms' );

		if ( ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
			echo '<hr class="styled-separator is-style-wide section-inner" aria-hidden="true">';
		}
	}
}
add_action( 'get_template_part_template-parts/navigation', 'veintiv_filter_navigation' );

/**
 * Returns all the post categories.
 */
function veintiv_exclude_terms() {
	$cat_ids = get_terms( 'category', array( 'fields' => 'ids', 'get' => 'all' ) );
	return $cat_ids;
}

/**
 * Filters whether all posts are open for comments.
 */
function veintiv_comments_open( $open ) {
	if ( ! get_theme_mod( 'veintiv_blog_comments', true ) ) {
		return false;
	}
	return $open;
}
add_filter( 'comments_open', 'veintiv_comments_open' );

/**
 * Filters the comment count for all posts.
 */
function veintiv_comments_number( $count ) {
	if ( ! get_theme_mod( 'veintiv_blog_comments', true ) ) {
		return 0;
	}
	return $count;
}
add_filter( 'get_comments_number', 'veintiv_comments_number' );

/**
 * Hide excerpt on single post.
 */
function veintiv_remove_excerpt_single_post( $slug, $name = null ) {	
	if ( is_single() && ! get_theme_mod( 'veintiv_post_excerpt', true ) ) {
		add_filter( 'the_excerpt', '__return_empty_string' );
	}
}
add_action( 'get_template_part_template-parts/entry-header', 'veintiv_remove_excerpt_single_post', 10, 2 );
add_action( 'get_template_part_template-parts/content-cover', 'veintiv_remove_excerpt_single_post', 10, 2 );

/**
 * Displays custom footer based on Customizer settings.
 */
function veintiv_get_footer( $name = null ) { 

	$footer_layout 	= get_theme_mod( 'veintiv_footer_layout' );
	$footer_credit = get_theme_mod( 'veintiv_footer_credit' );
	$credit_text = get_theme_mod( 'veintiv_footer_credit_text' );

	if ( '' == $footer_credit && '' == $footer_layout ) {
		return;
	}
	
	if ( 'hidden' !== $footer_layout ) : ?>

                <footer id="site-footer" role="contentinfo" class="header-footer-group">

                    <div class="section-inner">

                        <div class="footer-credits">

                            <p class="footer-copyright">

                                <?php if ( 'custom' === $footer_credit && $credit_text ) : ?>
                                <?php echo veintiv_sanitize_credit( str_replace( '[Y]', date_i18n( 'Y' ), $credit_text ) ); ?>
                                <?php else : ?>&copy;
                                <?php echo date_i18n(
							/* translators: Copyright date format, see https://secure.php.net/date */
							_x( 'Y', 'copyright date format', 'twentytwenty' )
							); ?>
                                <a
                                    href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo bloginfo( 'name' ); ?></a>
                                <?php endif; ?>
                            </p>
                        </div>

                        <a class="to-the-top" href="#site-header">
                            <span class="to-the-top-long">
                                <?php
						/* translators: %s: HTML character for up arrow */
						printf( __( 'To the top %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
						?>
                            </span><!-- .to-the-top-long -->
                            <span class="to-the-top-short">
                                <?php
						/* translators: %s: HTML character for up arrow */
						printf( __( 'Up %s', 'twentytwenty' ), '<span class="arrow" aria-hidden="true">&uarr;</span>' );
					?>
                            </span>
                        </a>

                    </div>

                </footer>

                <?php elseif ( in_array( 'footer-top-hidden', get_body_class() ) ) : ?>
                <div id="footer-placeholder"></div>
                <?php endif; ?>

                <?php wp_footer(); ?>

                </body>

                </html>

                <?php
	$templates = array();
	$name = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "footer-{$name}.php";
	}

	$templates[] = 'footer.php';
	
	ob_start();
	locate_template( $templates, true );
	ob_get_clean();
}
add_action( 'get_footer', 'veintiv_get_footer' );