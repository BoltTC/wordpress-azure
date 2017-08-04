var __thrive_$oJ = window.$;
var ThriveGlobal = {$j: jQuery.noConflict()};
if ( __thrive_$oJ ) {
	window.$ = __thrive_$oJ;
}
; var TVE_jQFn = {};

var TCB_Front = {
	tableSort: function ( $th ) {
		$th.on( 'click', function () {
			var $thisTh = ThriveGlobal.$j( this ),
				headingIndex = $thisTh.index(),
				parentTable = $thisTh.parents( '.tve_make_sortable' ),
				$tbody = parentTable.find( 'tbody' ),
				dataString = [],
				dataNumber = [],
				direction = $thisTh.attr( 'data-direction' ) == 'down' ? 'up' : 'down',
				data = [];

			$thisTh.attr( 'data-direction', direction );

			ThriveGlobal.$j( parentTable ).find( 'tbody tr' ).each( function () {
				var $tr = ThriveGlobal.$j( this ),
					$td = $tr.find( '> td' ).eq( headingIndex ),
					content = parseInt( $td.text() );
				if ( isNaN( content ) ) {
					dataString.push( {
						tr: $tr,
						text: $td.text().trim().toLowerCase()
					} );
				} else {
					dataNumber.push( {
						tr: $tr,
						text: content
					} );
				}
			} );

			function sortArrayAscending( a, b ) {
				return (
					a.text == b.text
				) ? 0 : (
					(
						a.text > b.text
					) ? 1 : - 1
				);
			}

			function sortArrayDescending( a, b ) {
				return (
					a.text == b.text
				) ? 0 : (
					(
						a.text > b.text
					) ? - 1 : 1
				);
			}

			direction == 'down' ? dataString.sort( sortArrayAscending ) : dataString.sort( sortArrayDescending );
			direction == 'down' ? dataNumber.sort( sortArrayAscending ) : dataNumber.sort( sortArrayDescending );

			data = direction == 'down' ? dataNumber.concat( dataString ) : dataString.concat( dataNumber );

			ThriveGlobal.$j.each( data, function ( item, index ) {
				$tbody.append( index.tr );
			} );

			$th.attr( 'data-direction', '' );
			$thisTh.attr( 'data-direction', direction );
		} );

	},
	getCookie: function ( name ) {
		var nameEQ = name + "=";
		var ca = document.cookie.split( ';' );
		for ( var i = 0; i < ca.length; i ++ ) {
			var c = ca[i];
			while ( c.charAt( 0 ) == ' ' ) {
				c = c.substring( 1, c.length );
			}
			if ( c.indexOf( nameEQ ) == 0 ) {
				return c.substring( nameEQ.length, c.length );
			}
		}
		return null;
	},
	setCookie: function ( key, value, options ) {
		if ( typeof options.expires === 'number' ) {
			var days = options.expires, t = options.expires = new Date();
			t.setTime( + t + days * 864e+5 );
		}

		return (
			document.cookie = [
				encodeURIComponent( key ), '=', encodeURIComponent( value ),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path ? '; path=' + options.path : '',
				options.domain ? '; domain=' + options.domain : '',
				options.secure ? '; secure' : ''
			].join( '' )
		);
	},
	postGridLayout: function () {

		var _selector = ThriveGlobal.$j( '.tve_post_grid_masonry' );

		if ( _selector.length > 0 ) {
			try {
				_selector.masonry();
			} catch ( e ) {
				console.log( e );
			}
		}

		var $grid = ThriveGlobal.$j( '.tve_post_grid_grid' );

		if ( $grid.length <= 0 ) {
			return;
		}

		$grid.find( '.tve_pg_row' ).each( function () {
			var _maxHeight = 0,
				$row = ThriveGlobal.$j( this ).css( 'height', '' );

			$row.find( '.tve_post' ).each( function () {
				var _this = ThriveGlobal.$j( this ),
					_elHeight = parseInt( _this.css( 'height' ) );
				if ( _elHeight > _maxHeight ) {
					_maxHeight = _elHeight;
				}
				_this.css( 'height', '100%' );
			} );
			$row.css( 'height', (
				                    _maxHeight - 1
			                    ) + 'px' );
		} );
	},
	changeAutoplayVideo: function () {
		/*
		 * change responsive video src
		 */
		ThriveGlobal.$j( '.thrv_responsive_video' ).each( function () {
			var $this = ThriveGlobal.$j( this ),
				_type = $this.attr( 'data-type' ),
				$frame, _autoplay, _src;

			if ( tve_frontend_options.is_editor_page ) {
				return;
			}

			if ( $this.attr( 'data-autoplay' ) !== '1' ) {
				return;
			}

			switch ( _type ) {
				case 'youtube':
				case 'vimeo':
					_autoplay = '&autoplay=1';
					break;
				case 'wistia':
					_autoplay = '&autoPlay=true';
					break;
				case 'self':
				case 'custom':
				case 'default':
					break;
			}

			if ( _type === 'youtube' || _type === 'vimeo' || _type === 'wistia' ) {
				$frame = $this.find( 'iframe' );
				_src = $frame.attr( 'src' );

				if ( _src.length !== 0 ) {
					$frame.attr( 'src', _src + _autoplay );
				}

			} else if ( _type === 'custom' || _type === 'self' ) {
				$this.find( 'video' ).get( 0 ).play();
			}


//			var $this = ThriveGlobal.$j( this ),
//				$frame = $this.find( 'iframe' );
//			if ( $frame.length == 0 ) {
//				$frame = $this.find( '.tve-video' );
//			}
//			var _src = $frame.attr( 'src' ),
//				_data_src = $frame.attr( 'data-src' ),
//				_type = $this.attr( 'data-type' ),
//				_autoplay = '';
//			if ( _data_src === undefined ) {
//				_data_src = '';
//			}
//
//			if ( _type == 'youtube' || _type == 'vimeo' ) {
//				_autoplay = '&autoplay=1';
//			} else if ( _type == 'wistia' ) {
//				_autoplay = '&autoPlay=true';
//			} else if ( _type == 'self' || _type == 'custom' ) {
//				_autoplay = '1';
//			}
//
//			if ( tve_frontend_options.is_editor_page && $this.attr( 'data-autoplay' ) === '1' ) {
//				if ( _type !== 'self' ) {
//					var _new_src = _src.replace( _autoplay, '' ),
//						_new_data_src = _data_src.replace( _autoplay, '' );
//					$frame.attr( 'src', _new_src ).attr( 'data-src', _new_data_src );
//				}
//			}
//
//			if ( ! tve_frontend_options.is_editor_page && $this.attr( 'data-autoplay' ) === '1' ) {
//				if ( _data_src.length !== 0 ) {
//					$frame.attr( 'data-src', _data_src + _autoplay );
//				}
//				if ( _src.length !== 0 ) {
//					if ( _type == 'self' || _type == 'custom' ) {
//						$frame.attr( 'autoplay', _autoplay );
//					} else {
//						$frame.attr( 'src', _src + _autoplay );
//					}
//
//				}
//
//			}
		} );
	},
	isValidUrl: function ( s ) {
		var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
		return regexp.test( s );
	},
	/**
	 * automatic resizing of page sections - this is only called on Landing Pages when the user does not have a Thrive Theme
	 */
	pageSectionHeight: function () {
		var $window = TCB_Front.$window;

		ThriveGlobal.$j( '.pdfbg.pdwbg' ).css( {
			'box-sizing': "border-box",
			height: $window.height() + 'px'
		} );

		ThriveGlobal.$j( '.pddbg' ).css( 'max-width', $window.width() + 'px' );

		ThriveGlobal.$j( '.pddbg.pdfbg' ).each( function () {
			var img = ThriveGlobal.$j( this ).css( 'height', '' ),
				imgHeight = img.attr( 'data-height' ),
				imgWidth = img.attr( 'data-width' );

			if ( imgHeight !== undefined && imgWidth !== undefined ) {

				var _parentWidth = img.parent().width(),
					ratio = (
						        _parentWidth * imgHeight
					        ) / imgWidth;
				if ( _parentWidth <= imgWidth ) {
					img.css( 'min-height', ratio + 'px' );
				} else {
					img.css( {
						'min-height': imgHeight + 'px'
					} );
				}
			}
		} );
	},
	getBrowserScrollSize: function () {
		var $ = ThriveGlobal.$j;
		var css = {
			"border": "none",
			"height": "200px",
			"margin": "0",
			"padding": "0",
			"width": "200px"
		};

		var inner = $( "<div>" ).css( $.extend( {}, css ) );
		var outer = $( "<div>" ).css( $.extend( {
			"left": "-1000px",
			"overflow": "scroll",
			"position": "absolute",
			"top": "-1000px"
		}, css ) ).append( inner ).appendTo( "body" ).scrollLeft( 1000 ).scrollTop( 1000 );

		var scrollSize = {
			"height": (
				          outer.offset().top - inner.offset().top
			          ) || 0,
			"width": (
				         outer.offset().left - inner.offset().left
			         ) || 0
		};

		outer.remove();
		return scrollSize;
	},
	openLightbox: function ( $target, animation ) {
		var $body = ThriveGlobal.$j( 'body' ),
			$html = ThriveGlobal.$j( 'html' ),
			overflow_hidden = 'tve-o-hidden tve-l-open tve-hide-overflow',
			scroll_width = this.getBrowserScrollSize().width,
			oPadding = parseInt( $body.css( 'padding-right' ) );
		$target.find( 'input[placeholder]' ).thrive_iphone_placeholder();

		var video = $target.find( 'video' );
		video.each( function () {
			ThriveGlobal.$j( this )[0].currentTime = 0;
			ThriveGlobal.$j( this )[0].play();
		} );

		function close_it( $lightbox, skip_body_scroll ) {
			$lightbox.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
				var $this = ThriveGlobal.$j( this ).addClass( 'tcb-dr-done' );
				$this.attr( 'data-src', $this.attr( 'src' ) );
				$this.attr( 'src', '' );
			} );
			var video = $lightbox.find( 'video' );
			video.each( function () {
				ThriveGlobal.$j( this )[0].pause();
			} );

			if ( typeof skip_body_scroll === 'undefined' ) {
				if ( ThriveGlobal.$j( '.tve_lb_open' ).length === 1 ) {
					$body.removeClass( overflow_hidden ).css( 'padding-right', '' );
					$html.removeClass( overflow_hidden );
				}
			}
			$lightbox.removeClass( 'tve_lb_open tve_lb_opening' ).addClass( 'tve_lb_closing' );

			setTimeout( function () {
				$lightbox.attr( 'class', '' ).css( 'display', 'none' ).find( 'tve_p_lb_content' ).trigger( 'tve.lightbox-close' );
			}, 300 );
			/**
			 * close any error messages that might have been displayed on forms inside the lightbox
			 */
			ThriveGlobal.$j( '#tve-lg-error-container' ).hide();
		}

		$target.off().on( "click", ".tve_p_lb_close", function () {
			close_it( $target );
			return false;
		} );

		$body.off( 'keyup.tve_lb_close' ).on( 'keyup.tve_lb_close', function ( e ) {
			if ( e.which == 27 ) {
				close_it( $target );
				return false;
			}
		} );

		$target.children( '.tve_p_lb_overlay' ).off( 'click.tve_lb_close' ).on( 'click.tve_lb_close', function () {
			close_it( $target );
			return false;
		} );

		/* close any other opened lightboxes */
		close_it( ThriveGlobal.$j( '.tve_p_lb_background.tve_lb_open' ), true );

		$target.addClass( 'tve_p_lb_background tve_lb_anim_' + animation );

		$body.addClass( overflow_hidden );
		$html.addClass( overflow_hidden );

		var wHeight = TCB_Front.$window.height(),
			page_has_scroll = wHeight < ThriveGlobal.$j( document ).height();

		if ( page_has_scroll ) {
			$body.css( 'padding-right', ( oPadding + scroll_width ) + 'px' );
		}

		if ( $target.find( '.tve_scT' ).length ) {
			$target.find( '.tve_scT' ).each( function () {
				var $this = ThriveGlobal.$j( this ),
					_selected = parseInt( $this.attr( 'data-selected' ) );
				if ( ! tve_frontend_options.is_editor_page ) {
					$this.find( '> ul li' ).eq( isNaN( _selected ) ? 0 : _selected ).click();
				}
			} );
		} else {
			$target.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
				var $this = ThriveGlobal.$j( this );
				if ( $this.attr( 'data-src' ) ) {
					$this.attr( 'src', $this.attr( 'data-src' ) );
				}
			} );
		}

		/**
		 *
		 * @param {Boolean} [animate]
		 */
		function position_it( animate ) {
			var $lContent = $target.find( '.tve_p_lb_content' ),
				cHeight, top, wHeight = TCB_Front.$window.height(),
				fn = typeof animate !== 'undefined' && animate ? 'animate' : 'css';
			TCB_Front.postGridLayout();
			cHeight = $lContent.outerHeight( true );
			top = ( wHeight - cHeight ) / 2;
			$target.find( '.tve_p_lb_overlay' )[fn]( {
				height: ( cHeight + 80 ) + 'px',
				'min-height': wHeight + 'px'
			}, 200 );
			$lContent[fn]( {
				'top': ( top < 40 ? 40 : top ) + 'px'
			}, 200 );
			if ( cHeight + 40 > wHeight ) {
				$target.addClass( 'tve-scroll' );
			}
		}

		setTimeout( function () {
			$target.css( 'display', '' );
			$target.addClass( 'tve_lb_opening' );
			setTimeout( function () {
				position_it();
			}, 0 );
		}, 20 );
		$target.find( '.tve_p_lb_content' ).trigger( 'tve.before-lightbox-open' );
		setTimeout( function () {
			$target.removeClass( 'tve_lb_opening' ).addClass( 'tve_lb_open' ).find( '.tve_p_lb_content' ).trigger( 'tve.lightbox-open' );
		}, 300 );
		TCB_Front.$window.resize( function () {
			position_it();
		} );
		$target.on( 'lbresize', function () {
			position_it( true );
		} );
	},
	event_triggers: function ( $evt_root ) {
		if ( ! window.TVE_Event_Manager_Registered_Callbacks ) {
			return;
		}
		$evt_root.find( '.tve_evt_manager_listen' ).each( function () {
			var $this = ThriveGlobal.$j( this ),
				events = $this.attr( 'data-tcb-events' );
			if ( ! events ) {
				/* try also getting the closest wrapper */
				events = $this.closest( '.thrv_wrapper' ).attr( 'data-tcb-events' );
			}
			if ( ! events ) {
				return true;
			}
			try {
				events = ThriveGlobal.$j.parseJSON( events.replace( '__TCB_EVENT_', '' ).replace( '_TNEVE_BCT__', '' ) );
				ThriveGlobal.$j.each( events, function ( i, event_data ) {
					if ( ! TVE_Event_Manager_Registered_Callbacks[event_data.a] ) {
						return;
					}
					$this.off( (event_data.t === 'mouseover' ? 'mouseenter' : event_data.t) + '.tcbevt' + event_data.t ).on( event_data.t + '.tcbevt' + event_data.t, function () {
						return TVE_Event_Manager_Registered_Callbacks[event_data.a].call( $this[0], event_data.t, event_data.a, event_data.config ? event_data.config : {} );
					} );
				} );
			} catch ( e ) {
				console.log( 'Could not parse events' );
				console.log( e );
			}
		} );
	},
	show_data_elemements: function ( position ) {
		if ( ! tve_frontend_options.is_editor_page ) {
			ThriveGlobal.$j( '.thrv_data_element' ).filter( ':not(.thrv_data_element_start)' ).each( function () {
				var $this = ThriveGlobal.$j( this ),
					windowHeight = TCB_Front.$window.height();
				if ( position + windowHeight >= $this.offset().top + $this.outerHeight() ) {
					$this.addClass( 'thrv_data_element_start' ).trigger( 'tve.start-animation' );
				}
			} );
		}
	},
	onDOMReady: function () {
		/**
		 * Video backgrounds - autoplay and mute sound (?)
		 */
		ThriveGlobal.$j( '.tcb-video-background-el' ).each( function () {
			this.parentNode.classList.add( 'tcb-video-background-parent' );
		} );

		if ( ! window.TVE_Dash || TVE_Dash.ajax_sent ) {
			this.getShareCounts();
		} else {
			ThriveGlobal.$j( document ).on( 'tve-dash.load', function ( event ) {
				var _data = TCB_Front.getShareCounts( ThriveGlobal.$j( 'body' ), {}, true );
				if ( ! _data ) {
					return;
				}
				TVE_Dash.add_load_item( 'tcb_social', _data[0], _data[1] );
			} );
		}
		ThriveGlobal.$j.each( TVE_jQFn, function ( key, fnImpl ) {
			ThriveGlobal.$j.fn[key] = fnImpl;
		} );

		/**
		 * Responsive menus
		 */
		var $menus = ThriveGlobal.$j( 'ul.tve_w_menu.tve_horizontal' ),
			total = $menus.length;
		$menus.each( function () {
			this.style.zIndex = total + 5;
			ThriveGlobal.$j( this ).find( 'ul' ).css( 'z-index', total + 6 );
			total --;
		} );

		ThriveGlobal.$j( '.tve_scT' ).each( function () {
			var $this = ThriveGlobal.$j( this ),
				_selected = parseInt( $this.attr( 'data-selected' ) );

			$this.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
				var $frame = ThriveGlobal.$j( this );
				if ( $frame.is( ":visible" ) && $frame.attr( 'data-src' ) && $frame.attr( 'data-src' ).length && $frame.attr( 'src' ).length <= 0 ) {
					$frame.attr( 'src', $frame.attr( 'data-src' ) );
					$frame.attr( 'data-src', '' );
					$frame.removeClass( 'tcb-dr-done' );
				} else if ( ! $frame.is( ":visible" ) ) {
					if ( $frame.attr( 'src' ).length ) {
						$frame.addClass( 'tcb-dr-done' );
						$frame.attr( 'data-src', $frame.attr( 'src' ) );
						$frame.attr( 'src', '' );
					}
				}
			} );
			if ( ! tve_frontend_options.is_editor_page ) {
				$this.find( '> ul li' ).eq( isNaN( _selected ) ? 0 : _selected ).click();
			}
		} );
		ThriveGlobal.$j( ".thrv_toggle_shortcode" ).each( function () {
			var $this = ThriveGlobal.$j( this );
			$this.find( 'iframe' ).not( '.thrv_social_default iframe' ).not( '.tcb-dr-done' ).each( function () {
				var $frame = ThriveGlobal.$j( this );
				$frame.addClass( 'tcb-dr-done' );
				if ( $frame.attr( 'src' ) ) {
					$frame.attr( 'data-src', $frame.attr( 'src' ) );
				}
				$frame.attr( 'src', '' );
			} );
		} );
		if ( ! tve_frontend_options.is_editor_page ) {

			ThriveGlobal.$j( document ).on( 'click', '.tve-close-error-message', function () {
				ThriveGlobal.$j( this ).parent().hide().prev().show();
			} );
			/**
			 * fill counter animation
			 */
			ThriveGlobal.$j( '.thrv_fill_counter' ).each( function () {
				var $this = ThriveGlobal.$j( this );
				$this.one( 'tve.start-animation', function () {
					var fill_counter_element = $this.find( '.tve_fill_counter' ),
						rotation = fill_counter_element.attr( 'data-fill' ),
						fix_rotation = rotation * 2,
						transform_styles = ['-webkit-transform', '-ms-transform', 'transform'];
					for ( var i in transform_styles ) {
						ThriveGlobal.$j( '.tve_fill_c_in, .tve_fill_circle.tve_fill_circle1', this ).css( transform_styles[i], 'rotate(' + rotation + 'deg)' );
						ThriveGlobal.$j( '.tve_fill_c_in-d', this ).css( transform_styles[i], 'rotate(' + fix_rotation + 'deg)' );
					}
				} );
			} );
			/**
			 * number counter
			 */
			ThriveGlobal.$j( '.thrv_number_counter' ).each( function () {
				var $this = ThriveGlobal.$j( this );

				$this.on( 'tve.start-animation', function () {
					var counter_element = ThriveGlobal.$j( '.tve_numberc_text', this ),
						count_to = counter_element.attr( 'data-counter' ),
						count_start = counter_element.attr( 'data-counter-start' ) ? counter_element.attr( 'data-counter-start' ) : 0;

					var i = parseInt( count_start ), t = null,
						step = Math.ceil( (
							                  count_to > i ? count_to : i
						                  ) / 100 );

					step = step ? step : 1; //for the cases when ceil step is zero

					function countUp( i, count_to ) {
						if ( i <= count_to ) {
							counter_element.text( i );
							i += step;
							if ( i + step > count_to ) {
								counter_element.text( count_to );
								clearTimeout( t );
							}
							t = setTimeout( function () {
								countUp( i, count_to )
							}, 50 );
						} else {
							clearTimeout( t );
						}
					}

					function countDown( i, count_to ) {
						if ( i >= count_to ) {
							counter_element.text( i );
							i -= step;
							if ( i - step < count_to ) {
								counter_element.text( count_to );
								clearTimeout( t );
							}
							t = setTimeout( function () {
								countDown( i, count_to )
							}, 50 );
						} else {
							clearTimeout( t );
						}
					}

					if ( i < count_to ) {
						countUp( i, count_to );
					} else {
						countDown( i, count_to );
					}

				} );
			} );

			/**
			 * go through every lightbox that has a responsive video and disable the autoplay feature (to prevent it from playing in the background)
			 */
			var video = ThriveGlobal.$j( '.tve_p_lb_content' ).find( 'video' );
			video.each( function () {
				ThriveGlobal.$j( this )[0].pause();
			} );

			ThriveGlobal.$j( '.tve_p_lb_content iframe' ).not( '.thrv_social_default iframe' ).not( '.tcb-dr-done' ).each( function () {
				var $frame = ThriveGlobal.$j( this ).addClass( 'tcb-dr-done' );
				if ( $frame.attr( 'src' ) ) {
					$frame.attr( 'data-src', $frame.attr( 'src' ) );
				}
				$frame.attr( 'src', '' );
			} );
			/**
			 * Content Reveal elements inside content reveal elements
			 *
			 */
			ThriveGlobal.$j( '.thrv_content_reveal' ).each( function () {
				var $this = ThriveGlobal.$j( this ),
					_when = parseInt( $this.attr( 'data-after' ) ),
					$is_lightbox = $this.parents( '.tve_p_lb_content' ),
					$is_tabs = $this.children( 'thrv_tabs_shortcode' ).length,
					$is_toggle = $this.children( '.thrv_toggle_shortcode' ).length;

				_when = isNaN( _when ) ? 0 : _when;

				$this.find( 'iframe' ).not( '.tcb-dr-done' ).not( '.thrv_social_default iframe' ).each( function () {
					var $frame = ThriveGlobal.$j( this );
					$frame.addClass( 'tcb-dr-done' );
					if ( $frame.attr( 'src' ) ) {
						$frame.attr( 'data-src', $frame.attr( 'src' ) );
					}
					$frame.attr( 'src', '' );
				} );

				var trigger_countdown = function () {
					setTimeout( function () {
						$this.slideDown( 200, function () {
							$this.trigger( 'lbresize' );
						} );
						if ( $this.data( 'scroll' ) ) {
							jQuery( 'html, body' ).animate( {
								scrollTop: $this.offset().top - 270
							} );
						}

						if ( tve_frontend_options.is_single === "1" && typeof $this.attr( 'data-redirect-url' ) === 'string' &&
						     $this.attr( 'data-redirect-url' ).length && TCB_Front.isValidUrl( $this.attr( 'data-redirect-url' ) ) ) {

							window.location = $this.attr( 'data-redirect-url' );
						}

						$this.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
							var $frame = ThriveGlobal.$j( this );
							if ( ! $is_tabs || ! $is_toggle ) {
								$frame.attr( 'src', $frame.attr( 'data-src' ) );
							}
						} );

						$this.trigger( 'tve-content-revealed' );

					}, _when * 1000 );
				};
				if ( $is_lightbox.length ) { /* if inside a lightbox, it needs to be triggered when the lightbox opens */
					$is_lightbox.bind( 'tve.lightbox-open', trigger_countdown );
				} else {
					trigger_countdown();
				}
			} );
			ThriveGlobal.$j( ".thrv_tw_qs" ).tve_front_tw_qs();
			ThriveGlobal.$j( '.thrv-countdown_timer_evergreen' ).thrive_tcb_timer();
			ThriveGlobal.$j( '.thrv-countdown_timer_plain' ).thrive_tcb_timer();
			ThriveGlobal.$j( ".thrv_lead_generation" ).tve_front_lead_generation();

			setTimeout( function () {
				try {

					ThriveGlobal.$j( ".tve_typefocus" ).each( function () {

						var $this = ThriveGlobal.$j( this ),
							strings = [],
							speed = parseInt( $this.attr( 'data-speed' ) ),
							color = $this.attr( 'data-colors' );

						if ( $this.attr( 'data-typist' ).length === 0 ) {
							return;
						}
						strings.push( $this.text() );
						strings = strings.concat( $this.attr( 'data-typist' ).split( "|" ) );
						$this.typed( {
							strings: strings,
							loop: true,
							typeSpeed: 125,
							backSpeed: 50,
							highlightClass: $this.attr( 'data-highlight' ) ? 'tve_selected_typist' : '',
							highlightStyle: $this.attr( 'data-highlight' ) && color ? ( 'background-color:' + color ) : '',
							backDelay: speed,
							contentType: 'text',
							startDelay: 800,
							showCursor: $this.hasClass( "tve_typefocus_cursor" )
						} );

					} );
				} catch ( e ) {
					console.log( e );
				}
			}, 1000 );
			ThriveGlobal.$j( '.thrv_responsive_video' ).off().click( function () {
				var $this = ThriveGlobal.$j( this ),
					$overlay = $this.find( '.video_overlay_image' ),
					_type = $this.attr( 'data-type' ),
					$frame, _autoplay, _src;


				switch ( _type ) {
					case 'youtube':
					case 'vimeo':
						_autoplay = '&autoplay=1';
						break;
					case 'wistia':
						_autoplay = '&autoPlay=true';
						break;
					case 'self':
					case 'custom':
					case 'default':
						break;
				}

				if ( _type === 'youtube' || _type === 'vimeo' || _type === 'wistia' ) {
					$frame = $this.find( 'iframe' );
					_src = $frame.attr( 'src' );

					if ( _src.length !== 0 ) {
						$frame.attr( 'src', _src + _autoplay );
					}
				} else if ( _type === 'custom' || _type === 'self' ) {
					$this.find( 'video' ).get( 0 ).play();
				}

				/**
				 * Has Video Overlay
				 */
				if ( $overlay.length > 0 ) {
					$overlay.fadeOut();
				}

//
//				if ( ! $this.data( 'tve-video-clicked' ) ) {
//					if ( ThriveGlobal.$j( this ).find( '.video_overlay_image' ).length != 0 ) {
//						var $frame = $this.find( 'iframe' );
//						if ( $frame.length === 0 ) {
//							$frame = $this.find( '.tve-video' );
//						}
//						var _src = $frame.attr( 'src' );
//						if ( _type == 'youtube' || _type == 'vimeo' ) {
//							$frame.attr( 'src', _src + '&autoplay=1' );
//							$this.find( '.video_overlay_image' ).fadeOut();
//						} else if ( _type == 'wistia' ) {
//							var $_container = $this.find( '.tve_wistia_container' );
//
//							if ( $_container.length === 0 ) {
//								$frame.attr( 'src', _src + '&autoPlay=true' );
//								$this.find( '.video_overlay_image' ).fadeOut();
//							} else {
//								if ( typeof Wistia !== 'undefined' ) {
//									var _video = Wistia.api( $_container.attr( 'id' ) ),
//										_start = parseInt( $_container.attr( "data-start-time" ) );
//									if ( ! isNaN( _start ) && _start ) {
//										_video.time( _start );
//									}
//									_video.play();
//								} else {
//									tcb_start_wistia_video( $this );
//								}
//								return false;
//							}
//						} else if ( _type == 'self' ) {
//							var video = ThriveGlobal.$j( this ).find( 'video' );
//							if ( video.prop( 'paused' ) ) {
//								video[0].play();
//								ThriveGlobal.$j( this ).find( '.video_overlay_image' ).removeClass();
//							} else {
//								video[0].pause();
//							}
//							$this.find( '.video_overlay_image' ).fadeOut();
//						}
//					} else {
//						var video = ThriveGlobal.$j( this ).find( 'video' );
//						if ( ! video.attr( 'controls' ) ) {
//							if ( video.prop( 'paused' ) ) {
//								video[0].play();
//							} else {
//								video[0].pause();
//							}
//						}
//
//					}
//				} else {
//					if ( _type == 'wistia' ) {
//						tcb_start_wistia_video( $this );
//					} else {
//						var isSafari = Object.prototype.toString.call( window.HTMLElement ).indexOf( 'Constructor' ) > 0;
//						var isChrome = navigator.userAgent.toLowerCase().indexOf( 'chrome' ) > - 1;
//						if ( (
//							     parseInt( ThriveGlobal.$j( this ).attr( 'data-controls' ) ) === 0
//						     ) || (
//							     parseInt( ThriveGlobal.$j( this ).attr( 'data-controls' ) ) === 0 && isSafari
//						     ) || isChrome ) {
//							var video = ThriveGlobal.$j( this ).find( 'video' );
//							if ( video.prop( 'paused' ) ) {
//								video[0].play();
//							} else {
//								video[0].pause();
//							}
//						}
//					}
//				}
//				if ( record_click ) {
//					$this.data( 'tve-video-clicked', true );
//				}
			} );
		} else {
			try {
				//fix for Socrates 4.0 theme conflict
				ThriveGlobal.$j( 'html' ).getNiceScroll().remove();
			} catch ( error ) {
			}

		}

		function tcb_start_wistia_video( thisItem ) {
			var wistiaPopoverFrame = thisItem.find( '.tve_wistia_container' ),
				startTime = wistiaPopoverFrame.attr( "data-start-time" ),
				videoId = wistiaPopoverFrame.attr( "data-video-id" ),
				arr = {};
			window._wq = window._wq || [];
			arr['wistia-' + videoId + "-1"] = function ( video ) { //'-1' means that the first instance of the same wistia popover video will be started
				video.time( startTime );
				video.play();
				/* on mobile devices, we cannot auto-start the video, so we just show it at this point */
				video.popover.show();
			};
			_wq.push( arr );
		}

		TCB_Front.changeAutoplayVideo();

		TCB_Front.postGridLayout();

		/**
		 * SUPP-1229 on pages with multiple fonts, this will not work correctly if it's just called on domready
		 */
		TCB_Front.$window.on( 'load', function () {
			TCB_Front.postGridLayout();
		} );

		/**
		 * social sharing buttons
		 */
		if ( window.FB ) {
			setTimeout( function () {
				ThriveGlobal.$j( '.thrv_social_default .tve_s_fb_share, .thrv_social_default .tve_s_fb_like' ).each( function () {
					FB.XFBML.parse( this );
				} );
			}, 200 );
		}

		TCB_Front.$window.on( 'scroll', function () {
			//set this timeout for typefocues that come from hidden forms like greedy ribbon
			//and initiate the typefocus after the forms are animated
			var scrollPosition = ThriveGlobal.$j( document ).scrollTop();
			TCB_Front.show_data_elemements( scrollPosition );
			try {
				ThriveGlobal.$j( ".tve_typefocus" ).each( function () {
					var $this = ThriveGlobal.$j( this ),
						wHeight = TCB_Front.$window.height();

					if ( ! $this.data( 'typed' ) ) {
						return;
					}

					if ( (
						     (
							     scrollPosition + wHeight >= $this.offset().top
						     ) && (
							     $this.offset().top > scrollPosition
						     )
					     ) || (
						     $this.attr( 'data-typefocus' ) == 'start'
					     ) ) {
						$this.typed( 'start' );
					} else if ( $this.attr( 'data-typefocus' ) == 'pause' ) {
						$this.typed( 'pause' );
					} else {
						$this.typed( 'pause' );
					}
				} );
			} catch ( e ) {
				console.log( e );
			}
		} );

		TCB_Front.$window.trigger( 'scroll' );

		if ( ! tve_frontend_options.is_editor_page ) {
			/* Load recaptcha script */
			var $recaptcha = ThriveGlobal.$j( '.tve-captcha-container' );
			if ( $recaptcha.length ) {
				function reCaptchaLoaded() {
					$recaptcha.filter( ':not(.tve-recapcha-rendered)' ).each( function () {
						var $this = ThriveGlobal.$j( this ),
							size = (
								window.innerWidth < 400
							) ? 'compact' : $this.attr( 'data-size' );
						/* For small displays, we show the compact size captcha */
						$this.addClass( 'tve-recaptcha-rendered' );
						grecaptcha.render( this.id, {
							"sitekey": $this.attr( 'data-site-key' ),
							"theme": $this.attr( 'data-theme' ),
							"type": $this.attr( 'data-type' ),
							"size": size
						} );
					} );
				}

				function checkCaptchaLoaded() {
					if ( typeof grecaptcha === 'undefined' ) {
						setTimeout( checkCaptchaLoaded, 50 );
					} else {
						reCaptchaLoaded();
					}

				}

				var loading_script = false;
				if ( ! window.tve_gapi_loaded ) {
					ThriveGlobal.$j.getScript( 'https://www.google.com/recaptcha/api.js?render=explicit', checkCaptchaLoaded );
					loading_script = true;
					window.tve_gapi_loaded = true;
				}
				if ( ! loading_script ) {
					checkCaptchaLoaded();
				}
			}

			/* Load Facebook Script */
			var $fb_comments = ThriveGlobal.$j( '.tve-fb-comments' );
			if ( $fb_comments.length ) {
				if ( ThriveGlobal.$j( '#fb-root' ).length ) {
					ThriveGlobal.$j( 'head' ).append( '<div id="fb-root"></div>' );
				}

				$fb_comments.each( function () {
					if ( ThriveGlobal.$j( this ).attr( 'data-href' ) === '' ) {
						ThriveGlobal.$j( this ).attr( 'data-href', window.location );
					}
					ThriveGlobal.$j( this ).addClass( 'fb-comments' );
					FB.XFBML.parse( this.parentNode );
				} );
			}

			/* Load Disqus Comment Script */
			var $disqus_comments = ThriveGlobal.$j( '.thrv_disqus_comments #disqus_thread' );
			if ( $disqus_comments.length ) {
				window.disqus_shortname = $disqus_comments.attr( 'data-disqus_shortname' );
				if ( $disqus_comments.attr( 'data-disqus_url' ) == '' ) {
					window.disqus_url = window.location;
				} else {
					window.disqus_url = $disqus_comments.attr( 'data-disqus_url' );
				}
				window.disqus_identifier = window.disqus_url;
				if ( typeof DISQUS === 'undefined' ) {
					ThriveGlobal.$j.getScript( '//' + disqus_shortname + '.disqus.com/embed.js' );
				}
			}

		}
		/* mediaelement video shortcodes */
		if ( window.mejs ) {
			ThriveGlobal.$j( '.tcb-video-shortcode' ).not( '.mejs-container' )
				.filter( function () {
					return ! ThriveGlobal.$j( this ).parent().hasClass( '.mejs-mediaelement' );
				} )
				.mediaelementplayer();
		}

		this.resizePageSection();

	},
	/**
	 * Page section width
	 */
	resizePageSection: function ( $element ) {
		if ( typeof $element === 'undefined' ) {
			$element = ThriveGlobal.$j( '.tcb-window-width' );
		}
		$element.each( function () {
			var $this = ThriveGlobal.$j( this ),
				content_width = $this.parent().width(),
				$inside = $this.find( '.tve-page-section-in' ),
				initial_left = Number( $this.css( ['left'] ).left.replace( 'px', '' ) );

			$this.css( {
				width: TCB_Front.$window.width() + 'px',
				left: (- $this.offset().left + initial_left) + 'px'
			} );

//			if ( ! $inside.hasClass( 'tve-section-content-stretched' ) ) {
//				$inside.css( {
//					'max-width': content_width + 'px'
//				} );
//			}
		} );

	},
	/**
	 * handle onclick events on the social elements having custom designs
	 */
	onSocialCustomClick: {
		/**
		 * open modal window
		 *
		 * @param url string url to open
		 * @param w int width of the window
		 * @param h int height of the window
		 */
		wnd: function ( url, w, h ) {

			// Fixes dual-screen position                         Most browsers      Firefox
			var dualScreenLeft = typeof window.screenLeft !== 'undefined' ? window.screenLeft : screen.left,
				dualScreenTop = typeof window.screenTop !== 'undefined' ? window.screenTop : screen.top,
				width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
				height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

			var left = (
				           (
					           width / 2
				           ) - (
					           w / 2
				           )
			           ) + dualScreenLeft;
			var top = (
				          (
					          height / 2
				          ) - (
					          h / 2
				          )
			          ) + dualScreenTop;
			var newWindow = window.open( url, 'Thrive Share', 'scrollbars=yes,resizable=yes,toolbar=no,menubar=no,scrollbars=no,location=no,directories=no,width=' + w + ', height=' + h + ', top=' + top + ', left=' + left );

			// Puts focus on the newWindow
			if ( window.focus ) {
				newWindow.focus();
			}

			return newWindow;

		},
		fb_share: function ( $element ) {
			var config = $element.data();
			if ( ! config.type || config.type == 'feed' ) {


				var queryString = '';
				if ( config.name ) {
					queryString += '&title=' + encodeURIComponent( config.name );
				}

				if ( config.description ) {
					queryString += '&description=' + encodeURIComponent( config.description );
				}

				if ( config.href ) {
					queryString += '&u=' + encodeURIComponent( config.href );
				}

				if ( config.caption ) {
					queryString += '&caption=' + encodeURIComponent( config.caption );
				}

				if ( config.image ) {
					queryString += '&picture=' + encodeURIComponent( config.image );
				}

				//Replace First & with ?
				queryString = '?' + queryString.substr( 1 );

				this.wnd( 'https://www.facebook.com/sharer.php' + queryString, 650, 500 );

//TODO: This can be removed
//				// Feed dialog opened via FB JS SDK
//				FB.ui( {
//					method: 'feed',
//					link: config.href,
//					caption: config.caption,
//					name: config.name,
//					description: config.description,
//					picture: config.image
//				}, function ( response ) {
//				} );


			} else {
				this.wnd( 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent( config.href ), 650, 500 );
			}
			return false;
		},
		g_share: function ( $element ) {
			var config = $element.data();
			this.wnd( 'https://plus.google.com/share?url=' + encodeURIComponent( config.href ), 600, 600 );
		},
		t_share: function ( $element ) {
			var config = $element.data();
			this.wnd( 'https://twitter.com/intent/tweet?url=' + encodeURIComponent( config.href ) +
			          (
				          config.tweet ? '&text=' + encodeURIComponent( config.tweet ) : ''
			          ) +
			          (
				          config.via ? '&via=' + config.via : ''
			          ), 550, 450 );
		},
		in_share: function ( $element ) {
			var config = $element.data();
			this.wnd( 'https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent( config.href ), 550, 400 );
		},
		pin_share: function ( $element ) {
			var config = $element.data();
			this.wnd( 'https://pinterest.com/pin/create/button/?url=' + encodeURIComponent( config.href ) +
			          (
				          config.media ? '&media=' + encodeURIComponent( config.media ) : ''
			          ) +
			          (
				          config.description ? '&description=' + encodeURIComponent( config.description ) : ''
			          ), 600, 600 );
		},
		xing_share: function ( $element ) {
			var config = $element.data();
			this.wnd( 'https://www.xing.com/spi/shares/new?url=' + encodeURIComponent( config.href ), 600, 500 );
		}
	},
	getShareCounts: function ( $root, ajax_data, return_data ) {
		$root = $root || ThriveGlobal.$j( 'body' );
		var $elements = $root.find( '.tve_social_items.tve_social_custom' );
		if ( ! $elements.length ) {
			return;
		}
		var data = {
			action: 'tve_social_count',
			for: []
		};

		if ( typeof ajax_data !== 'undefined' ) {
			ThriveGlobal.$j.each( ajax_data, function ( k, v ) {
				data[k] = v;
			} );
		} else if ( typeof tve_path_params !== 'undefined' && tve_path_params.post_id ) {
			data.post_id = tve_path_params.post_id;
		}
		$elements.each( function ( index ) {
			var $this = ThriveGlobal.$j( this ),
				networks = {};
			if ( $this.data( 'tve-social-counts' ) || (
					! $this.hasClass( 'tve_social_cb' ) && $this.parent().attr( 'data-counts' ) !== '1'
				) ) {
				$this.data( 'tve-social-counts', 1 );
				return;
			}
			$this.data( 'tve-social-counts', 1 );

			var $count = $this.prev( '.tve_s_share_count' );
			var $items = $this.children( '.tve_s_item' ).each( function () {
				var $element = ThriveGlobal.$j( this ).addClass( 'tve_count_loading' );
				networks[$element.attr( 'data-s' )] = $element.attr( 'data-href' );
			} );
			/**
			 * if only twitter is selected, we hide the total number of shares, and skip one ajax call
			 */
			if ( networks.hasOwnProperty( 't_share' ) && $items.length === 1 ) {
				$items.removeClass( 'tve_count_loading' );
				$count.remove();
				$this.parent().removeAttr( 'data-counts' );
				return;
			}
			data.for[index] = networks;
		} );
		if ( ! data.for.length ) {
			return null;
		}
		TCB_Front.total_share_counts = {};
		var _r = function ( response ) {
			if ( ! response || ! response.counts || ! response.totals ) {
				return;
			}
			$elements.each( function ( index ) {
				var $this = ThriveGlobal.$j( this ),
					$count = $this.prev( '.tve_s_share_count' ),
					$items = $this.children( '.tve_s_item' );
				ThriveGlobal.$j.each( response.counts[index], function ( network, data ) {
					$items.filter( '.tve_s_' + network ).find( '.tve_s_count' ).html( data.formatted );
				} );
				$items.removeClass( 'tve_count_loading' );
				if ( response.totals && response.totals[index] && $this.parent().attr( 'data-counts' ) === '1' ) {
					var min_shares = parseInt( $this.parent().attr( 'data-min_shares' ) );
					min_shares = isNaN( min_shares ) ? 0 : min_shares;
					$count.find( '.tve_s_cnt' ).html( response.totals[index].formatted );
					$count[min_shares <= response.totals[index].value ? 'show' : 'hide']();
				}
				if ( response.totals && response.totals[index] ) {
					TCB_Front.total_share_counts[$items.attr( 'data-href' )] = response.totals[index];
				}
			} );
		};
		if ( typeof return_data !== 'undefined' && return_data ) {
			return [data, _r];
		}

		ThriveGlobal.$j.ajax( {
			type: 'post',
			xhrFields: {
				withCredentials: true
			},
			url: tve_frontend_options.ajaxurl,
			data: data,
			dataType: 'json'
		} ).done( _r );
	}
};

