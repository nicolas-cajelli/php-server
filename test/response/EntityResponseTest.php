<?php
namespace nicolascajelli\server\response;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/1/16
 * Time: 9:27 PM
 */
class EntityResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $elementClass = new class  implements \JsonSerializable {
            function jsonSerialize()
            {
                return ["Element"];
            }
        };
        $element = new $elementClass();

        $response = new EntityResponse($element);
        $result = json_encode($response);
        $expected = '{"status":"ok","data":["Element"]}';
        $this->assertEquals($expected, $result);
    }
}