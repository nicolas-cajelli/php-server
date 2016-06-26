<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/17/16
 * Time: 11:28 PM
 */

namespace nicolascajelli\server\exception;

use nicolascajelli\server\datatype\HttpError;

class NotFoundException extends \Exception implements RestException
{
    public function getHttpCode() {
        return HttpError::ERROR_404;
    }

}