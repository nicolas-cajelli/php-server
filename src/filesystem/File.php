<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/26/16
 * Time: 5:54 PM
 */

namespace nicolascajelli\server\filesystem;


class File
{
    protected $path;
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function requireContent()
    {
        return require $this->path;
    }
}