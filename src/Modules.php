<?php

namespace Dachi\Core;

/**
 * The Modules class is responsable for providing information regarding modules.
 *
 * This class will provide information such as a modules PHP namespace, it's path on filesystem
 * and it's shortcut name.
 *
 * @version   2.0.0
 *
 * @since     2.0.0
 *
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Modules
{
    protected static $modules = [];

    /**
     * Load the routing information object into memory.
     *
     * @return null
     */
    protected static function initialize()
    {
        if (file_exists('cache/dachi.modules.json')) {
            $modules = json_decode(file_get_contents('cache/dachi.modules.json'));

            foreach ($modules as $module) {
                self::$modules[$module->shortname] = new Module($module);
            }
        }
    }

    /**
     * Retrieve module information.
     *
     * @param string $module Module shortname
     *
     * @return Module
     */
    public static function get($module)
    {
        if (self::$modules === []) {
            self::initialize();
        }

        if (!isset(self::$modules[$module])) {
            return false;
        }

        return self::$modules[$module];
    }

    /**
     * Retrieve all module information.
     *
     * @return array Array of Module objects
     */
    public static function getAll()
    {
        if (self::$modules === []) {
            self::initialize();
        }

        return self::$modules;
    }
}

/**
 * The Module class is responsable for storing information related to a module.
 *
 * @version   2.0.0
 *
 * @since     2.0.0
 *
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Module
{
    protected $shortname = '';
    protected $path = '';
    protected $namespace = '';

    public function __construct($module)
    {
        $this->shortname = $module->shortname;
        $this->path = $module->path;
        $this->namespace = $module->namespace;
    }

    /**
     * Get module short name.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortname;
    }

    /**
     * Get module path from root.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get module PHP namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
