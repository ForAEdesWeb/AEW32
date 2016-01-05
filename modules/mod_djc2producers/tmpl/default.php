<?php
/**
 * @version $Id: default.php 87 2012-08-02 12:13:07Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined ('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="producersForm" id="producersForm" >
	<input type="hidden" name="option" value="com_djcatalog2" />
	<input type="hidden" name="view" value="items" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="order" value="<?php echo $order; ?>" />
	<input type="hidden" name="dir" value="<?php echo $orderDir; ?>" />
	<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
	<input type="hidden" name="task" value="search" />
    <?php
		$options = array();
		$options[] = JHTML::_('select.option', 0,JText::_('MOD_DJC2PRODUCERS_CHOOSE_PRODUCER') );
		foreach($producers as $producer){
			$options[] = JHTML::_('select.option', $producer['id'], $producer['name']);
			
		}

		echo JHTML::_('select.genericlist', $options, 'pid', 'onchange="producersForm.submit()"', 'value', 'text', $prod_id, 'mod_djc2producers_pid');
?>
<input type="submit" style="display: none;"/>
</form>
