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
    public function add($className, $parameters, $shared = true, $id = null)
    {
        if ($id === null) {
            $id = $className;
        }
        $id = trim(str_replace('\\', '.', $id), '.');
        $dependencies = [];
        foreach ($parameters as $param) {
            $serviceClass = strval($param->getType());
            $service = trim(str_replace('\\', '.', $serviceClass), '.');
            if (! isset($this->services[$service])) {
                $this->services[$service] = [
                    'className' => $serviceClass,
                    'shared' => true
                ];
            }
            $dependencies[] = $service;
        }
        $this->services[$id] = [
            'className' => $className,
            'shared' => $shared,
            'dependencies' => $dependencies
        ];
    }

    public function getServices()
    {
        return $this->services;
    }
}