/**
 * ThriveGlobal.$j extensions
 * @returns {*}
 */
TVE_jQFn.tve_front_tw_qs = function () {
	return this.each( function () {
		var $this = ThriveGlobal.$j( this );
		if ( $this.data( 'tve_front_tw_qs_done' ) ) {
			return this;
		}
		$this.data( 'tve_front_tw_qs_done', true );

		$this.click( function () {
			window.open( getUrl(), '_blank' );
		} );

		var getUrl = function () {
			var has_custom_url = $this.attr( 'data-use_custom_url' ) && $this.attr( 'data-custom_url' ),
				page_url = has_custom_url ? $this.attr( 'data-custom_url' ) : window.location.href,
				url = $this.data( 'url' ) + '?text=' + encodeURIComponent( $this.find( 'p' ).text() ) + '&url=' + encodeURIComponent( page_url );
			if ( $this.data( 'via' ).length > 0 ) {
				url += '&via=' + $this.data( 'via' );
			}
			return url;
		}
	} );
};

TVE_jQFn.tve_front_lead_generation = function () {
	return this.each( function () {
		var $this = ThriveGlobal.$j( this ),
			$form = $this.find( 'form' ),
			_action = $form.find( '.tve-f-a-hidden' ).val();

		if ( $form.length == 0 ) {
			$this.find( '.thrv_lead_generation_container' ).wrapInner( '<form method="post"></form>' );
			$form = $this.find( 'form' );
		}

		if ( typeof _action !== 'undefined' && $form.attr( 'action' ) == '#' ) {
			$form.attr( 'action', _action );
		}

		if ( $this.data( 'tve_lg_done' ) ) {
			return this;
		}
		$this.data( 'tve_lg_done', true );

		/**
		 * SegMet will call the default form.submit() action cancelling all events that were added with jquery
		 * we need to overpass that by overriding the default submit() function for this form
		 */
		if ( typeof window.SegMet !== 'undefined' && SegMet && $form[0].action.indexOf( "infusionsoft" ) !== - 1 && ! $form.data( 'tve-segmet-submit' ) ) {
			$form.data( 'tve-segmet-submit', 1 );
			var oSubmitFn = $form[0].submit;
			$form[0].submit = function () {
				if ( $form.data( 'tve-segmet-submitted' ) ) {
					return oSubmitFn.call( $form[0] );
				}
				$form.data( 'tve-segmet-submitted', 1 );
				return $form.submit();
			};
		}

		var validationManager = {
			errClass: 'tve-lg-error',
			init: function () {
				this.container = ThriveGlobal.$j( '#tve-lg-error-container' );
				if ( ! this.container.length ) {
					this.container = ThriveGlobal.$j( '<div id="tve-lg-error-container"></div>' ).appendTo( 'body' );
					this.container.on( 'click', '.tve-lg-err-close', ThriveGlobal.$j.proxy( this.close, this ) );
				}
				this.container.empty().hide();
				this.clear();
			},
			close: function () {
				this.container.fadeOut( 200 );
				/**
				 * focus the first input element
				 */
				var _focus = $form.find( '.' + this.errClass );
				if ( ! _focus.length ) {
					_focus = $form.find( 'input,select,textarea' );
				}

				_focus.first().focus();
				return false;
			},
			clear: function () {
				$form.find( 'input,select,textarea' ).removeClass( this.errClass );
			},
			markApiError: function ( error ) {
				this.container.append( '<div class="tve-lg-err-item tve-lg-ext-err">' + error + '</div>' );
				return this;
			},
			_markError: function ( $formElement, error, error_type ) {

				$formElement.addClass( this.errClass );
				if ( error_type !== 'required' || ! this.container.find( '.tve-lg-required' ).length ) { // show just one "required" - type error
					this.container.append( '<div class="tve-lg-err-item tve-lg-' + error_type + '">' + error + '</div>' );
				}
			},
			show: function () {
				/* show the remove icon */
				this.container.append( '<a href="javascript:void(0)" class="tve-lg-err-close" title="Close"><span class="thrv-icon thrv-icon-cross"></span></a>' );
				var self = this;
				setTimeout( function () {
					var form_position = $form.offset(),
						mt = parseInt( $form.css( 'margin-top' ) ),
						ml = parseInt( $form.css( 'margin-left' ) ),
						top = (
							form_position.top - 10 - self.container.outerHeight( true ) - (
								isNaN( mt ) ? 0 : mt
							)
						);

					if ( $form.parents( '.thrv-ribbon' ).length ) {
						if ( $form.parents( '.tve-leads-ribbon' ).attr( 'data-position' ) == 'bottom' ) {
							top += 7;
						} else {
							top = form_position.top + $form.outerHeight();
						}
					}

					self.container.css( {
						top: top + 'px',
						left: (
							form_position.left - (
								isNaN( ml ) ? 0 : ml
							)
						),
						width: $form.outerWidth()
					} ).fadeIn( 200 );
					/**
					 * uncomment this to hide the error messages on scroll
					 * ThriveGlobal.$j(window).off('scroll.lg-close').on('scroll.lg-close', ThriveGlobal.$j.proxy(self.close, self));
					 *
					 */
				}, 50 );

			},
			phone: function ( $input, error ) {

				/**
				 * these formats are supported
				 * (123) 456-7890
				 * 123-456-7890
				 * 123.456.7890
				 * 1234567890
				 * +31636363634
				 * 075-63546725
				 */
				if ( $input.val().replace( /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im, "" ).length ) {
					this._markError( $input, error, 'phone' );
					return false;
				}
				return true;
			},
			required: function ( $input, error ) {
				var valid = true;
				if ( $input.attr( 'type' ) === 'radio' ) {
					valid = ThriveGlobal.$j( 'input[name="' + $input.attr( 'name' ) + '"]' ).is( ':checked' );
				} else if ( $input.attr( 'type' ) === 'checkbox' ) {
					valid = $input.is( ':checked' );
				} else {
					valid = ThriveGlobal.$j.trim( $input.val() ).length > 0;
				}

				if ( ! valid ) {
					this._markError( $input, error, 'required' );
					return false;
				}
				return true;
			},
			email: function ( $input, error ) {
				var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				if ( ! re.test( $input.val() ) ) {
					this._markError( $input, error, 'email' );
					return false;
				}
				return true;
			},
			getErrors: function ( $element ) {
				try {
					return JSON.parse( $element.find( '.tve-lg-err-msg' ).val() );
				} catch ( e ) {
					return {
						required: 'Please fill in all of the required fields',
						phone: 'The phone number is not valid',
						email: 'The email address is not valid'
					};
				}
			}
		};

		var applyFormTarget = function () {
			if ( $this.data( 'form-target' ) && $this.data( 'form-target' ).length ) {
				$this.find( 'form' ).attr( 'target', $this.data( 'form-target' ) );
			}
		};

		var showMessages = function ( success, msg ) {

			var messages = ThriveGlobal.$j.extend( {
					success: 'Success!',
					error: 'Error!'
				}, msg ),
				_form_type = $form.find( '#_form_type' ).val(),
				name = $form.find( 'input[name=name]' ).val() ? $form.find( 'input[name=name]' ).val() : '',
				email = $form.find( 'input[name=email]' ).val();

			if ( success ) {
				$form.parents( '.tl-style' ).first().find( '.tve_p_lb_close' ).trigger( "click" );
				var leads_messages = ThriveGlobal.$j.Event( 'leads_messages.tcb' );
				$form.trigger( leads_messages );

				if ( _form_type == 'lead_generation' ) {
					$form.find( 'input' ).val( '' );
					$form.parents( '.tve_p_lb_content' ).find( '.tve_p_lb_close' ).click();
				}

				messages.success = messages.success.replace( '[lead_email]', email );
				messages.success = messages.success.replace( '[lead_name]', name );
				jQuery( 'body' ).slideDown( "fast", function () {
					jQuery( 'body' ).prepend( '<div class="tvd-toast tve-fe-message"><div class="tve-toast-message"><div class="tve-toast-icon-container"><span class="tve_tick thrv-icon-checkmark"></span></div><div class="tve-toast-message-container">' + messages.success + '</div></div></div>' );
				} );

				setTimeout( function () {
					jQuery( '.tvd-toast' ).hide();
				}, 6000 );

			} else {

				var container = $form.parent(),
					wrapper = container.parent(),
					$fail = wrapper.find( '.tve-error-wrapper' ),
					_show_error = $form.find( '#_error_message_option' ).val();
				if ( _show_error == 1 ) {
					container.hide();
					if ( $fail.length == 0 ) {
						wrapper.append( '<div class="tve-error-wrapper"><div class="tve-error-content"></div><button class="tve-close-error-message">Retry</button></div>' );
						messages.error = messages.error.replace( '[lead_email]', email );
						messages.error = messages.error.replace( '[lead_name]', name );
						wrapper.find( '.tve-error-content' ).append( messages.error );
					} else {
						wrapper.find( '.tve-error-wrapper' ).show();
					}
				} else {
					location.reload();
				}

			}

			$form.tve_form_loading( true );
		};

		var changeState = function ( content ) {
			var leads_states = ThriveGlobal.$j.Event( 'leads_states.tcb' );
			$form.trigger( leads_states, content );

			if ( leads_states.change_states ) {
				ThriveGlobal.$j( ".thrv_lead_generation" ).tve_front_lead_generation();
			}
			$form.tve_form_loading( true );

		};

		var applyValidation = function () {

			var errors = validationManager.getErrors( $this );
			$form.submit( function ( event ) {
				if ( $form.data( 'tve-force-submit' ) ) {
					return true;
				}
				var _valid = true,
					_form = ThriveGlobal.$j( this ),
					password = {};

				validationManager.init();

				_form.find( 'input, select, textarea' ).each( function () {
					var _input = ThriveGlobal.$j( this ),
						_validationType = _input.data( 'validation' ),
						_required = _input.data( 'required' );


					/**
					 * if the user is on an iphone we move the placeholder to the input value
					 * so that the user can scroll.
					 * if the iphone-placeholder is set on the input and
					 * if the value is the same as the placeholder
					 * don't submit the form.
					 */
					var iphoneDataPlaceholder = _input.data( 'iphone-placeholder' );
					if ( iphoneDataPlaceholder !== '' && _input.val() === iphoneDataPlaceholder ) {
						_input.val( '' );
					}

					if ( _required === 1 ) {
						if ( ! validationManager.required( _input, errors['required'] ) ) {
							_valid = false;
						}
					}

					if ( typeof _validationType !== 'undefined' && _validationType !== 'none' && validationManager[_validationType] ) {
						if ( ! validationManager[_validationType]( _input, errors[_validationType] ) ) {
							_valid = false;
						}
					}

				} );

				if ( _form.find( '#g-recaptcha-response' ).length > 0 ) {
					if ( _form.find( '#g-recaptcha-response' ).val() == '' ) {
						validationManager.markApiError( 'Please validate captcha form' );
						_valid = false;
					}
				}

				if ( Object.keys( password ).length == 2 ) {
					if ( ! validationManager.mismatch( password, errors.passwordmismatch ) ) {
						_valid = false;
					}
				}

				if ( ! _valid ) {
					validationManager.show();
					$form.find( '.' + validationManager.errClass ).first().focus();
					return false;
				}
				$form.tve_form_loading();
				/**
				 * if this form is connected via API to emailing lists, we need to make an ajax call to the server and send out this subscription
				 */
				if ( $form.parents( '.thrv_lead_generation' ).attr( 'data-connection' ) == 'api' ) {
					var data = $form.serialize() + '&action=tve_api_form_submit' + "&url=" + encodeURIComponent( location.href ),
						_option = $form.find( '#_submit_option' ).val();

					var conversion_event = ThriveGlobal.$j.Event( 'form_conversion.tcb' );
					$form.trigger( conversion_event );

					if ( conversion_event.post_data ) {
						data += '&' + ThriveGlobal.$j.param( conversion_event.post_data );
					}

					ThriveGlobal.$j.ajax( {
						type: 'post',
						xhrFields: {
							withCredentials: true
						},
						url: tve_frontend_options.ajaxurl,
						data: data
					} ).fail( function () {
						validationManager.markApiError( 'An error occurred while submitting your data. Please try again' ).show();
						$form.tve_form_loading( true );
					} ).done( function ( response ) {
						try {
							response = ThriveGlobal.$j.parseJSON( response );

							var messages = response.form_messages || {};
							var redirect = '';
							if ( response.redirect ) {
								redirect = response.redirect;
								delete response.redirect;
							}

							delete response.form_messages;

							if ( response.variation ) {
								var content = response.variation;
								delete response.variation;
							}

							// objects can't be checked for length so we're checking against it's keys
							var success = Object.keys( response ).length > 0 && ! response.error;

							if ( response.error ) {
								showMessages( success, messages );
								return;
							}
						} catch ( e ) {
							console.log( e )
						}

						var lead_conversion_success = ThriveGlobal.$j.Event( 'lead_conversion_success.tcb' );
						$form.trigger( lead_conversion_success );

						if ( lead_conversion_success.content_unlocked ) {
							return;
						}

						var _show_error = $form.find( '#_error_message_option' ).val();

						// redirect the user to the thank you url, or reload the current page or show the message or change the state
						if ( ! _option || _option == 'reload' && ( success || _show_error != 1 ) ) {
							location.reload();
							return;
						} else if ( _option == 'redirect' ) {

							var url = $form.find( '#_back_url' ).val();
							if ( url && TCB_Front.isValidUrl( url ) && ( success || _show_error != 1 ) ) {
								location.href = url;
								return;
							}
						} else if ( _option == 'state' && success ) {
							var trigger = $form.find( '.tve-switch-state-trigger' );
							trigger.trigger( "click" );

							if ( $form.find( 'input[name=_form_type]' ).val() != 'lightbox' && trigger.attr( 'data-tcb-events' ).indexOf( 'tl_state_lightbox' ) > 0 ) {
								/**
								 * We have a lighbox that needs to be opened so let's close the form we already have
								 */
								var leads_messages = ThriveGlobal.$j.Event( 'leads_messages.tcb' );
								leads_messages.lightbox_state = true;
								$form.trigger( leads_messages );
								$form.tve_form_loading( true );

							}
							if ( TL_Front.parent_state ) {
								$form.parents( '.tl-style' ).first().find( '.tve_p_lb_close' ).trigger( "click" );
								delete TL_Front.parent_state;
							}
							return;
						} else if ( _option == 'klicktipp-redirect' && success ) {
							if ( redirect && TCB_Front.isValidUrl( redirect ) ) {
								location.href = redirect;
								return;
							}
						}

						showMessages( success, messages );
						return true;
					} );

					event.stopPropagation();
					event.preventDefault();
					return false;
				} else {
					var shortcircuit_form = ThriveGlobal.$j.Event( 'should_submit_form.tcb' );
					$form.trigger( shortcircuit_form );
					if ( $form.find( 'input#_asset_option' ).val() !== '1' && ! shortcircuit_form.flag_need_data ) {
						return true;
					}

					/**
					 * try to detect the email field from a form and get its value
					 *
					 * @param $form
					 */
					function try_getting_email( $form ) {
						/* if there is a field which requires email validation, use that */
						if ( $form.find( '[data-validation="email"]' ).length ) {
							return $form.find( '[data-validation="email"]' ).val();
						}
						var maybe_email = '';
						/* try search for 'email' in the input name */
						$form.find( 'input' ).each( function () {
							if ( this.name && this.name.match( /email/i ) ) {
								maybe_email = this.value;
								return false;
							}
						} );

						return maybe_email;
					}

					/**
					 * try to detect the name field from a form and get its value
					 *
					 * @param $form
					 */
					function try_getting_name( $form ) {
						var found = false;
						$form.find( 'input' ).each( function () {
							var _input = ThriveGlobal.$j( this ),
								placeholder = _input.attr( 'placeholder' ),
								name = _input.attr( 'name' );
							if ( (
								     placeholder && placeholder.toLowerCase().indexOf( 'name' ) !== - 1
							     ) || (
								     name && name.toLowerCase().indexOf( 'name' ) !== - 1
							     ) ) {
								found = _input;
								return false;
							}
						} );

						return found !== false ? found.val() : '';
					}

					var data = $form.serialize() + '&action=tve_custom_form_submit&email=' + try_getting_email( $form ) + '&name=' + try_getting_name( $form );
					var conversion_event = ThriveGlobal.$j.Event( 'form_conversion.tcb' );
					$form.trigger( conversion_event );

					if ( conversion_event.post_data ) {
						data += '&' + ThriveGlobal.$j.param( conversion_event.post_data );
					}

					/**
					 * Synchronous XHR requests are deprecated - we need to make this async and, on return, force-submit the form
					 */
					ThriveGlobal.$j.ajax( {
						type: 'post',
						xhrFields: {
							withCredentials: true
						},
						url: tve_frontend_options.ajaxurl,
						data: data
					} ).always( function ( response ) {
						$form.find( 'input,select,textarea' ).removeAttr( 'disabled' );
						$form.data( 'tve-force-submit', true ).submit();
					} );

					event.stopPropagation();

					return false;
				}

			} );
		};

		applyFormTarget();
		if ( $this.find( ".tve-lg-err-msg" ).length ) {
			applyValidation();
		}
	} );
};
TVE_jQFn.tve_form_loading = function ( remove_loader ) {
	var $form = this,
		$container = $form.find( 'button[type=submit]' ).parent(),
		$loader = $form.find( '.tcb-form-loader' );

	if ( typeof remove_loader !== 'undefined' && remove_loader ) {
		$loader.fadeOut();
		$form.find( 'input,select,textarea' ).removeAttr( 'disabled' );
		$container.find( 'button' ).fadeIn();
		return this;
	}

	if ( ! $loader.length ) {
		$loader = ThriveGlobal.$j( '<div class="tcb-form-loader"><span class="tcb-form-loader-icon thrv-icon-spinner9"></span></div>' ).appendTo( $container );
		$container.css( {
			position: 'relative',
			width: $container.width() + 'px',
			height: $container.height() + 'px'
		} );
	}

	$container.find( 'button' ).hide();
	$loader.show();

	return this;
};
TVE_jQFn.thrive_tcb_timer = function () {
	return this.each( function () {
		var el = ThriveGlobal.$j( this ),
			now = new Date(),
			event_date = new Date( el.attr( 'data-date' ) + 'T' + el.attr( 'data-hour' ) + ':' + el.attr( 'data-min' ) + ':' + (el.attr( 'data-sec' ) || '00') + el.attr( 'data-timezone' ) ),
			day = 0, hour = 0, min = 0, sec = 0, day_digits = 2,
			message = el.attr( 'data-text' ),
			interval_id,
			is_evergreen = el.hasClass( 'thrv-countdown_timer_evergreen' ),
			is_norestart = el.attr( 'data-norestart' ),
			_data_day = parseInt( el.attr( 'data-day' ) ),
			_data_hour = parseInt( el.attr( 'data-hour' ) ),
			_data_min = parseInt( el.attr( 'data-min' ) ),
			_data_sec = parseInt( el.attr( 'data-sec' ) );

		if ( el.data( 'tcb_timer_done' ) ) {
			return this;
		}
		el.data( 'tcb_timer_done', true );

		if ( is_evergreen ) {
			event_date = new Date();

			// check if cookie exists
			var _cookie = TCB_Front.getCookie( el.attr( 'data-id' ) );
			// if we don't have a cookie set the timer to start from the data we already have and create the cookie
			if ( ! _cookie ) {
				event_date.setTime( now.getTime() + _data_day * 24 * 3600 * 1000 + _data_hour * 3600 * 1000 + _data_min * 60 * 1000 + _data_sec * 1000 );
				var _value = event_date.getFullYear() + '-' + (
				             event_date.getMonth() + 1
					) + '-' + event_date.getDate() + '-' + event_date.getHours() + '-' + event_date.getMinutes() + '-' + event_date.getSeconds();

				/**
				 * Start Again Functionality:
				 * If norestatr is 0, the cookie expire formula is event date + Start Again parameters (days and hours)
				 */
				var _cookie_expire_date = new Date( '2034-01-01' );
				if ( ! parseInt( is_norestart ) ) {
					var cpy_event_date = new Date( event_date.getTime() );
					_cookie_expire_date = cpy_event_date.setDate( event_date.getDate() + parseInt( el.attr( 'data-expday' ) ) );
					_cookie_expire_date = cpy_event_date.setHours( event_date.getHours() + parseInt( el.attr( 'data-exphour' ) ) );
					_cookie_expire_date = new Date( _cookie_expire_date );
				}

				TCB_Front.setCookie( el.attr( 'data-id' ), _value, {expires: _cookie_expire_date} );
			} else { //if cookie exists create a new date from it and pass it
				_cookie = _cookie.split( "-" );
				var exp_date = new Date( _cookie[0], _cookie[1] - 1, _cookie[2], _cookie[3], _cookie[4], _cookie[5] )
				event_date.setTime( exp_date );
			}

		}

		/* utility functions */
		/**
		 * setup html <span>s to hold each of the digits making up seconds, minutes, hours, days
		 *
		 * check the number of digits required for days (this might be bigger than 2)
		 */
		var htmlSetup = function () {
			/**
			 * create a new span containing the value
			 *
			 * @param index
			 * @param value
			 * @returns {ThriveGlobal.$j|ThriveGlobal.$j}
			 * @private
			 */
			var _span = function ( index, value ) {
				return ThriveGlobal.$j( '<span class="part-p ct-d-placeholder">&nbsp;</span><span class="part-' + index + '">' + value + '</span>' );
			};

			el.find( '.tve_t_sec .t-digits' ).empty().append( _span( 2, Math.floor( sec / 10 ) ) ).append( _span( 1, sec % 10 ) );
			el.find( '.tve_t_min .t-digits' ).empty().append( _span( 2, Math.floor( min / 10 ) ) ).append( _span( 1, min % 10 ) );
			el.find( '.tve_t_hour .t-digits' ).empty().append( _span( 2, Math.floor( hour / 10 ) ) ).append( _span( 1, hour % 10 ) );

			var $dayContainer = el.find( '.tve_t_day .t-digits' ).empty(),
				total_days = day;
			for ( var i = 1; i <= day_digits; i ++ ) {
				$dayContainer.append( _span( i, total_days % 10 ) );
				total_days = Math.floor( total_days / 10 );
			}

			$dayContainer.css( 'min-width', '' );
		};

		/**
		 * reposition and resize the day container
		 * used when the timer is not visible at first on the page
		 */
		function reposition() {
			setTimeout( function () {
				el.find( '.tve_t_day .t-digits' ).css( 'min-width', ( el.find( '.tve_t_sec .t-digits > span' ).outerWidth() * day_digits ) + 'px' )
			}, 10 );
		}

		/**
		 * when used inside a content reveal or a lightbox, we need to recalculate the day section width
		 */
		el.parents( '.thrv_content_reveal' ).on( 'tve-content-revealed', reposition );
		el.parents( '.tve_p_lb_content' ).on( 'tve.before-lightbox-open', reposition );

		/**
		 * if value is the same as current value, do nothing, else, we need to create animation
		 *
		 * @param $part
		 * @param value
		 */
		var setValue = function ( $part, value ) {
			if ( $part.html() == value ) {
				return $part;
			}
			$part.removeClass( 'next' );
			//create another span, and insert it before the original part, in order to animate it nicely
			var _new = $part.clone().removeClass( 'go-down' ).addClass( 'next' ).html( value );
			$part.before( _new ).next( '.go-down' ).remove();
			$part.addClass( 'go-down' );
			setTimeout( function () {
				_new.addClass( 'go-down' );
			}, 20 );

			return $part;
		};

		/**
		 * set each of the new values on a group (seconds, minutes, hours, days)
		 * @param container
		 * @param value
		 * @param number_length
		 */
		var setValues = function ( container, value, number_length ) {

			if ( typeof number_length === 'undefined' ) {
				number_length = false;
			}
			var index = 0;
			if ( value <= 99 ) {
				setValue( container.find( '.part-1' ).first(), value % 10 );
				setValue( container.find( '.part-2' ).first(), Math.floor( value / 10 ) );
				index = 2;
			} else {
				while ( value ) {
					index ++;
					setValue( container.find( '.part-' + index ).first(), value % 10 );
					value = Math.floor( value / 10 );
				}
			}
			if ( number_length !== false && index < number_length ) {
				for ( var i = index + 1; i <= number_length; i ++ ) {
					setValue( container.find( '.part-' + i ).first(), 0 );
				}
			}
		};

		/**
		 * called every second, it decrements the time and updates the HTML accordingly
		 */
		var step = function () {
			sec --;

			if ( sec < 0 ) {
				sec = 59;
				min --;
			}
			if ( min < 0 ) {
				min = 59;
				hour --;
			}
			if ( hour < 0 ) {
				hour = 23;
				day --;
			}
			setValues( el.find( '.tve_t_sec .t-digits' ), sec );
			setValues( el.find( '.tve_t_min .t-digits' ), min );
			setValues( el.find( '.tve_t_hour .t-digits' ), hour );
			setValues( el.find( '.tve_t_day .t-digits' ), day, day_digits );

			if ( day <= 0 && hour <= 0 && min <= 0 && sec <= 0 ) {
				day = hour = min = sec = 0;
				//done!
				clearInterval( interval_id );
				finished();
			}
		};

		/**
		 * finished counting, or the event time is somewhere in the past
		 */
		var finished = function () {
			el.addClass( 'tve_cd_expired' );
			el.find( '.t-digits span' ).html( '0' );
			if ( message ) {
				el.find( '.tve_t_part' ).addClass( 'ct_finished' );
				el.find( '.tve_t_text' ).html( message ).fadeIn( 200 );
			}
			/** throw an event to be catch by others when the countdown is finished */
			var event = ThriveGlobal.$j.Event( 'tve.countdown-finished' );
			el.trigger( event );
		};

		if ( ! is_evergreen && now >= event_date ) {
			finished();
		} else {
			sec = Math.floor( (
				                  event_date.getTime() - now.getTime()
			                  ) / 1000 );
			min = Math.floor( sec / 60 );
			sec = sec % 60;
			hour = Math.floor( min / 60 );
			min = min % 60;
			day = Math.floor( hour / 24 );
			hour = hour % 24;
			if ( day > 99 ) {
				day_digits = day.toString().length;
			}

			htmlSetup();

			el.addClass( 'init_done' );
			// setup the interval function
			interval_id = setInterval( step, 1000 );
		}

		if ( is_evergreen && now >= event_date ) {
			clearInterval( interval_id );
			finished();
		}
	} );
};
/**
 * if an input has a placeholder set, safari on iphones does some repainting
 * that breaks the scroll of the page
 *
 * moving the placeholder text in the input value
 */
