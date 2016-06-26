<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/22/16
 * Time: 9:39 PM
 */

namespace nicolascajelli\server;


class ScalarArgument
{
    protected $value;

    /**
     * ScalarArgument constructor.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}