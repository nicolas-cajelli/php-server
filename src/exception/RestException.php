<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/17/16
 * Time: 11:33 PM
 */

namespace nicolascajelli\server\exception;


interface RestException
{
    public function getHttpCode();
}