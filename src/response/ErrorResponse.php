<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/12/16
 * Time: 10:22 PM
 */

namespace nicolascajelli\server\response;

/**
 * @NonSharedService
 */
class ErrorResponse extends ApiResponse
{
    /**
     * @var
     */
    private $_message;

    public function setData($message, $status)
    {
        $this->_message = $message;
        $this->_headers[] = 'HTTP/1.1 ' . $status;
    }

    protected function getStatus() : string
    {
        return 'error';
    }

    protected function getData() : array
    {
        return [
            'message' => $this->_message
        ];
    }

    public function render()
    {
        foreach ($this->getHeaders() as $header) {
            header($header);
        }
        parent::render();
    }
}