<?php if (!defined('ABSPATH')) {exit;}
/**
* Creates a closing tag 
*
* @link			https://icopydoc.ru/
* @since		1.0.0
*/

class XFGMC_Get_Open_Tag extends XFGMC_Get_Closed_Tag {
	protected $attr_tag_arr;

	public function __construct(string $name_tag, array $attr_tag_arr = array()) {
		parent::__construct($name_tag);

		if (!empty($attr_tag_arr)) {
			$this->attr_tag_arr = $attr_tag_arr;
		}		
	}

	public function __toString() {
		if (empty($this->get_name_tag())) { 
			return '';
		} else {
			return sprintf("<%1\$s%2\$s>",
				$this->get_name_tag(),
				$this->get_attr_tag()
			). PHP_EOL;
		}
	}

	public function get_attr_tag() {
		$res_string = '';
		if (!empty($this->attr_tag_arr)) {
			foreach ($this->attr_tag_arr as $key => $value) {
				$res_string .= ' '.$key.'="'.$value.'"';
			}
		}
		return $res_string;
	}
}
?>