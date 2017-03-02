<?php

namespace Dachi\Core;

/**
 * The Template class is responsable for rendering all templates.
 *
 * @version   2.0.0
 *
 * @since     2.0.0
 *
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
class Template
{
    protected static $twig = null;
    protected static $render_actions = [];
    protected static $render_template = '@global/base';

    /**
     * Load the routing information object into memory.
     *
     * @return null
     */
    protected static function initialize()
    {
        $loader = new \Twig_Loader_Filesystem();

        foreach (Modules::getAll() as $module) {
            if (file_exists($module->getPath().'/Views')) {
                $loader->addPath($module->getPath().'/Views', $module->getShortName());
            }
        }

        if (file_exists('views')) {
            $loader->addPath('views', 'global');
            $loader->addPath('views', 'Global');
        }

        self::$twig = new \Twig_Environment($loader, [
            'debug'            => Configuration::get('debug.template', 'false') === 'true',
            'auto_reload'      => Kernel::getEnvironment() === 'local',
            'charset'          => Configuration::get('templates.charset', 'utf-8'),
            'cache'            => 'cache/twig',
            'strict_variables' => false,
            'autoescape'       => true,
        ]);

        self::$twig->addFilter(new \Twig_SimpleFilter('time_short', function ($date) {
            if ($date == 'now') {
                $date = new \DateTime();
            }

            return $date->format('H:i');
        }));
        self::$twig->addFilter(new \Twig_SimpleFilter('date_short', function ($date) {
            if ($date == 'now') {
                $date = new \DateTime();
            }

            return $date->format('Y-m-d');
        }));
        self::$twig->addFilter(new \Twig_SimpleFilter('date_long', function ($date) {
            if ($date == 'now') {
                $date = new \DateTime();
            }

            return $date->format('jS F Y');
        }));
        self::$twig->addFilter(new \Twig_SimpleFilter('date_uk', function ($date) {
            if ($date == 'now') {
                $date = new \DateTime();
            }

            return $date->format('d/m/Y');
        }));
        self::$twig->getExtension('core')->setDateFormat('Y-m-d H:i');

        $sort_filter = function ($value, $key, $direction, $absolute, $natural) {
            usort($value, function ($a, $b) use ($key, $direction, $absolute, $natural) {
                if ($key) {
                    $a = $a[$key];
                    $b = $b[$key];
                }

                if ($absolute) {
                    $a = abs($a);
                    $b = abs($b);
                }

                if ($direction == 'desc' || $direction === true) {
                    if ($natural) {
                        return strnatcmp($b, $a);
                    }

                    if ($a == $b) {
                        return 0;
                    }

                    return $a > $b ? -1 : 1;
                }

                if ($direction == 'asc') {
                    if ($natural) {
                        return strnatcmp($a, $b);
                    }

                    if ($a == $b) {
                        return 0;
                    }

                    return $a > $b ? 1 : -1;
                }

                return 0;
            });

            return $value;
        };

        /*
         * $test = array(
         *   array("value" => 3),
         *   array("value" => 1),
         *   array("value" => 2),
         *   array("value" => 5),
         *   array("value" => 4),
         *   array("value" => -3),
         *   array("value" => -1),
         *   array("value" => -2),
         *   array("value" => -5),
         *   array("value" => -4)
         * );
         *
         * (the zero argument versions only work on simple arrays!)
         *
         * | sort([key, direction, absolute, natural])
         * | sort()                                          [-5, -4, -3, -2, -1, 1, 2, 3, 4, 5]
         * | sort("value", "asc")                            [-5, -4, -3, -2, -1, 1, 2, 3, 4, 5]
         * | sort("value", "desc")                           [5, 4, 3, 2, 1, -1, -2, -3, -4, -5]
         * | sort("value", true)                             [5, 4, 3, 2, 1, -1, -2, -3, -4, -5]
         * | sort("value", "asc", true)                      [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | sort("value", "asc", true, true)                [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | sort("value", "asc", false, true)               [-1, -2, -3, -4, -5, 1, 2, 3, 4, 5]
         *
         * | sortabs([key, direction])
         * | sortabs()                                       [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | sortabs("value", "asc")                         [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | sortabs("value", "desc")                        [5, -5, -4, 4, -3, 3, 2, -2, 1, -1]
         *
         * | natsort([key, direction])
         * | natsort()                                       [-1, -2, -3, -4, -5, 1, 2, 3, 4, 5]
         * | natsort("value", "asc")                         [-1, -2, -3, -4, -5, 1, 2, 3, 4, 5]
         * | natsort("value", "desc")                        [5, 4, 3, 2, 1, -5, -4, -3, -2, -1]
         *
         * | natsortabs([key, direction])
         * | natsortabs()                                    [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | natsortabs("value", "asc")                      [-1, 1, -2, 2, -3, 3, -4, 4, 5, -5]
         * | natsortabs("value", "desc")                     [5, -5, -4, 4, -3, 3, 2, -2, 1, -1]
         */

        self::$twig->addFilter(new \Twig_SimpleFilter('sort', function ($value, array $options = []) use ($sort_filter) {
            $key = isset($options[0]) ? $options[0] : null;
            $direction = isset($options[1]) ? $options[1] : 'asc';
            $absolute = isset($options[2]) ? $options[2] : false;
            $natural = isset($options[3]) ? $options[3] : false;

            return $sort_filter($value, $key, $direction, $absolute, $natural);
        }, ['is_variadic' => true]));

        self::$twig->addFilter(new \Twig_SimpleFilter('natsort', function ($value, array $options = []) use ($sort_filter) {
            $key = isset($options[0]) ? $options[0] : null;
            $direction = isset($options[1]) ? $options[1] : 'asc';

            return $sort_filter($value, $key, $direction, false, true);
        }, ['is_variadic' => true]));

        self::$twig->addFilter(new \Twig_SimpleFilter('sortabs', function ($value, array $options = []) use ($sort_filter) {
            $key = isset($options[0]) ? $options[0] : null;
            $direction = isset($options[1]) ? $options[1] : 'asc';

            return $sort_filter($value, $key, $direction, true, false);
        }, ['is_variadic' => true]));

        self::$twig->addFilter(new \Twig_SimpleFilter('natsortabs', function ($value, array $options = []) use ($sort_filter) {
            $key = isset($options[0]) ? $options[0] : null;
            $direction = isset($options[1]) ? $options[1] : 'asc';

            return $sort_filter($value, $key, $direction, true, true);
        }, ['is_variadic' => true]));
    }

    /**
     * Retreive a twig template object.
     *
     * @param string $template The template file
     *
     * @return Twig_Template
     */
    public static function get($template)
    {
        if (self::$twig === null) {
            self::initialize();
        }

        return self::$twig->loadTemplate($template.'.twig');
    }

    /**
     * Append a template render action to the render queue.
     *
     * @param string $template  The template file
     * @param string $target_id The dachi-ui-block to load into
     *
     * @return null
     */
    public static function display($template, $target_id)
    {
        if (self::$twig === null) {
            self::initialize();
        }

        self::$render_actions[] = [
            'type'      => 'display_tpl',
            'template'  => $template,
            'target_id' => $target_id,
        ];
    }

    /**
     * Render the render queue to the browser.
     *
     * If the request is an ajax request, the render queue and data will be sent to the browser via JSON.
     * If the request is a standard request, the render queue will be rendered server-side and will be sent to the browser via HTML.
     *
     * @internal
     *
     * @see Router
     *
     * @return null
     */
    public static function render()
    {
        $apiMode = Request::isAPI();

        if (self::$twig === null) {
            self::initialize();
        }

        $data = Request::getAllData();
        if (!$apiMode) {
            $data['siteName'] = Configuration::get('dachi.siteName', 'Unnamed Dachi Installation');
            $data['timezone'] = Configuration::get('dachi.timezone', 'Europe/London');
            $data['domain'] = Configuration::get('dachi.domain', 'localhost');
            $data['baseURL'] = Configuration::get('dachi.baseURL', '/');
            $data['assetsURL'] = str_replace('%v', Kernel::getGitHash(), Configuration::get('dachi.assetsURL', '/build/'));
            $data['renderTPL'] = self::getRenderTemplate();
            $data['URI'] = Request::getFullUri();
        }

        if ($apiMode) {
            $response = [
                'data'           => $data,
                'response'       => Request::getResponseCode(),
            ];

            return json_echo($response);
        } elseif (Request::isAjax()) {
            $response = [
                'render_tpl'     => self::getRenderTemplate(),
                'data'           => $data,
                'response'       => Request::getResponseCode(),
                'render_actions' => self::$render_actions,
            ];

            return json_echo($response);
        } else {
            $data['response'] = Request::getResponseCode();

            $response = self::$twig->render(self::getRenderTemplate(true), $data);

            foreach (array_reverse(self::$render_actions) as $action) {
                switch ($action['type']) {
                    case 'redirect':
                        if ($action['soft'] !== true) {
                            header('Location: '.$action['location']);
                        }
                        break;
                    case 'display_tpl':
                        $match = preg_match("/<dachi-ui-block id=[\"']".preg_quote($action['target_id'])."[\"'][^>]*>([\s\S]*)<\/dachi-ui-block>/U", $response, $matches);
                        if ($match) {
                            $replacement = "<dachi-ui-block id='".$action['target_id']."'>".self::$twig->render($action['template'].'.twig', $data).'</dachi-ui-block>';
                            $response = str_replace($matches[0], $replacement, $response);
                        }
                        break;
                }
            }

            echo $response;
        }
    }

    /**
     * Append a redirect action to the render queue.
     *
     * If $location does not start with "http", the dachi.baseURL configuration value will be prepended
     *
     * @param string $location The location to redirect to
     *
     * @return null
     */
    public static function redirect($location, $soft = false)
    {
        if (substr($location, 0, 4) !== 'http') {
            $location = Configuration::get('dachi.baseURL').$location;
        }

        self::$render_actions[] = [
            'type'     => 'redirect',
            'location' => $location,
            'soft'     => $soft,
        ];
    }

    /**
     * Retreive the current render queue.
     *
     * @return array
     */
    public static function getRenderQueue()
    {
        return self::$render_actions;
    }

    /**
     * Retreive the base render template.
     *
     * @return array
     */
    public static function getRenderTemplate($extension = false)
    {
        return self::$render_template.($extension ? '.twig' : '');
    }

    /**
     * Set the base render template.
     *
     * @return array
     */
    public static function setRenderTemplate($template)
    {
        self::$render_template = $template;
    }
}
