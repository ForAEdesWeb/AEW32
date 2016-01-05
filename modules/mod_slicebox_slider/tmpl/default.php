<?php
/**
* Module mod_slicebox_slider for Joomla 2.5/3.0
* Created on	: 23 October 2012
* Last Modified : 04 November 2012
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

if ($gallery_position) {
	$gallery_style = "margin: 0 auto 0; padding: 0;" ;
} else {
	$gallery_style = "padding: 0;" ;
}
?>
<!-- start slicebox slider -->
	<div class="sb-slider-wrapper"  style="<?php echo $gallery_style; ?> width: <?php echo $gallery_width; ?>; ">
		<ul id="sb-slider<?php echo $module->id; ?>" class="sb-slider" >
<?php
$cnt_items = count($items);
$imagenr = 1;
foreach ($items as $item) { 
?>
			<li>
<?php		if($item->link) { ?>
				<a href="<?php echo $item->link; ?>" target="<?php echo $item->target; ?>" >
<?php } ?>
				<img src="<?php echo $item->name; ?>" alt="<?php echo $item->alt; ?>" />
<?php			if($item->link) { ?>
				</a>
<?php } ?>
<?php			if($item->title) { ?>
				<div class="sb-description"><h3><?php echo $item->title; ?></h3></div>
<?php } ?>
			</li>
<?php } ?>
		</ul>
<?php	if($cnt_items > 1 ) { ?>
		<div id="nav-arrows<?php echo $module->id; ?>" class="nav-arrows">
			<a href="slboxnav">Next</a>
			<a href="slboxnav">Previous</a>
		</div>
<?php } ?>
<?php	if($nav_controls == 1 && $cnt_items > 1 ) { ?>
		<div id="nav-dots<?php echo $module->id; ?>" class="nav-dots">
			<span class="nav-dot-current"></span>	
<?php		for($j=1; $j < $cnt_items; $j++) {	?>
			<span></span>
<?php } ?>	
		</div>	
<?php } ?>		
<?php	if($nav_controls == 2 && $cnt_items > 1 ) { ?>
		<div id="nav-options<?php echo $module->id; ?>" class="nav-options">
			<span id="navPlay<?php echo $module->id; ?>">Play</span>
			<span id="navPause<?php echo $module->id; ?>">Pause</span>
		</div>	
 <?php } ?>

	</div><!-- /wrapper -->
<div style="clear:both;"></div>
<!-- end slicebox slider -->