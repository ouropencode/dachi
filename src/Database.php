<?php
namespace Dachi\Core;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * The Database class is responsable for managing database interfaces.
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Database {
	protected static $entity_manager = null;

	/**
	 * Initialize the Doctrine database engine.
	 *
	 * This will connect to the database uri specified in the configuration.
	 * If the database uri cannot be found, an in-memory sqlite instance will be used.
	 *
	 * @return null
	 */
	public static function initialize() {
		$paths = array_merge(
			array_filter(glob('src/*'), 'is_dir'),
			array_filter(glob('src-*/*'), 'is_dir'),
			array_filter(glob(__DIR__ . '/../../*/src/*/'), 'is_dir')
		);

		$db_params = array(
			"url" => Configuration::get("database.uri", "sqlite:///:memory:")
		);

		$cache = null;
		if(Configuration::get("database.cache", "false") == "false")
			$cache = new \Doctrine\Common\Cache\ArrayCache;

		$config = Setup::createAnnotationMetadataConfiguration($paths, Configuration::get("debug.database", "false") == "true", "cache", $cache);

		foreach(Modules::getAll() as $module)
			$config->addEntityNamespace($module->getShortName(), $module->getNamespace());

		self::$entity_manager = EntityManager::create($db_params, $config);
	}

	/**
	 * Get a reference to the Doctrine2 entity manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return EntityManager
	 */
	public static function getEntityManager() {
		if(self::$entity_manager == null)
			self::initialize();

		return self::$entity_manager;
	}

	/**
	 * Wrapper to the Doctrine2 getRepository function.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return EntityRepository
	 */
	public static function getRepository() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "getRepository"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 find function.
	 *
	 * This function will call the 'find' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return mixed
	 */
	public static function find() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "find"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 flush function.
	 *
	 * This function will call the 'flush' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return null
	 */
	public static function flush() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "flush"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 persist function.
	 *
	 * This function will call the 'persist' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return null
	 */
	public static function persist() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "persist"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 remove function.
	 *
	 * This function will call the 'remove' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return null
	 */
	public static function remove() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "remove"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 createQuery function.
	 *
	 * This function will call the 'createQuery' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return mixed
	 */
	public static function createQuery() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "createQuery"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 createQueryBuilder function.
	 *
	 * This function will call the 'createQueryBuilder' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return mixed
	 */
	public static function createQueryBuilder() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "createQueryBuilder"), func_get_args());
	}
	
	/**
	 * Wrapper to the Doctrine2 createNativeQuery function.
	 *
	 * This function will call the 'createNativeQuery' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return mixed
	 */
	public static function createNativeQuery() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "createNativeQuery"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 getReference function.
	 *
	 * This function will call the 'getReference' function on the Doctrine2 Entity Manager.
	 *
	 * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
	 * @return mixed
	 */
	public static function getReference() {
		if(self::$entity_manager == null)
			self::initialize();

		return call_user_func_array(array(self::$entity_manager, "getReference"), func_get_args());
	}

	/**
	 * Wrapper to the Doctrine2 Debug::Dump function.
	 *
	 * This function will call the 'Dump' static function on the \Doctrine\Common\Util\Debug class.
	 *
	 * @see http://www.doctrine-project.org/api/common/2.3/class-Doctrine.Common.Util.Debug.html
	 * @return mixed
	 */
	public static function dump() {
		return call_user_func_array(array("\Doctrine\Common\Util\Debug", "Dump"), func_get_args());
	}
}
