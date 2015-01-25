<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Store\Model;


use Sylius\Component\Scope\ScopeInterface;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
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