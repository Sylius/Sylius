<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationRemoverInterface;

/**
 * Taxation remover.
 *
 * @author Piotr Walków <walkow.piotr@gmail.com>
 */
class TaxationRemover implements TaxationRemoverInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeTaxes(OrderInterface $order)
    {
        // Remove all tax adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        $order->calculateTotal();
    }
}
