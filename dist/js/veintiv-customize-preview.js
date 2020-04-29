/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function( api, $ ) {

	api.bind( 'preview-ready', function() {	
		// Disable smooth scroll when controls sidebar is expanded
		$( 'html' ).css( 'scroll-behavior', 'auto' );
		
		api.preview.bind( 'veintiv-customizer-sidebar-expanded', function( expanded ) {
			$( 'html' ).css( 'scroll-behavior', 'true' === expanded ? 'auto' : 'smooth' );
		} );
	} );

	api( 'veintiv_text_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-text-width-medium viv-text-width-wide' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-text-width-' + to );		
			}
		} );
	} );

	api( 'veintiv_body_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-site-font-small viv-site-font-medium' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-site-font-' + to );		
			}
		} );
	} );

	api( 'veintiv_body_line_height', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-site-lh-medium viv-site-lh-loose' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-site-lh-' + to );		
			}
		} );
	} );

	api( 'veintiv_heading_letter_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).toggleClass( 'viv-heading-ls-normal', to === 'normal' );	
		} );
	} );

	api( 'veintiv_h1_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-h1-font-small viv-h1-font-medium viv-h1-font-large' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-h1-font-' + to );		
			}
		} );
	} );

	api( 'veintiv_logo_text_transform', function( value ) {
		value.bind( function( to ) {
			$( '.site-title' ).css( { 'text-transform': to ? to : 'none' } );
		} );
	} );

	api( 'veintiv_menu_spacing', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-nav-spacing-medium viv-nav-spacing-large' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-nav-spacing-' + to );		
			}
		} );
	} );

	api( 'veintiv_menu_font_size', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-nav-size-small viv-nav-size-medium viv-nav-size-larger' );	
			if ( to ) {
				$( 'body' ).addClass( 'viv-nav-size-' + to );		
			}
		} );
	} );

	api( 'veintiv_menu_text_transform', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( 'ul.primary-menu a, ul.modal-menu a' ).css( { 'text-transform': to, 'letter-spacing': 'uppercase' === to ? '0.0333em' : 'normal' } );
			} else {
				$( 'ul.primary-menu a, ul.modal-menu a' ).css( { 'text-transform': 'none', 'letter-spacing':'normal' } );
			}
		} );
	} );

	api( 'veintiv_cover_page_height', function( value ) {
		value.bind( function( to ) {
			$( '.page-template-template-cover' ).removeClass( 'viv-cover-medium' );
			if ( to ) {
				$( '.page-template-template-cover' ).addClass( 'viv-cover-' + to );
			}
		} );
	} );

	api( 'veintiv_cover_post_height', function( value ) {
		value.bind( function( to ) {
			$( '.post-template-template-cover' ).removeClass( 'viv-cover-medium' );
			if ( to ) {
				$( '.post-template-template-cover' ).addClass( 'viv-cover-' + to );
			}
		} );
	} );

	api( 'veintiv_cover_vertical_align', function( value ) {
		value.bind( function( to ) {
			$( '.template-cover' ).removeClass( 'viv-cover-center' );
			if ( to ) {
				$( '.template-cover' ).addClass( 'viv-cover-' + to );
			}
		} );
	} );
	
	api( 'veintiv_cover_page_scroll_indicator', function( value ) {
		value.bind( function( to ) {
			$( '.page-template-template-cover' ).toggleClass( 'viv-cover-hide-arrow' );
		} );
	} );
		
	api( 'veintiv_header_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-header-wide viv-header-full' );
			if ( to ) {
				$( 'body' ).addClass( 'viv-header-' + to );
			}
		} );
	} );

	api( 'veintiv_footer_width', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).removeClass( 'viv-footer-wider viv-footer-full' );
			if ( to ) {
				$( 'body' ).addClass( 'viv-footer-' + to );
			}
		} );
	} );

	function veintivLoadGoogleFont( context, to ) {
		if ( to && 'sans-serif' !== to ) {
			var font, style, el, styleID, fontVariations;

			font = to.replace( / /g, '+' );
			fontVariations = 'body' === context ? '400,400i,500,600,700,800,900' : '400,500,600,700,800,900';
			styleID = 'veintiv-customizer-font-' + context;
			style = '<link rel="stylesheet" type="text/css" id="' + styleID + '" href="https://fonts.googleapis.com/css?family=' + font + ':' + fontVariations + '">';
			el = $( '#' + styleID );
				
			if ( el.length ) {
				el.replaceWith( style );
			} else {
				$( 'head' ).append( style );
			}
		}
	}

	api( 'veintiv_body_font', function( value ) {
		var onChange = function( to ) {
			veintivLoadGoogleFont( 'body', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );

	api( 'veintiv_heading_font', function( value ) {
		var onChange = function( to ) {
			veintivLoadGoogleFont( 'heading', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );

	api( 'veintiv_logo_font', function( value ) {
		var onChange = function( to ) {
			veintivLoadGoogleFont( 'logo', to );
		};
		onChange( value.get() );
		value.bind( onChange );
	} );
	
	var style = $( '#veintiv-customizer-live-css' );
	if ( ! style.length ) {
		style = $( 'head' ).append( '<style type="text/css" id="veintiv-customizer-live-css" />' ).find( '#veintiv-customizer-live-css' );
	}

	api.bind( 'preview-ready', function() {	
		api.preview.bind( 'update-customizer-live-css', function( css ) {
			style.text( css );
		} );				
	} );

})( wp.customize, jQuery );
