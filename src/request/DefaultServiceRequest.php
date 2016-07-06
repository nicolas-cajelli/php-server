<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/6/16
 * Time: 12:05 AM
 */

namespace nicolascajelli\server\request;


class DefaultServiceRequest implements ServiceRequest
{

    /**
     * DefaultServiceRequest constructor.
     * @param array $array
     */
    public function __construct($queryArgs = [], $pathArgs = [])
    {
        $this->queryArgs = $queryArgs;
        $this->pathArgs = $pathArgs;
    }

    public function getQueryArguments() : array
    {
        return $this->queryArgs;
    }

    public function getPathArguments() : array
    {
        return $this->pathArgs;
    }
}