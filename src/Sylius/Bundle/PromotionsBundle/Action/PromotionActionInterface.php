<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Action;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Executes promotion action on given order.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionActionInterface
{
    public function execute(OrderInterface $order, array $configuration);
    public function getConfigurationFormType();
}
