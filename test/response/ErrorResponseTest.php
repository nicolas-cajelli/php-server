<?php
namespace nicolascajelli\server\response;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/1/16
 * Time: 9:27 PM
 */
class ErrorResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $response = new ErrorResponse();
        $response->setData('Error', '442');
        $result = json_encode($response);
        $expected = '{"status":"error","data":{"message":"Error"}}';
        $this->assertEquals($expected, $result);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRender()
    {
        $response = new ErrorResponse();
        $response->setData('Error', '442');
        $response->render();
        $headers = xdebug_get_headers();
        $expected = [
            "Content-type: application/json;charset=utf-8"
        ];
        $this->assertEquals($expected, $headers);
        $this->assertEquals(442, http_response_code());
    }
}