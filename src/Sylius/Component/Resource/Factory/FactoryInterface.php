<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Factory;

/**
 * Should be implemented by services responsible for creating new instance of a resource.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface FactoryInterface
{
    /**
     * Create a new resource.
     *
     * @return object
     */
    public function createNew();
}
