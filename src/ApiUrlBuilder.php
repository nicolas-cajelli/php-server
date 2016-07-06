<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/5/16
 * Time: 11:28 PM
 */

namespace nicolascajelli\server;


use nicolascajelli\server\filesystem\ProjectStructure;
use nicolascajelli\server\request\Request;
use nicolascajelli\server\request\RouteDefinition;
use nicolascajelli\server\request\DynamicRouteDefinition;
use nicolascajelli\server\request\ServiceRequest;
/**
 * @Inject
 */
class ApiUrlBuilder
{
    /**
     * @var RouteDefinition[]
     */
    protected $definitions = [];

    public function __construct(Request $request)
    {
        $this->host = $request->getProtocol() . '://' . $request->getHost();
    }

    public function initRoutes(ProjectStructure $structure)
    {
        $file = $structure->cd('build')->file('path_mapping.php');
        $mapping = $file->requireContent();
        $types = ['get', 'post', 'put'];
        foreach ($mapping['simple_paths'] as $path => $config) {
            foreach ($types as $type) {
                if (! isset($config[$type])) {
                    continue;
                }
                foreach ($config[$type] as $key => $routeName) {
                    if ($key == 'name' && ! empty($routeName)) {
                        $this->definitions[$routeName] = new RouteDefinition($path);
                    }
                }
            }
        }
        foreach ($mapping['dynamic_paths'] as $path => $config) {
            foreach ($types as $type) {
                if (! isset($config[$type])) {
                    continue;
                }
                foreach ($config[$type] as $key => $routeName) {
                    if ($key == 'name' && ! empty($routeName)) {
                        $this->definitions[$routeName] = new DynamicRouteDefinition($path);
                    }
                }
            }
        }
    }

    public function buildForRoute($routeName, ServiceRequest $request) : string
    {
        if (! isset($this->definitions[$routeName])) {
            throw new \InvalidArgumentException('Invalid route name: ' . $routeName);
        }

        $definition = $this->definitions[$routeName];
        $url = $this->host . $definition->getPath($request);

        $args = $request->getQueryArguments();
        if (! empty($args)) {
            $url .= '?' . http_build_query($args);
        }
        return $url;
    }
}