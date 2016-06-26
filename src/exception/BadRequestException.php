<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/17/16
 * Time: 11:33 PM
 */

namespace nicolascajelli\server\exception;



use nicolascajelli\server\datatype\HttpError;

class BadRequestException extends \Exception implements RestException
{

    public function getHttpCode()
    {
        return HttpError::ERROR_400;
    }
}