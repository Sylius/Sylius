<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlexibleRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration)
    {
        $firstItemCost = $configuration['first_item_cost'];
        $additionalItemCost = $configuration['additional_item_cost'];
        $additionalItemLimit = $configuration['additional_item_limit'];

        $totalItems = $subject->getShippingItemCount();
        $additionalItems = $totalItems - 1;

        if (0 !== $additionalItemLimit) {
            $additionalItems = $additionalItemLimit >= $additionalItems ? $additionalItems : $additionalItemLimit;
        }

        return (int)($firstItemCost + ($additionalItems * $additionalItemCost));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'flexible_rate';
    }
}
