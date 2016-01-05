<?php
/**
* Module mod_slicebox for Joomla 2.5/3.0
* Created on	: 23 October 2012
* Last Modified : 10 February 2013
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

// no direct access
defined('_JEXEC') or die;


$doc =& JFactory::getDocument();

$base = JURI::base();
$mod_basepath = JURI::base()."modules/mod_slicebox_slider/";

class ModSlicebox_sliderHelper 
{	
	/**
	 * get the list of all image file names (jpg|jpeg|gif|png) defined in folder parameter
	 * 
	 * @return Array of file names
	 */

    function getImagesFromFolder(&$params) {

		$folder = $params->get('folder');

		if(!$dir = @opendir($folder)) return null;
        while (false !== ($file = readdir($dir)))
        {
            if (preg_match('/.+\.(jpg|jpeg|gif|png)$/', $file)) $files[] = $file;						
        }
        closedir($dir);

		$image_cnt = count ($files); // nr of images found in folder
		$mod_max_img = 9; // max 9 images can be processed by the module for performance reason
		if(!is_numeric($g_max_img = $params->get('g_max_img'))) $g_max_img = $mod_max_img;
		if ($g_max_img > $mod_max_img) $g_max_img = $mod_max_img; // limit max number of slides to be displayed
		if ($g_max_img > $image_cnt) $g_max_img = $image_cnt; // limit to number of slides found in folder if less than the number requested 
		
		($params->get('g_s_crit'))? shuffle($files) : natcasesort($files); // randomize if requested

		$images = array_slice($files, 0, $g_max_img); // limit to the max requested images

		foreach($images as $image) {
			$slides[] = (object) array(
				'title'=>'',
				'name'=>JURI::base().$folder.$image,
				'link'=>'',
				'alt'=>$image,
				'target'=>'',
			);
		}

		return $slides;
	
	}

	/**
	 * get the list of all images defined in module parameters
	 * 
	 * @return Array of file names
	 */

    function getImagesFromModule(&$params) {
		$mod_max_img = 9; // max nr of images to be processed (max 9 images can be defined in module parameters)
//		if(!is_numeric($g_max_img = $params->get('g_max_img'))) $g_max_img = $mod_max_img;
//		if ($g_max_img > $mod_max_img) $g_max_img = $mod_max_img; // limit max number of slides to be displayed

		$folder 	= JURI::base().$params->get( 'folder' );		

		// initialize working arrays
		$image_ref = array();
		$image_img = array();
		$image_alt = array();
		$image_title = array();
		$image_url = array();
		$target_url= array();

		$image_url_artid = array(); // future?
		$image_url_artid_pos = array(); // future?

		for ($i = 1; $i <= $mod_max_img; $i++) {
			if ($params->get( 'img_img'.$i )) { // check if image file name is defined
				$image_ref[]	= $i;
				$image_img[$i] 	= $folder.trim($params->get( 'img_img'.$i ));
				$image_title[$i] = $params->get( 'img_title'.$i );
				$image_alt[$i] 	= ($image_title[$i]) ? strip_tags($image_title[$i]) : "";
				// check if url parameter references an article id or a blank/full url 
				$image_url[$i] 	= str_replace('&', '&amp;', $params->get( 'img_url'.$i )); // future
				if ( (int)$image_url[$i] == $image_url[$i] && (int)$image_url[$i]  > 0 ) {
					$image_url_artid[] = $image_url[$i];
					$image_url_artid_pos[] = $i;
				} 
				$target_url[$i] = $params->get( 'img_target_url'.$i );
			} 
		}
		// replace url article id by article sef url if needed
		$image_url_artid_cnt = count ($image_url_artid);
//		$url_id_target	= $params->get( 'url_id_target'); // get target type (J! content or K2 items)
		$url_id_target = 0; //force joomla content for debug
		// if some artid/K2itemid are defined in the image url module parameter, start process to convert artid/K2itemid in sef url
		if ($image_url_artid_cnt) {
			if ($url_id_target) {
				$urls = self::getk2ArticlesUrl ($image_url_artid);		
			} else {
				$urls = self::getArticlesUrl ($image_url_artid);
			}
			$urls_cnt = count ($urls);

		// check if all art/k2 items ids have been converted to urls (duplicate artid or non existent artid) and replace missing url for duplicate artids by url associated to the first artid
		// create array with artids not converted to url (contains "")
			$missing_urls = array_keys($urls,""); 
			for ($j = 0; $j < count ($missing_urls); $j++) {
				$missing_artid = $image_url_artid[$missing_urls[$j]];
				// find the key of the same artid if existing in the array 
				$equivalent_artid = array_search ($missing_artid, $image_url_artid);
				if ($equivalent_artid !== false) {
					$urls[$missing_urls[$j]] = $urls[$equivalent_artid];
				}
			}

		// replace urls content/k2 items id by their sef url in the base $image_url array and forceset target =_self
			for ($i = 0; $i < $image_url_artid_cnt; $i++) {
				$image_url[$image_url_artid_pos[$i]] = $urls[$i];
			}
		}

		if ($params->get('g_s_crit')) { // apply requested sorting criteria
				shuffle ($image_ref);
		}
		
		$image_ref = array_slice($image_ref, 0, $g_max_img); // limit to the max requested images
		$cnt_img_for_display = count ($image_ref);

		// init return object 
	
		$imagenr = 0;
		for ($i= 1; $i <= $cnt_img_for_display; $i++) {
			$cur_img = $image_ref[$imagenr] ;
			$slides[] = (object) array(
				'position'=>$cur_img,
				'title'=>$image_title[$cur_img],
				'name'=>$image_img[$cur_img],
				'link'=>$image_url[$cur_img],
				'alt'=>$image_alt[$cur_img],
				'target'=>$target_url[$cur_img],
			);		
		$imagenr++;
		}	
				
		return $slides;
	
	}

	/**
	 * get the urls of the list of articles
	 * 
	 * @param $ids Array;
	 * @return Array
	 */
	function getArticlesUrl ($ids)
	{
		//Load content route helper
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';

		$urls = array_fill (0, count ($ids), ""); // initialise urls to ""	

		global $mainframe;
		$my 	       = &JFactory::getUser();
		$aid	       = $my->get( 'aid', 0 );

		$db	    = &JFactory::getDBO();
		$date   =& JFactory::getDate();
		$now    = $date->toMySQL();

		// make sql query
		$query 	= 'SELECT a.id,cc.description as catdesc, cc.title as category_title, cc.title as cattitle,s.description as secdesc, s.title as sectitle,' 
				. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
				. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":",cc.id,cc.alias) ELSE cc.id END as catslug,'
				. ' CASE WHEN CHAR_LENGTH(s.alias) THEN CONCAT_WS(":", s.id, s.alias) ELSE s.id END as secslug'
				. "\n FROM #__content AS a"
				. ' INNER JOIN #__categories AS cc ON cc.id = a.catid' 
				. ' INNER JOIN #__sections AS s ON s.id = a.sectionid'
				. "\n WHERE a.state = 1"
				. "\n AND ( a.publish_up = " . $db->Quote( $db->getNullDate() ) . " OR a.publish_up <= " . $db->Quote( $now  ) . " )"
				. "\n AND ( a.publish_down = " . $db->Quote( $db->getNullDate() ) . " OR a.publish_down >= " . $db->Quote( $now  ) . " )"
				. ( ( !$mainframe->getCfg( 'shownoauth' ) ) ? "\n AND a.access <= " .(int) $aid. " AND cc.access <= " .(int) $aid. " AND s.access <= " .(int) $aid : '')
				;
		$query .= '  AND a.id IN("'. implode( '","', $ids ) .'") ';
		$db->setQuery($query);
		$data = $db->loadObjectlist();
		if( empty($data) ) return $urls;
		
		foreach( $data as $key => $item ){	
			$index = array_search ($data[$key]->id, $ids);
			if($item->access <= $aid ) {
				$data[$key]->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
			} else {
				$data[$key]->link = JRoute::_('index.php?option=com_user&view=login');
			}
			$urls[$index] = $data[$key]->link;
		}
		return $urls;	
	}
	
	/**
	 * get the urls of the list of k2articles
	 * 
	 * @param $ids Array;
	 * @return Array
	 */
	function getk2ArticlesUrl ($ids)
	{
		$urls = array_fill (0, count ($ids), ""); // initialise urls to ""
		if( !ModCoinsliderproHelper::isK2Existed() ){
			return $urls;
		} 
		return self::getk2ListUrl( $ids );
	}
		
	/**
	 * check K2 Existed ?
	 */
	 function isK2Existed(){
		return is_file( JPATH_SITE.DS.  "components" . DS . "com_k2" . DS . "k2.php" );	
	}		
		

	/**
	 * get the urls of the list of k2articles
	 * 
	 * @param $ids Array;
	 * @return Array
	 */		
		
	 function getk2ListUrl( $ids ){		
		//Load K2 content route helper
		require_once ( JPath::clean(JPATH_SITE.'/components/com_k2/helpers/route.php') );

		$urls = array_fill (0, count ($ids), ""); // initialise urls to ""
		
		global $mainframe;
		$my 	       = &JFactory::getUser();
		$aid	       = $my->get( 'aid', 0 );

		$db	    = &JFactory::getDBO();
		$date   =& JFactory::getDate();
		$now    = $date->toMySQL();
		
		// make sql query

		$query = "SELECT a.*,  c.name as categoryname,
						c.id as categoryid, c.alias as categoryalias, c.params as categoryparams".
				" FROM #__k2_items as a".
					" LEFT JOIN #__k2_categories c ON c.id = a.catid"; 
			
		$query .= " WHERE a.published = 1"
					. " AND a.access <= {$aid}"
					. " AND a.trash = 0"
					. " AND c.published = 1"
					. " AND c.access <= {$aid}"
					. " AND c.trash = 0 " ;	

		$query .= '  AND a.id IN("'. implode( '","', $ids ) .'") ';			

		$db->setQuery($query);
		$data = $db->loadObjectlist();

		if( empty($data) ) return $urls;
	
		foreach( $data as $key => $item ){	
			$index = array_search ($data[$key]->id, $ids);
			if($item->access <= $aid ) {
				$data[$key]->link = JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.$item->alias, $item->catid.':'.$item->categoryalias));
			} else {
				$data[$key]->link = JRoute::_('index.php?option=com_user&view=login');
			}
			$urls[$index] = $data[$key]->link;
		}
		return $urls;
	}

	function HexToRGB($hex) {
		$hex = ereg_replace("#", "", $hex);
		$color = array();
		 
		if(strlen($hex) == 3) {
		$color['r'] = hexdec(substr($hex, 0, 1) . $r);
		$color['g'] = hexdec(substr($hex, 1, 1) . $g);
		$color['b'] = hexdec(substr($hex, 2, 1) . $b);
		}
		else if(strlen($hex) == 6) {
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
		}
 
		return $color;
	}
 
	function RGBToHex($r, $g, $b) {
		$hex = "#";
		$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
		$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
		 
		return $hex;		
	}

}
