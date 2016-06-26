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
    public function add($className, $parameters)
    {
        $dependencies = [];
        foreach ($parameters as $param) {
            $service = strval($param->getType());
            if (! isset($this->services[$service])) {
                $this->services[$service] = [];
            }
            $dependencies[] = $service;
        }
        $this->services[$className] = $dependencies;
    }

    public function getServices()
    {
        return $this->services;
    }
}