<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/6/16
 * Time: 8:53 PM
 */

namespace nicolascajelli\server\exception;


use nicolascajelli\server\datatype\HttpError;

class UnauthorizedException extends \Exception implements RestException
{

    public function getHttpCode()
    {
        return HttpError::ERROR_401;
    }
}