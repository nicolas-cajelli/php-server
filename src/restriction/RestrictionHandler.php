<?php
namespace nicolascajelli\server\restriction;
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 7/6/16
 * Time: 8:48 PM
 */
interface RestrictionHandler
{

    public function isAllowed() : bool;
}