<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/23/16
 * Time: 11:17 PM
 */

namespace nicolascajelli\server\build;


use ReflectionParameter;

class DependenciesMap
{
    protected $services = [];
    /**
     * @param $parameters ReflectionParameter[]
     */
    public function add($className, $parameters, $shared = true)
    {
        $dependencies = [];
        foreach ($parameters as $param) {
            $service = strval($param->getType());
            if (! isset($this->services[$service])) {
                $this->services[$service] = [
                    'shared' => true
                ];
            }
            $dependencies[] = $service;
        }
        $this->services[$className] = [
            'shared' => $shared,
            'dependencies' => $dependencies
        ];
    }

    public function getServices()
    {
        return $this->services;
    }
}