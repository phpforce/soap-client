<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:25
 */

namespace Phpforce\Metadata\Cache;

use Phpforce\SoapClient\Result\DescribeSObjectResult;

interface CacheInterface
{
    public function set(DescribeSObjectResult $sobjectType);

    public function get($sobjectType);
}