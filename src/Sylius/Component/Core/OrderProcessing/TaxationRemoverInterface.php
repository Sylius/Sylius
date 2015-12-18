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
 * Order taxation remover.
 *
 * Service implementing this service should remove taxes from given order.
 *
 * @author Piotr Walków <walkow.piotr@gmailcom>
 */
interface TaxationRemoverInterface
{
    /**
     * Remove taxes from given order.
     *
     * @param OrderInterface $order
     */
    public function removeTaxes(OrderInterface $order);
}