TVE_jQFn.thrive_iphone_placeholder = function () {
	var iOS = /iPad|iPhone|iPod/.test( navigator.userAgent ) && ! window.MSStream;
	if ( iOS === true ) {
		return this.each( function () {
			var $input = ThriveGlobal.$j( this ),
				inputValue = $input.attr( 'placeholder' );
			if ( $input.attr( 'type' ) === 'password' ) {
				return this;
			}
			if ( $input.data( 'iphone-placeholder' ) ) {
				return this;
			}
			$input.attr( 'placeholder', '' ).val( inputValue );
			$input.on( 'focus', function () {
				if ( $input.val() === inputValue ) {
					$input.val( '' );
				}
			} ).on( 'blur', function () {
				if ( $input.val() === '' ) {
					$input.val( inputValue );
				}
			} ).data( 'iphone-placeholder', inputValue );
		} );
	}
};
if ( typeof ThriveGlobal !== 'undefined' ) { // SUPP-1217 TL shortcode + Divi Builder + Yoast conflict
	TCB_Front.$window = ThriveGlobal.$j( window );
	ThriveGlobal.$j( document ).ready( function () {
		/* we need to bind to the body element, in case of listeners needed inside lightboxes */
		var $event_root = ThriveGlobal.$j( tve_frontend_options.is_editor_page ? '#tve_editor' : "body" ),
			resize_callbacks = [];
		/* callbacks that should be triggered on window resize */

		$event_root.on( "click", ".tve_scT > ul li", function ( e ) {
			var $li = ThriveGlobal.$j( this ),
				$container = $li.parents( '.tve_scT' ).first(),
				$target = $container.find( '> .tve_scTC' ).eq( $li.index() ),
				active_tab = $container.find( "> ul .tve_tS" ),
				active_tab_custom_colour = active_tab.attr( "data-tve-custom-colour" ),
				target_tab_custom_colour = $li.attr( "data-tve-custom-colour" );
			/** custom colour exists for currently active tab **/
			$li.attr( "data-tve-custom-colour", (
				typeof active_tab_custom_colour !== 'undefined' && active_tab_custom_colour !== false
			) ? active_tab_custom_colour : '' );
			active_tab.attr( "data-tve-custom-colour", (
				typeof target_tab_custom_colour !== 'undefined' && target_tab_custom_colour !== false
			) ? target_tab_custom_colour : '' );

			$container.find( "> ul .tve_tS" ).removeClass( "tve_tS" );
			$container.find( "> .tve_scTC" ).hide();
			$li.addClass( 'tve_tS' );
			$target.show();
			TCB_Front.postGridLayout();
			/* reload any iframe that might be in there, this was causing issues with google maps embeds in hidden tabs */
			$target.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
				var $this = ThriveGlobal.$j( this );
				if ( $this.attr( 'data-src' ) ) {
					$this.attr( 'src', $this.attr( 'data-src' ) );
					$this.attr( 'data-src', '' );
					$this.removeClass( 'tcb-dr-done' );
				}
			} );

			//foreach others tab remove the src of all the iframes
			$container.find( '> .tve_scTC' ).each( function ( index ) {
				if ( index === $li.index() ) {
					return;
				}
				var $this = ThriveGlobal.$j( this );
				$this.find( 'iframe' ).not( '.thrv_social_default iframe' ).not( '.tcb-dr-done' ).each( function () {
					var $frame = ThriveGlobal.$j( this );
					if ( $frame.attr( 'src' ) ) {
						$frame.attr( 'data-src', $frame.attr( 'src' ) );
						$frame.attr( 'src', '' );
						$frame.addClass( 'tcb-dr-done' );
					}
				} );
			} );

		} );
		$event_root.off( 'click.tvetoggleelem' ).on( 'click.tvetoggleelem', '.tve_faqB', function () {

			var toggle_element = ThriveGlobal.$j( this ).parents( '.tve_faq' ),
				toggle_wrap = ThriveGlobal.$j( this ),
				toggle_content = ThriveGlobal.$j( toggle_wrap ).siblings( ".tve_faqC" );

			if ( toggle_content.is( ":visible" ) ) {
				toggle_content.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
					var $frame = ThriveGlobal.$j( this );
					if ( $frame.attr( 'src' ) ) {
						$frame.attr( 'data-src', $frame.attr( 'src' ) );
					}
					$frame.attr( 'src', '' );
				} );
				toggle_content.slideUp( 'fast', function () {
					toggle_content.trigger( 'lbresize' );
				} );
				toggle_element.removeClass( "tve_oFaq" );
			} else {
				toggle_content.find( 'iframe' ).not( '.thrv_social_default iframe' ).each( function () {
					var $frame = ThriveGlobal.$j( this );
					if ( $frame.attr( 'data-src' ) ) {
						$frame.attr( 'src', $frame.attr( 'data-src' ) );
					}
				} );
				toggle_content.slideDown( 'fast', function () {
					toggle_content.trigger( 'lbresize' );
				} );
				TCB_Front.postGridLayout();
				toggle_element.addClass( "tve_oFaq" );
			}
		} ).on( 'click', '.thrv_social_custom .tve_s_link', function () {
			var $element = ThriveGlobal.$j( this ).parents( '.tve_s_item' ),
				_type = $element.attr( 'data-s' );

			TCB_Front.onSocialCustomClick[_type] && TCB_Front.onSocialCustomClick[_type]( $element );
		} );

		if ( ! tve_frontend_options.is_editor_page ) {

			$event_root.on( 'mouseenter', '.tve_w_menu.tve_horizontal li', function () {
				var $list_item = ThriveGlobal.$j( this );
				if ( $list_item.parents( '.tve-m-expanded' ).length ) {
					return;
				}
				if ( ! $list_item.hasClass( 'menu-item-has-children' ) ) {
					return;
				}
				$list_item.find( '> ul' ).stop().fadeIn( 'fast' );//.end().find('> a').addClass('tve-active');
			} ).on( 'mouseleave', '.tve_w_menu.tve_horizontal li', function () {
				var $list_item = ThriveGlobal.$j( this );
				if ( $list_item.parents( '.tve-m-expanded' ).length ) {
					return;
				}
				if ( ! $list_item.hasClass( 'menu-item-has-children' ) ) {
					return;
				}
				$list_item.find( '> ul' ).stop().fadeOut( 'fast' );//.end().find('> a').removeClass('tve-active');
			} );

			/* responsive menu */
			$event_root.on( 'click', '.tve-m-trigger', function () {
				var $parent = ThriveGlobal.$j( this ).parent().find( '> ul' );
				$parent.toggleClass( 'tve-m-expanded' );
				return false;
			} );

			/* table of content */
			$event_root.on( 'click', '.thrv_contents_table a', function ( event ) {

				var $ = ThriveGlobal.$j,
					$this = $( this ),
					$title = $( $this.attr( 'href' ) );

				if ( ! $title.length ) {
					return;
				}

				$( 'html, body' ).animate( {
					scrollTop: $title.offset().top - 130
				} );
			} );


			/* event manager listeners */
			if ( typeof TVE_Event_Manager_Registered_Callbacks !== 'undefined' ) {
				TCB_Front.event_triggers( $event_root );
				/* page level events */
				if ( tve_frontend_options.page_events ) {
					ThriveGlobal.$j.each( tve_frontend_options.page_events, function ( i, v ) {
						if ( ! TVE_Event_Manager_Registered_Callbacks[v.a] ) {
							return;
						}
						ThriveGlobal.$j( document ).on( 'tve-page-event-' + v.t, function ( e, maybe_timer ) {
							var call_it = true;
							if ( v.t === 'timer' ) {
								if ( maybe_timer && v.config && v.config.t_delay !== maybe_timer ) {
									call_it = false;
								}
							}
							if ( call_it ) {
								return TVE_Event_Manager_Registered_Callbacks[v.a].call( document, v.t, v.a, v.config ? v.config : {} );
							}
							return false;
						} );
					} );
				}
			}

			function customMenuResponsive() {
				if ( ThriveGlobal.$j( '.tve-m-trigger:visible' ).length ) {
					ThriveGlobal.$j( '.tve-m-trigger' ).each( function () {
						var $parent = ThriveGlobal.$j( this ).parent().find( '> ul' );
						if ( ! $parent.data( 'tve-colors-added' ) && $parent.find( 'ul a' ).attr( 'data-tve-custom-colour' ) ) {
							var cc = $parent.find( 'ul a' ).attr( 'data-tve-custom-colour' );
							$parent.find( '> li > a' ).each( function () {
								var $this = ThriveGlobal.$j( this );
								if ( $this.attr( 'data-tve-custom-colour' ) ) {
									$this.attr( 'data-o-color', $this.attr( 'data-tve-custom-colour' ) );
									$this.attr( 'data-tve-custom-colour', cc );
								}
							} );
						}
						$parent.data( 'tve-colors-added', true );
					} );
				} else {
					ThriveGlobal.$j( '.tve-m-expanded' ).removeClass( 'tve-m-expanded' );
					ThriveGlobal.$j( '.tve-m-trigger' ).each( function () {
						var $parent = ThriveGlobal.$j( this ).parent().find( '> ul' );
						if ( ! $parent.data( 'tve-colors-added' ) && $parent.find( '> li > a' ).attr( 'data-o-color' ) ) {
							var $all = $parent.find( '> li > a' ),
								cc = $all.attr( 'data-o-color' );
							$all.attr( 'data-tve-custom-colour', cc ).removeAttr( 'data-o-color' );
						}
						$parent.data( 'tve-colors-added', false );
					} );
				}
			}

			resize_callbacks.push( TCB_Front.postGridLayout );
			resize_callbacks.push( customMenuResponsive );

			customMenuResponsive();

		}

		TCB_Front.onDOMReady();

		/**
		 * if not using a Thrive Theme, we need to handle the automatic resizing of page sections
		 */
		if ( typeof ThriveApp === 'undefined' ) {
			TCB_Front.pageSectionHeight();
			resize_callbacks.push( TCB_Front.pageSectionHeight );
		}
		resize_callbacks.push( TCB_Front.resizePageSection );

		TCB_Front.$window.resize( function () {
			ThriveGlobal.$j.each( resize_callbacks, function ( i, fn ) {
				fn.call( null );
			} );
		} );
		TCB_Front.tableSort( ThriveGlobal.$j( '.tve_make_sortable tr:last-child th' ) );
	} );
}
; // The MIT License (MIT)

