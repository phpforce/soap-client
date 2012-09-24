<?php

namespace Phpforce\SoapClient\Result;

class DescribeTab
{
    protected $custom;

    protected $iconUrl;

    protected $label;

    protected $miniIconUrl;

    protected $sobjectName;

    protected $url;

    public function getCustom()
    {
        return $this->custom;
    }

    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getMiniIconUrl()
    {
        return $this->miniIconUrl;
    }

    public function getSobjectName()
    {
        return $this->sobjectName;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
