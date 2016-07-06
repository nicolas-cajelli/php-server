<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/5/16
 * Time: 11:31 PM
 */

namespace nicolascajelli\server\request;


interface ServiceRequest
{
    public function getQueryArguments() : array;
}