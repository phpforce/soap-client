<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 23.10.13
 * Time: 16:16
 */

namespace Phpforce\SoapClient;

class EnterpriseClient extends Client
{
    /**
     * {@inheritdoc}
     */
    public function query($query)
    {
        $result = $this->call(
            'query',
            array('queryString' => $query)
        );

        return new Result\RecordIterator($this, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function queryAll($query)
    {
        $result = $this->call(
            'queryAll',
            array('queryString' => $query)
        );

        return new Result\RecordIterator($this, $result);
    }
} 