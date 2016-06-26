<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/12/16
 * Time: 7:02 PM
 */

namespace nicolascajelli\server\request;


class Request
{
    protected $_host;
    protected $_method;
    protected $_uri;

    public function __construct()
    {
        $this->_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->_host = $_SERVER['SERVER_NAME'];

        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $this->_uri = rtrim($uri, '/');
    }

    public function getUri() : string
    {
        return $this->_uri;
    }

    public function getMethod() : string
    {
        return $this->_method;
    }

    public function getPayload()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function get($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
}