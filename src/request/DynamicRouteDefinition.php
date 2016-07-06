<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/6/16
 * Time: 12:13 AM
 */

namespace nicolascajelli\server\request;


class DynamicRouteDefinition extends RouteDefinition
{
    public function getPath(ServiceRequest $request)
    {
        $path = $this->path;
        foreach ($request->getPathArguments() as $key => $value) {
            $path = preg_replace('#\(\?P<' . $key . '>([^\)]+)\)#', $value, $path);
        }
        return $path;
    }
}