<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/12/16
 * Time: 10:23 PM
 */

namespace nicolascajelli\server\response;


abstract class ApiResponse extends Response implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $_headers;

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'status' => $this->getStatus(),
            'data' => $this->getData()
        ];
    }

    abstract protected function getStatus() : string;

    abstract protected function getData() : array;

    public function getHeaders() : array
    {
        return $this->_headers;
    }

    public function render()
    {
        header("Content-type: application/json;charset=utf-8");
        echo json_encode($this);
    }
}