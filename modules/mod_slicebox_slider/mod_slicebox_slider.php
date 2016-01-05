<?php


/**
* Module mod_slicebox_slider for Joomla 2.5/3.0
* Created by	: Daniel Pardons
* Email			: daniel.pardons@joompad.be
* Created on	: 23 October 2012
* Last Modified : 13 February 2012
* URL			: www.joompad.be
* Copyright (C) 2012  Daniel Pardons
* License GPLv2.0 - http://www.gnu.org/licenses/gpl-2.0.html
* Based on Pedro Bothelho Slicebox revised jquery plugin 
* (http://tympanus.net/codrops/2012/10/22/slicebox-revised/)
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined('_JEXEC') or die;

//add helper
$doc =& JFactory::getDocument();

//include the class of the syndicate functions only once
require_once(dirname(__FILE__).'/helper.php');

$gallery_position = $params->get('gallery_position', '1');
$gallery_width = $params->get('gallery_width','100%');

//parameters
$orientation = $params->get('orientation','v'); 
$perspective = $params->get('perspective','1200'); 
$cuboids_count = $params->get('cuboids_count', '5'); 
$cuboids_effect = $params->get('cuboids_effect', 'false'); 
$max_cuboids_count_random = $params->get('max_cuboids_count_random', '5');  
$dispersefactor = $params->get('dispersefactor', 0); 
$hidden_slides_color = $params->get('hidden_slides_color', '#222'); 
$sequential_factor = $params->get('sequential_factor', '150'); 
$animation_speed = $params->get('animation_speed', '600');
$easing_effect = $params->get('easing_effect', 'ease');
$autoplay = $params->get('autoplay', 'false'); 
$interval = $params->get('interval', '5000');
$fallback_fade_speed = $params->get('fallback_fade_speed', '300');
$nav_controls = $params->get('nav_controls', '1');


// find items to display
switch ($params->get('g_img_src')) {
	case 'module':
		$items = ModSlicebox_sliderHelper::getImagesFromModule($params);
		break;
	case 'folder':
	default:
		$items = ModSlicebox_sliderHelper::getImagesFromFolder($params);
}

if (!count($items)) {
	echo 'Error! Module mod_slicebox_slider is unable to retrieve any images!';
	return;
}

// add js scripts

if ($params->get('load_jquery')){
	$doc->addScript(JURI::base(true) . '/modules/mod_slicebox_slider/js/jquery-1.8.3.min.js');
	$doc->addScript(JURI::base(true) . '/modules/mod_slicebox_slider/js/noconflict.js');
}

if ($params->get('load_modernizr')) {
	$doc->addScript(JURI::base(true) . '/modules/mod_slicebox_slider/js/modernizr.custom.46884.js');
} 
$doc->addScript(JURI::base(true) . '/modules/mod_slicebox_slider/js/jquery.slicebox_pad.min.js');

// add stylesheets
$doc =& JFactory::getDocument();
$doc->addStyleSheet(JURI::base(true) . '/modules/mod_slicebox_slider/css/slicebox.css', 'text/css' );
$doc->addStyleSheet(JURI::base(true) . '/modules/mod_slicebox_slider/css/custom.css', 'text/css' );

if ($params->get('titleusewebfont')) {
	$font = $params->get('titlewebfont');
	$doc->addStyleSheet('http://fonts.googleapis.com/css?family='.$font.'', "text/css" );
}

// add inline CSS
	$sscss .= '
	div#nav-arrows'.$module->id.'.nav-arrows a , div#nav-dots'.$module->id.'.nav-dots span, div#nav-options'.$module->id.'.nav-options span {
		background-color: #'.$params->get('controls_bg').' ;
	}
		
	ul#sb-slider'.$module->id.'.sb-slider li div.sb-description {
		'.$params->get('caption_css').' ;
	}
	ul#sb-slider'.$module->id.'.sb-slider li div.sb-description h3 {';
	if ($params->get('titleusewebfont')) {
		$sscss .= '
		font-family: '.$params->get('titlewebfont').' ;';
	};

	$sscss .= '
		'.$params->get('title_css').' ;
		font-size: '.$params->get('titlefontsize').' ;
		font-weight: '.$params->get('titlefontweight').' ;
		color: #'.$params->get('titlefontcolor').' ;
	}';

$doc->addStyleDeclaration($sscss);
// add slideshow script
switch ($nav_controls) {
	case 1: // bullets nav controls?> 
		<script type="text/javascript">
			jQuery(function() {
				var Page<?php echo $module->id; ?> = (function() {
					var $navArrows = jQuery( '#nav-arrows<?php echo $module->id; ?>' ).hide(),
						$navDots = jQuery( '#nav-dots<?php echo $module->id; ?>' ).hide(),
						$nav = $navDots.children( 'span' ),
						slicebox = jQuery( '#sb-slider<?php echo $module->id; ?>' ).slicebox( {
							onReady : function() {
								$navArrows.show();
								$navDots.show();
							},
							onBeforeChange : function( pos ) {
								$nav.removeClass( 'nav-dot-current' );
								$nav.eq( pos ).addClass( 'nav-dot-current' );
							},
							orientation : '<?php echo $orientation; ?>',
							perspective : <?php echo $perspective; ?>,
							cuboidsCount : <?php echo $cuboids_count; ?>,
							cuboidsRandom : <?php echo $cuboids_effect; ?>,
							maxCuboidsCount : <?php echo $max_cuboids_count_random; ?>,
							disperseFactor : <?php echo $dispersefactor; ?>,
							colorHiddenSides : '<?php echo $hidden_slides_color; ?>',
							sequentialFactor : <?php echo $sequential_factor ?>,
							speed : <?php echo $animation_speed ?>,
							easing : '<?php echo $easing_effect; ?>',
							autoplay : <?php echo $autoplay; ?>,
							interval: <?php echo $interval; ?>,
							fallbackFadeSpeed : <?php echo $fallback_fade_speed; ?>
						} ),
						
						init = function() {
							initEvents();
						},

						initEvents = function() {
							// add navigation events
							$navArrows.children( ':first' ).on( 'click', function() {
								slicebox.next();
								return false;
							} );

							$navArrows.children( ':last' ).on( 'click', function() {
								slicebox.previous();
								return false;
							} );

							$nav.each( function( i ) {
								jQuery( this ).on( 'click', function( event ) {
									var $dot = jQuery( this );
									if( !slicebox.isActive() ) {
										$nav.removeClass( 'nav-dot-current' );
										$dot.addClass( 'nav-dot-current' );
									}
									slicebox.jump( i + 1 );
									return false;
								} );
							} );
						};
						return { init : init };
				})();
				Page<?php echo $module->id; ?>.init();
			});
		</script>
	<?php break;

	case 2: // play/pause nav controls?> 
		<script type="text/javascript">
			jQuery(function() {
				var Page<?php echo $module->id; ?> = (function() {
					var $navArrows = jQuery( '#nav-arrows<?php echo $module->id; ?>' ).hide(),
						$navOptions = jQuery( '#nav-options<?php echo $module->id; ?>' ).hide(),
						slicebox = jQuery( '#sb-slider<?php echo $module->id; ?>' ).slicebox( {
							onReady : function() {
								$navArrows.show();
								$navOptions.show();
							},
							orientation : '<?php echo $orientation; ?>',
							perspective : <?php echo $perspective; ?>,
							cuboidsCount : <?php echo $cuboids_count; ?>,
							cuboidsRandom : <?php echo $cuboids_effect; ?>,
							maxCuboidsCount : <?php echo $max_cuboids_count_random; ?>,
							disperseFactor : <?php echo $dispersefactor; ?>,
							colorHiddenSides : '<?php echo $hidden_slides_color; ?>',
							sequentialFactor : <?php echo $sequential_factor ?>,
							speed : <?php echo $animation_speed ?>,
							easing : '<?php echo $easing_effect; ?>',
							autoplay : false,
							interval: <?php echo $interval; ?>,
							fallbackFadeSpeed : <?php echo $fallback_fade_speed; ?>
						} ),
						
						init = function() {
							initEvents();
						},

						initEvents = function() {
							// add navigation events
							$navArrows.children( ':first' ).on( 'click', function() {
								slicebox.next();
								return false;
							} );

							$navArrows.children( ':last' ).on( 'click', function() {
								slicebox.previous();
								return false;
							} );

							jQuery( '#navPlay<?php echo $module->id; ?>' ).on( 'click', function() {
								slicebox.play();
								return false;
							} );

							jQuery( '#navPause<?php echo $module->id; ?>' ).on( 'click', function() {
								slicebox.pause();
								return false;
							} );

<?php						if ($autoplay == 'true') { ?>
								slicebox.play();
<?php } ?>
						};

						return { init : init };
				})();
				Page<?php echo $module->id; ?>.init();
			});
		</script>
	<?php break;

	case 0:
	default: ;// arrows only?>
		<script type="text/javascript">
			jQuery(function() {
				
				var Page<?php echo $module->id; ?> = (function() {
					var $navArrows = jQuery( '#nav-arrows<?php echo $module->id; ?>' ).hide(),
						slicebox = jQuery( '#sb-slider<?php echo $module->id; ?>' ).slicebox( {
							onReady : function() {
								$navArrows.show();
							},
							orientation : '<?php echo $orientation; ?>',
							perspective : <?php echo $perspective; ?>,
							cuboidsCount : <?php echo $cuboids_count; ?>,
							cuboidsRandom : <?php echo $cuboids_effect; ?>,
							maxCuboidsCount : <?php echo $max_cuboids_count_random; ?>,
							disperseFactor : <?php echo $dispersefactor; ?>,
							colorHiddenSides : '<?php echo $hidden_slides_color; ?>',
							sequentialFactor : <?php echo $sequential_factor ?>,
							speed : <?php echo $animation_speed ?>,
							easing : '<?php echo $easing_effect; ?>',
							autoplay : <?php echo $autoplay; ?>,
							interval: <?php echo $interval; ?>,
							fallbackFadeSpeed : <?php echo $fallback_fade_speed; ?>
						} ),
						
						init = function() {
							initEvents();
						},

						initEvents = function() {
							// add navigation events
							$navArrows.children( ':first' ).on( 'click', function() {
								slicebox.next();
								return false;
							} );

							$navArrows.children( ':last' ).on( 'click', function() {
								slicebox.previous();
								return false;
							} );
						};
						return { init : init };
				})();
				Page<?php echo $module->id; ?>.init();
			});
		</script>
<?php }

//render module
require(JModuleHelper::getLayoutPath('mod_slicebox_slider'));
