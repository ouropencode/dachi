<?php
namespace Dachi\Core;

use Dachi\Core\Kernel;

/**
 * The Configuration class is responsable for loading, building and retrieving all configuration data.
 *
 * Configuration is stored in seperate .json files for each core functionality. These files are stored
 * in three directories. One for each environment level. Sucessive levels are treated as overrides.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Configuration {
	/**
	 * Full configuration object.
	 *
	 * This array is structured as an array of nested arrays, with three top nodes:
	 * - production
	 * - development
	 * - environment
	 *
	 * Below these nodes are the configuration files, these should be be formatted as such:
	 * - dachi
	 * - api.twitter
	 * - [a-z.-]+
	 *
	 * Under the configuration file nodes the structure is determined by the file itself and can vary.
	 *
	 * @var array
	 */
	protected static $config = array();

	/**
	 * Load the configuration object into memory.
	 * @return null
	 */
	protected static function load() {
		self::$config = json_decode(file_get_contents('cache/dachi.config.json'), true);
	}

	/**
	 * Get a configuration entry
	 * @param  string $key     The configuration key to retrieve (e.g. dachi.siteName or api.twitter.publicKey)
	 * @param  string $default The default value to return if the key was not found
	 * @return mixed
	 */
	public static function get($key, $default = "default") {
		if(self::$config == array())
			self::load();

		$env = Kernel::getEnvironment();
		$position = self::$config[$env];
		$token = strtok($key, '.');
		while($token !== false) {
			$nextToken = strtok('.');
			if(!isset($position[$token]))
				return $default;

			if($nextToken === false)
				return $position[$token];

			$position = &$position[$token];
			$token = $nextToken;
		}

		return $default;
	}
}
