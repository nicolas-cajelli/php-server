<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/5/16
 * Time: 11:41 PM
 */

namespace nicolascajelli\server\request;


class RouteDefinition
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath(ServiceRequest $request)
    {
        return $this->path;
    }
}