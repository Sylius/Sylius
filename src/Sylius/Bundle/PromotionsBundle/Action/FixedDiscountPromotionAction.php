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

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Creates adjustment and adds it to given order.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountPromotionAction implements PromotionActionInterface
{
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(OrderInterface $order, array $configuration)
    {
        $adjustment = $this->repository->createNew();
        $adjustment->setAmount($configuration['amount']);

        $order->addAdjustment($adjustment);
    }

    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_fixed_discount_configuration';
    }
}
