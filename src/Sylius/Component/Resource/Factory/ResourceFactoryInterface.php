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
 * Resource factory should be a service, which initializes new instance of a resource.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourceFactoryInterface
{
    /**
     * Create a new resource
     *
     * @return mixed
     */
    public function createNew();
}
