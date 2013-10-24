<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:25
 */

namespace Phpforce\Metadata\Cache;

interface CacheInterface
{
    public function set($sobjectType);

    public function get($sobjectType);
}