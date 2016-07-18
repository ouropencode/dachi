<?php
namespace Dachi\Core;

/**
 * The Model class is a parent class for all Dachi models.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Model implements \JsonSerializable {
	/**
	 * Get a json serialized version of this object.
	 * @return array
	 */
	public function jsonSerialize() {
		$json = array();

		foreach(get_object_vars($this) as $var => $val) {
			if($val instanceof \DateTime) 
				$json[$var] = $val->getTimestamp();
			else
				$json[$var] = $val;
		}

		return $json;
	}
}