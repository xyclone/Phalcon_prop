<?php
namespace Property\Helpers;

use Property\Models\BaseMenu;
/**
* class Tag
*/
class Tag extends \Phalcon\Tag
{

	public static function groupMenu()
	{
   		$header = BaseMenu::find(["conditions" => "active = 'Y'"]);
		$tag 	= '<option value="">Pilih Group Menu</option>';
		$selected = "";
   		foreach ($header as $key => $value) {
   			if ($selected == $value->id) {
				$tag .= '<option value="' . $value->id . '" selected>' . $value->menu_group . '</option>';
   			}else{
				$tag .= '<option value="' . $value->id . '">' . $value->menu_group . '</option>';
   			}
   		}
        return $tag;
	}
	
	

}