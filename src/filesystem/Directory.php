<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/26/16
 * Time: 5:52 PM
 */

namespace nicolascajelli\server\filesystem;


class Directory
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function file($fileName) : File
    {
        if (is_file($this->path . '/' . $fileName)) {
            return new File($this->path . '/' . $fileName);
        } else {
            throw new \InvalidArgumentException("File not found.");
        }
    }
}