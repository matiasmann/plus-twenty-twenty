/**
 * Theme Customizer enhancements and logic.
 */

(function( api, $ ) {

	api.sectionConstructor['veintiv-more'] = api.Section.extend( {
		attachEvents: function () {},
		isContextuallyActive: function () {
			return true;
		}
	} );

	api.controlConstructor['checkbox-multiple'] = api.Control.extend( {
		ready: function() {	
			var control = this,
				container = this.container;	

			container.on( 'change', 'input[type="checkbox"]', function() {
				var values = container.find( 'input[type="checkbox"]:checked' ).map( function() {
					return this.value;
				} ).get();

				if ( null === values ) {
					control.setting.set( '' );
				} else {
					control.setting.set( values );
				}
			});
		}
	});

	api.bind( 'ready', function() {

		$( '.collapse-sidebar' ).on( 'click', function() {
			api.previewer.send( 'veintiv-customizer-sidebar-expanded', $( this ).attr( 'aria-expanded' ) );
		});

		$.each( {
			'veintiv_cover_page_height': {
				controls: [ 'veintiv_cover_page_scroll_indicator' ],
				callback: function( to ) { return '' === to; }
			},
			'veintiv_footer_credit': {
				controls: [ 'veintiv_footer_credit_text' ],
				callback: function( to ) { return 'custom' === to; }
			},
			'blog_content': {
				controls: [ 'veintiv_blog_excerpt_more', 'veintiv_blog_excerpt_length' ],
				callback: function( to ) { return 'summary' === to; }
			},
		}, function( settingId, o ) {
			wp.customize( settingId, function( setting ) {
				$.each( o.controls, function( i, controlId ) {
					api.control( controlId, function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};
						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			});
		});

		api( 'custom_logo', function( setting ) {			
			var onChange = function( logo ) {
				api.control( 'veintiv_typo_section_title_logo', function( control ) {
					control.container.find( '.description' ).toggle( !! logo );				
				});	
				$.each( [ 'veintiv_custom_logo_transparent', 'veintiv_logo_mobile_width' ], function( i, controlId ) {
					api.control( controlId, function( control ) {
						control.container.toggle( !! logo );				
					});					
				});
				$.each( [ 'veintiv_logo_font', 'veintiv_logo_font_weight', 'veintiv_logo_font_size', 'veintiv_logo_mobile_font_size', 'veintiv_logo_letter_spacing', 'veintiv_logo_text_transform' ], function( i, controlId ) {
					api.control( controlId, function( control ) {
						control.container.toggle( '' === logo );				
					});					
				});
			};	
			onChange( setting.get() );
			setting.bind( onChange );
		} );

		var hasLogoFont = api( 'veintiv_logo_font' ) !== undefined ? true : false;

		api( 'veintiv_heading_font', function( setting ) {
			var onChange = function( font ) {

				if ( font === '' ) {
					api.previewer.refresh(); 
				}

				updateControlFontWeights( 'veintiv_heading_font_weight', font );

				var menu_font = api( 'veintiv_menu_font' ).get();
				if ( 'heading' === menu_font ) {
					updateControlFontWeights( 'veintiv_menu_font_weight', font );					
				} 
				if ( hasLogoFont ) {
					var logo_font = api( 'veintiv_logo_font' ).get();
					if ( '' === logo_font ) {
						updateControlFontWeights( 'veintiv_logo_font_weight', font );					
					}
				}
			};	
			onChange( setting.get() );
			setting.bind( onChange );
		} );

		api( 'veintiv_body_font', function( setting ) {
			var onChange = function( font ) {

				if ( font === '' ) {
					api.previewer.refresh(); 
				}

				var menu_font = api( 'veintiv_menu_font' ).get();
				if ( 'body' === menu_font ) {
					updateControlFontWeights( 'veintiv_menu_font_weight', font );					
				}
			};	
			onChange( setting.get() );
			setting.bind( onChange );
		} );

		if ( hasLogoFont ) {
			api( 'veintiv_logo_font', function( setting ) {
				var onChange = function( font ) {
					if ( font === '' ) {					
						updateControlFontWeights( 'veintiv_logo_font_weight', api( 'veintiv_heading_font' ).get() );
						api.previewer.refresh(); 
					} else {
						updateControlFontWeights( 'veintiv_logo_font_weight', font );
					}				
				};	
				onChange( setting.get() );
				setting.bind( onChange );
			} );	
		}		

		api( 'veintiv_menu_font', function( setting ) {
			var onChange = function( font ) {
				updateControlFontWeights( 'veintiv_menu_font_weight', font );
			};	
			onChange( setting.get() );
			setting.bind( onChange );
		} );

		api( 'veintiv_logo_font_size', function( setting ) {
			var onChange = function( size ) {
				if ( size === '' ) {					
					api.previewer.refresh(); 
				} 			
			};	
			setting.bind( onChange );
		} );

		// Live fonts: tells the Customizer previewer to update the CSS
 		var cssTemplate = wp.template( 'veintiv-customizer-live-style' );
		var live_settings = [ 
			'veintiv_body_font', 
			'veintiv_heading_font', 
			'veintiv_heading_font_weight',
			'veintiv_secondary_font', 
			'veintiv_menu_font', 
			'veintiv_menu_font_weight',
			'veintiv_logo_font',
			'veintiv_logo_font_weight',
			'veintiv_logo_font_size',
			'veintiv_logo_letter_spacing',
		];

		// Update the CSS whenever a live setting is changed.
		_.each( live_settings, function( set ) {
			api( set, function( setting ) {		
				setting.bind( function( to ) {				
					updateCSS();	
				});
			});
		});
		
		// Generate the CSS for the current live settings.
		function updateCSS() {	
			var css,		
				styles = _.object( );	
			_.each( live_settings, function( setting ) {	
				styles[ setting ] = api( setting ) !== undefined ? api( setting )() : '';
			});	
			css = cssTemplate( styles );
			css = _.unescape(css);
			css = css.replace(/\s{2,}/g, ' ' );	
			api.previewer.send( 'update-customizer-live-css', css );
		}
	
		var veintivBgColors = veintivCustomizerSettings['colorVars'];

		// Add a listener for accent-color changes.
		wp.customize( 'accent_hue', function( value ) {
			value.bind( function( to ) {
				Object.keys( veintivBgColors ).forEach( function( context ) {
					var backgroundColorValue;
					if ( veintivBgColors[ context ].color ) {
						backgroundColorValue = veintivBgColors[ context ].color;
					} else {
						backgroundColorValue = wp.customize( veintivBgColors[ context ].setting ).get();
					}
					veintivSetAccessibleColorsValue( context, backgroundColorValue, to );
				} );
			} );
		} );

		// Add a listener for background-color changes.
		Object.keys( veintivBgColors ).forEach( function( context ) {
			wp.customize( veintivBgColors[ context ].setting, function( value ) {
				value.bind( function( to ) {
					// Update the value for our accessible colors for this area.
					veintivSetAccessibleColorsValue( context, to, wp.customize( 'accent_hue' ).get() );
				} );
			} );
		} );

	});

	// Update font-weight controls options
	function updateControlFontWeights( fontWeightControl, fontFamily ) {
		var selectedFont = fontFamily;
		if ( 'heading' === fontFamily ) {
			selectedFont = api( 'veintiv_heading_font' ).get();
		} else if ( 'body' === fontFamily ) {
			selectedFont = api( 'veintiv_body_font' ).get();
		} 

		var weightOpt = '';
		$.each( [ '400', '500', '600', '700', '800' ], function( key, value) {
			weightOpt += '<option value="' + value + '">' + veintivCustomizerSettings.fontVariants[ value ] + '</option>';
		});

		if ( selectedFont && 'sans-serif' !== selectedFont ) {
			weightOpt = '';				
			var fontObj = _.find( veintivCustomizerSettings.fonts, function( font ) { return font.family === selectedFont; } );
			if ( ! $.isEmptyObject( fontObj ) && ! _.isUndefined( fontObj.variants ) ) {
				$.each( fontObj.variants, function( key, value ) {
					weightOpt += '<option value="' + value + '">' + veintivCustomizerSettings.fontVariants[ value ] + '</option>';
				});									
			}					 
		}

		api.control( fontWeightControl, function( control ) {
			var value = control.setting.get();
			control.container.find( 'select' ).empty().append( weightOpt ).find( 'option[value="' + value + '"]' ).prop( 'selected', true );
			control.setting.set( control.container.find( 'select' ).val() );
		});	
	}

	//Updates the value of the "accent_accessible_colors" setting.
	function veintivSetAccessibleColorsValue( context, backgroundColor, accentHue ) {
		var value, colors;

		value = wp.customize( 'veintiv_accessible_colors' ).get();
		value = ( _.isObject( value ) && ! _.isArray( value ) ) ? value : {};

		colors = twentyTwentyColor( backgroundColor, accentHue );

		if ( colors.getAccentColor() && 'function' === typeof colors.getAccentColor().toCSS ) {
			
			value[ context ] = {
				text: colors.getTextColor(),
				accent: colors.getAccentColor().toCSS(),
				background: backgroundColor
			};

			value[ context ].borders = colors.bgColorObj
				.clone()
				.getReadableContrastingColor( colors.bgColorObj, 1.36 )
				.toCSS();

			value[ context ].secondary = colors.bgColorObj
				.clone()
				.getReadableContrastingColor( colors.bgColorObj )
				.s( colors.bgColorObj.s() / 2 )
				.toCSS();
		}

		// Important to trigger change.
		wp.customize( 'veintiv_accessible_colors' ).set( '' );		
		wp.customize( 'veintiv_accessible_colors' ).set( value );
		// Small hack to save the option.
		wp.customize( 'veintiv_accessible_colors' )._dirty = true;
	}

})( wp.customize, jQuery );
