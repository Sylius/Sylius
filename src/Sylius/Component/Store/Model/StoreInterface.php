<?php

namespace Sylius\Component\Store\Model;


use Sylius\Component\Scope\ScopeInterface;

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