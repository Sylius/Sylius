<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\StateResolver;

use Sylius\Component\Order\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CompositeOrderStateResolver implements StateResolverInterface
{
    /**
     * @var PriorityQueue|StateResolverInterface[]
     */
    private $stateResolvers;

    public function __construct()
    {
        $this->stateResolvers = new PriorityQueue();
    }

    /**
     * @param StateResolverInterface $stateResolver
     * @param int $priority
     */
    public function addResolver(StateResolverInterface $stateResolver, $priority = 0)
    {
        $this->stateResolvers->insert($stateResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        foreach ($this->stateResolvers as $stateResolver) {
            $stateResolver->resolve($order);
        }
    }
}
