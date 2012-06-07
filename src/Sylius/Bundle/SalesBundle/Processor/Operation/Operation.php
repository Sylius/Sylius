<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Processor\Operation;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Base operation.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Operation implements OperationInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare(OrderInterface $order)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(OrderInterface $order)
    {
    }
}
