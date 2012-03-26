<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Storage;

/**
 * Interface for service that stores current cart id.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartStorageInterface
{
    /**
     * Returns current cart id, used then to retrieve the cart.
     *
     * @return mixed
     */
    function getCurrentCartIdentifier();

    /**
     * Sets current cart id and persists it.
     *
     * @param mixed $identifier
     */
    function setCurrentCartIdentifier($identifier);
}
