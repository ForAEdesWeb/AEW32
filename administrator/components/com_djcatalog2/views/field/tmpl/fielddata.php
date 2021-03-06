<?php
/**
 * @version $Id: fielddata.php 105 2013-01-23 14:05:57Z michal $
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

defined('_JEXEC') or die;

$out = '';

switch ($this->fieldtype) {
	case 'select':
	case 'radio':
	case 'checkbox': {
		$out .= '
		<div class="control-group">
			<div class="control-label">
			<label>'
			.JText::_('COM_DJCATALOG2_FIELD_TYPE_'.strtoupper($this->fieldtype))
			.' '
			.JText::_('COM_DJCATALOG2_FIELD_TYPE_OPTIONS').'</label>
			</div>
			<div class="controls">	
				<span class="btn" onclick="Djfieldtype_'.$this->suffix.'.appendOption();">
				'.JText::_('COM_DJCATALOG2_FIELD_TYPE_ADD_OPTION').'
				</span>
			</div>
		</div>'
		;
		
		$out .= '<div class="clearfix"></div>
			 	<table class="table-condensed">
			 	<thead>
			 		<tr>
			 			<th>'.JText::_('COM_DJCATALOG2_FIELD_OPTION_NAME').'</th>
			 			<th>'.JText::_('COM_DJCATALOG2_FIELD_OPTION_POSITION').'</th>
			 		</tr>
			 	</thead>
			 	<tbody id="DjfieldOptions">'
			 ;
		if ($this->fieldId > 0) {
			if (count($this->fieldoptions)) {
				foreach ($this->fieldoptions as $option) {
					$out .= '<tr>
						 <td>
							 <input type="hidden" name="fieldtype[id][]" value="'.$option->id.'"/>
							 <input type="text" size="30" name="fieldtype[option][]" value="'.$option->value.'" class="input-medium" />
						 </td>
						 <td>
							 <input type="text" size="4" name="fieldtype[position][]" value="'.$option->ordering.'" class="input-mini" /><span class="btn button-x">&nbsp;&nbsp;&minus;&nbsp;&nbsp;</span><span class="btn button-down">&nbsp;&nbsp;&darr;&nbsp;&nbsp;</span><span class="btn button-up">&nbsp;&nbsp;&nbsp;&uarr;&nbsp;&nbsp;&nbsp;</span>
						 </td>
						 </tr>'
						 ;
				}
			}
		}
		$out .'</tbody>
			</table>';
		break;
	}
	default: {
		break;
	}
}

echo $out;