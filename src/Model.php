<?php
namespace Dachi\Core;

/**
 * The Model class is a parent class for all Dachi models.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    $ourOpenCode
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

	/**
	 * Retrieve all the Model's data in an array
	 *
	 * Models should implement this method themselves and provide the required
	 * data. Models can omit this, it just means you can't use it.
	 *
	 * @param bool $safe Should we return only data we consider "publicly exposable"?
	 * @param bool $eager Should we eager load child data?
	 * @return array
	 */
	public function asArray($safe = false, $eager = false) {
		throw new \Exception("unimplimented asArray method on model");
	}

	/**
	 * Retrieve all the Model's "publicly exposable" data in an array
	 * @param bool $eager Should we eager load child data?
	 * @return array
	 */
	public function asSafeArray($eager = false) {
		return $this->asArray(true, $eager);
	}

}
