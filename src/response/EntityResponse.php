<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/21/16
 * Time: 9:09 PM
 */

namespace nicolascajelli\server\response;

/**
 * @Doc {"abstract":"entityR"}
 */
class EntityResponse extends ApiResponse
{
    /**
     * @var \JsonSerializable
     */
    private $entity;

    /**
     * GameResponse constructor.
     */
    public function __construct(\JsonSerializable $entity)
    {
        $this->entity = $entity;
    }


    protected function getStatus() : string
    {
        return "ok";
    }

    protected function getData() : array
    {
        return $this->entity->jsonSerialize();
    }
}