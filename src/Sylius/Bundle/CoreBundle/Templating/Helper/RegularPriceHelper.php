<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegularPriceHelper extends Helper
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return int
     */
    public function getRegularPrice(OrderItemInterface $orderItem)
    {
        return
            $orderItem->getUnitPrice() * $orderItem->getQuantity() +
            $orderItem->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_regular_price';
    }
}