// Typed.js | Copyright (c) 2014 Matt Boldt | www.mattboldt.com

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.


!function ($) {

    "use strict";

    var Typed = function (el, options) {

        // chosen element to manipulate text
        this.el = $(el);

        // options
        this.options = $.extend({}, $.fn.typed.defaults, options);

        // attribute to type into
        this.isInput = this.el.is('input');
        this.attr = this.options.attr;

        // show cursor
        this.showCursor = this.isInput ? false : this.options.showCursor;

        // text content of element
        this.elContent = this.attr ? this.el.attr(this.attr) : this.el.text()

        // html or plain text
        this.contentType = this.options.contentType;

        // typing speed
        this.typeSpeed = this.options.typeSpeed;

        // add a delay before typing starts
        this.startDelay = this.options.startDelay;

        // backspacing speed
        this.backSpeed = this.options.backSpeed;

        // amount of time to wait before backspacing
        this.backDelay = this.options.backDelay;

        // div containing strings
        this.stringsElement = this.options.stringsElement;

        // input strings of text
        this.strings = this.options.strings;

        // character number position of current string
        this.strPos = 0;

        // current array position
        this.arrayPos = 0;

        // number to stop backspacing on.
        // default 0, can change depending on how many chars
        // you want to remove at the time
        this.stopNum = 0;

        // Looping logic
        this.loop = this.options.loop;
        this.loopCount = this.options.loopCount;
        this.curLoop = 0;

        // for stopping
        this.stop = false;

        // custom cursor
        this.cursorChar = this.options.cursorChar;

        // shuffle the strings
        this.shuffle = this.options.shuffle;
        // the order of strings
        this.sequence = [];

        // All systems go!
        this.build();
    };

    Typed.prototype = {

        constructor: Typed,

        init: function (action) {
            if (typeof action === 'undefined') {
                action = 'delete';
            }
            var self = this;

            function start_it() {
                for (var i = 0; i < self.strings.length; ++i) {
                    self.sequence[i] = i;
                }

                // shuffle the array if true
                if (self.shuffle) {
                    self.sequence = self.shuffleArray(self.sequence);
                }

                if (action === 'delete') {
                    // Start deleting
                    self.strPos = self.strings[self.sequence[self.arrayPos]].length;
                    if (self.options.highlightClass) {
                        self.highlight(self.strings[self.sequence[self.arrayPos]], self.strPos);
                    } else {
                        self.backspace(self.strings[self.sequence[self.arrayPos]], self.strPos);
                    }
                } else {
                    self.strPos = 0;
                    self.typewrite(self.strings[self.sequence[self.arrayPos]], self.strPos);
                }
            }

            // begin the loop w/ first current string (global self.strings)
            // current string will be passed as an argument each time after this
            if (action === 'delete') {
                self.timeout = setTimeout(start_it, self.startDelay);
            } else {
                start_it();
            }
        },

        build: function () {
            var self = this;
            // Insert cursor
            if (this.showCursor === true) {
                this.cursor = $("<span class=\"typed-cursor\">" + this.cursorChar + "</span>");
                this.el.after(this.cursor);
            }
            if (this.stringsElement) {
                self.strings = [];
                this.stringsElement.hide();
                var strings = this.stringsElement.find('p');
                $.each(strings, function (key, value) {
                    self.strings.push($(value).html());
                });
            }

            this.init();
        },

        // pass current string state to each function, types 1 char per call
        typewrite: function (curString, curStrPos) {
            // exit when stopped
            if (this.stop === true) {
                return;
            }

            // varying values for setTimeout during typing
            // can't be global since number changes each time loop is executed
            //var humanize = Math.round(Math.random() * (100 - 30)) + this.typeSpeed;
            var humanize = this.typeSpeed;
            var self = this;
            var $el = $(this.el);

            // ------------- optional ------------- //
            // backpaces a certain string faster
            // ------------------------------------ //
            // if (self.arrayPos == 1){
            //  self.backDelay = 50;
            // }
            // else{ self.backDelay = 500; }

            // contain typing function in a timeout humanize'd delay
            self.timeout = setTimeout(function () {
                // check for an escape character before a pause value
                // format: \^\d+ .. eg: ^1000 .. should be able to print the ^ too using ^^
                // single ^ are removed from string
                var charPause = 0;
                var substr = curString.substr(curStrPos);
                if (substr.charAt(0) === '^') {
                    var skip = 1; // skip atleast 1
                    if (/^\^\d+/.test(substr)) {
                        substr = /\d+/.exec(substr)[0];
                        skip += substr.length;
                        charPause = parseInt(substr);
                    }

                    // strip out the escape character and pause value so they're not printed
                    curString = curString.substring(0, curStrPos) + curString.substring(curStrPos + skip);
                }

                if (self.contentType === 'html') {
                    // skip over html tags while typing
                    var curChar = curString.substr(curStrPos).charAt(0)
                    if (curChar === '<' || curChar === '&') {
                        var tag = '';
                        var endTag = '';
                        if (curChar === '<') {
                            endTag = '>'
                        } else {
                            endTag = ';'
                        }
                        while (curString.substr(curStrPos).charAt(0) !== endTag) {
                            tag += curString.substr(curStrPos).charAt(0);
                            curStrPos++;
                        }
                        curStrPos++;
                        tag += endTag;
                    }
                }

                // timeout for any pause after a character
                self.timeout = setTimeout(function () {
                    if (curStrPos === curString.length) {
                        // fires callback function
                        self.options.onStringTyped(self.arrayPos);

                        // is this the final string
                        if (self.arrayPos === self.strings.length - 1) {
                            // animation that occurs on the last typed string
                            self.options.callback();

                            self.curLoop++;

                            // quit if we wont loop back
                            if (self.loop === false || self.curLoop === self.loopCount)
                                return;
                        }

                        self.timeout = setTimeout(function () {
                            if (self.options.highlightClass) {
                                self.highlight(curString, curStrPos);
                            } else {
                                self.backspace(curString, curStrPos);
                            }
                        }, self.backDelay);
                    } else {

                        /* call before functions if applicable */
                        if (curStrPos === 0)
                            self.options.preStringTyped(self.arrayPos);

                        // start typing each new char into existing string
                        // curString: arg, self.el.html: original text inside element
                        var nextString = curString.substr(0, curStrPos + 1);
                        if (self.attr) {
                            self.el.attr(self.attr, nextString);
                        } else {
                            if (self.isInput) {
                                self.el.val(nextString);
                            } else if (self.contentType === 'html') {
                                self.el.html(nextString);
                            } else {
                                self.el.text(nextString);
                            }
                        }

                        // add characters one by one
                        curStrPos++;
                        // loop the function
                        self.typewrite(curString, curStrPos);
                    }
                    // end of character pause
                }, charPause);

                // humanized value for typing
            }, humanize);

        },

        highlight: function (curString, curStrPos) {
            // exit when stopped
            if (this.stop === true) {
                return;
            }

            // varying values for setTimeout during typing
            // can't be global since number changes each time loop is executed
            //var humanize = Math.round(Math.random() * (100 - 30)) + this.backSpeed;
            var humanize = this.backSpeed;
            var self = this;
            var $el = $(self.el),
                $span = $('<span/>');
            //if (self.options.highlightClass && !$el.is("." + self.options.highlightClass)) {
            //    $el.addClass(self.options.highlightClass);
            //}

            self.timeout = setTimeout(function () {

                // ----- this part is optional ----- //
                // check string array position
                // on the first string, only delete one word
                // the stopNum actually represents the amount of chars to
                // keep in the current string. In my case it's 14.
                // if (self.arrayPos == 1){
                //  self.stopNum = 14;
                // }
                //every other time, delete the whole typed string
                // else{
                //  self.stopNum = 0;
                // }

                if (self.contentType === 'html') {
                    // skip over html tags while backspacing
                    if (curString.substr(curStrPos).charAt(0) === '>') {
                        var tag = '';
                        while (curString.substr(curStrPos).charAt(0) !== '<') {
                            tag -= curString.substr(curStrPos).charAt(0);
                            curStrPos--;
                        }
                        curStrPos--;
                        tag += '<';
                    }
                }

                // ----- continue important stuff ----- //
                // replace text with base text + typed characters
                var nextString = curString.substr(0, curStrPos),
                    highlighted = curString.substr(curStrPos);

                if (self.attr) {
                    self.el.attr(self.attr, nextString);
                } else {
                    self.el.html(nextString + '<span class="' + self.options.highlightClass + '" style="' + (self.options.highlightStyle || '') + '">' + highlighted + '</span>');
                }

                // if the number (id of character in current string) is
                // less than the stop number, keep going
                if (curStrPos > self.stopNum) {
                    // subtract characters one by one
                    curStrPos--;
                    // loop the function
                    self.highlight(curString, curStrPos);
                }
                // if the stop number has been reached, increase
                // array position to next string
                else if (curStrPos <= self.stopNum) {
                    setTimeout(function () {
                        self.arrayPos++;

                        if (self.arrayPos === self.strings.length) {
                            self.arrayPos = 0;

                            self.init('write');
                        } else {
                            self.typewrite(self.strings[self.sequence[self.arrayPos]], curStrPos);
                        }
                    }, 200);
                }

                // humanized value for typing
            }, humanize);
        },

        backspace: function (curString, curStrPos) {
            // exit when stopped
            if (this.stop === true) {
                return;
            }

            // varying values for setTimeout during typing
            // can't be global since number changes each time loop is executed
            //var humanize = Math.round(Math.random() * (100 - 30)) + this.backSpeed;
            var humanize = this.backSpeed;
            var self = this;
            var $el = $(self.el);

            self.timeout = setTimeout(function () {

                // ----- this part is optional ----- //
                // check string array position
                // on the first string, only delete one word
                // the stopNum actually represents the amount of chars to
                // keep in the current string. In my case it's 14.
                // if (self.arrayPos == 1){
                //  self.stopNum = 14;
                // }
                //every other time, delete the whole typed string
                // else{
                //  self.stopNum = 0;
                // }

                if (self.contentType === 'html') {
                    // skip over html tags while backspacing
                    if (curString.substr(curStrPos).charAt(0) === '>') {
                        var tag = '';
                        while (curString.substr(curStrPos).charAt(0) !== '<') {
                            tag -= curString.substr(curStrPos).charAt(0);
                            curStrPos--;
                        }
                        curStrPos--;
                        tag += '<';
                    }
                }

                // ----- continue important stuff ----- //
                // replace text with base text + typed characters
                var nextString = curString.substr(0, curStrPos);
                if (self.attr) {
                    self.el.attr(self.attr, nextString);
                } else {
                    if (self.isInput) {
                        self.el.val(nextString);
                    } else if (self.contentType === 'html') {
                        self.el.html(nextString);
                    } else {
                        self.el.text(nextString);
                    }
                }

                // if the number (id of character in current string) is
                // less than the stop number, keep going
                if (curStrPos > self.stopNum) {
                    // subtract characters one by one
                    curStrPos--;
                    // loop the function
                    self.backspace(curString, curStrPos);
                }
                // if the stop number has been reached, increase
                // array position to next string
                else if (curStrPos <= self.stopNum) {
                    self.arrayPos++;

                    if (self.arrayPos === self.strings.length) {
                        self.arrayPos = 0;

                        // Shuffle sequence again
                        if (self.shuffle) {
                            self.sequence = self.shuffleArray(self.sequence);
                        }

                        self.init('write');
                    } else {
                        self.typewrite(self.strings[self.sequence[self.arrayPos]], curStrPos);
                    }
                    if (self.options.highlightClass && $el.is("." + self.options.highlightClass)) {
                        $el.removeClass(self.options.highlightClass);
                    }
                }

                // humanized value for typing
            }, humanize);

        },
        /**
         * Shuffles the numbers in the given array.
         * @param {Array} array
         * @returns {Array}
         */
        shuffleArray: function (array) {
            var tmp, current, top = array.length;
            if (top) while (--top) {
                current = Math.floor(Math.random() * (top + 1));
                tmp = array[current];
                array[current] = array[top];
                array[top] = tmp;
            }
            return array;
        },

        // Start & Stop currently not working

        pause: function() {
             var self = this;

             self.stop = true;
             clearInterval(self.timeout);
         },

        start: function() {
             var self = this;
             if(self.stop === false)
                return;

             this.stop = false;
             this.init();
         },

        // Reset and rebuild the element
        reset: function () {
            var self = this;
            clearInterval(self.timeout);
            var id = this.el.attr('id');
            this.el.after('<span id="' + id + '"/>')
            this.el.remove();
            if (typeof this.cursor !== 'undefined') {
                this.cursor.remove();
            }
            // Send the callback
            self.options.resetCallback();
        }

    };

    $.fn.typed = function (option) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('typed'),
                options = typeof option == 'object' && option;
            if (!data) $this.data('typed', (data = new Typed(this, options)));
            if (typeof option == 'string') data[option]();
        });
    };

    $.fn.typed.defaults = {
        strings: ["These are the default values...", "You know what you should do?", "Use your own!", "Have a great day!"],
        stringsElement: null,
        // typing speed
        typeSpeed: 0,
        // time before typing starts
        startDelay: 0,
        // backspacing speed
        backSpeed: 0,
        // shuffle the strings
        shuffle: false,
        // time before backspacing
        backDelay: 500,
        // loop
        loop: false,
        // false = infinite
        loopCount: false,
        // show cursor
        showCursor: true,
        // character for cursor
        cursorChar: "|",
        // attribute to type (null == text)
        attr: null,
        // either html or text
        contentType: 'html',
        // call when done callback function
        callback: function () {
        },
        // starting callback function before each string
        preStringTyped: function () {
        },
        //callback for every typed string
        onStringTyped: function () {
        },
        // callback for reset
        resetCallback: function () {
        }
    };


}(window.jQuery);
