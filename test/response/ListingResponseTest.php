<?php
namespace nicolascajelli\server\response;
use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/1/16
 * Time: 9:27 PM
 */
class ListingResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $elementClass = new class  implements \JsonSerializable {
            function jsonSerialize()
            {
                return "Element";
            }
        };
        $element = new $elementClass();

        $response = new ListingResponse();
        $response->add($element);
        $response->setTotalCount(1);
        $result = json_encode($response);
        $expected = '{"status":"ok","data":{"docs":["Element"],"next_page":null,"total_count":1}}';
        $this->assertEquals($expected, $result);
    }
}