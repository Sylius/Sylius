<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface NewResourceFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param FactoryInterface $factory
     *
     * @return ResourceInterface
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory);
}
