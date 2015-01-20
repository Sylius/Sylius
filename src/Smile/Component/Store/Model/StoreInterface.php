<?php

namespace Smile\Component\Store\Model;


use Smile\Component\Scope\ScopeInterface;

interface StoreInterface extends ScopeInterface
{
    /**
     * Get store url
     * @return string
     */
    public function getUrl();


    /**
     * Set store url
     * @param string $url
     * @return self
     */
    public function setUrl($url);
}