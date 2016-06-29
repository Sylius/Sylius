<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ResourceBundle\Controller;

use Sylius\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface StateMachineInterface
{
    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     */
    public function apply(RequestConfiguration $configuration, ResourceInterface $resource);
}
