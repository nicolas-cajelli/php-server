<?php
namespace nicolascajelli\server\request;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/1/16
 * Time: 10:45 PM
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testParseRequest()
    {
        $_SERVER['REQUEST_URI'] = "/uri/?a=1&b=2";
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['a'] = 1;
        $tmpFile = '/tmp/test_input_' . time();
        file_put_contents($tmpFile, '{}');
        $request = new Request($tmpFile);

        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals('/uri', $request->getUri());
        $this->assertEquals([], $request->getPayload());
        $this->assertEquals(1, $request->get('a'));
        unlink($tmpFile);
    }
}