<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * Order taxation processor.
 * Service implementing this service should apply taxes to given order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxationProcessorInterface
{
    /**
     * Apply taxes to given order.
     *
     * @param OrderInterface $order
     */
    public function applyTaxes(OrderInterface $order);
}
