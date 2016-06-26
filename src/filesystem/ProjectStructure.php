<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/26/16
 * Time: 5:44 PM
 */

namespace nicolascajelli\server\filesystem;


class ProjectStructure
{
    protected $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function cd($dir, $create = false) : Directory
    {
        $path = $this->basePath . '/' . $dir;
        if (! is_dir($path)) {
            if (! $create) {
                throw new \InvalidArgumentException("Directory $dir doesn't exists.");
            } else {
                mkdir($path);
            }
        }
        return new Directory($path);
    }
}