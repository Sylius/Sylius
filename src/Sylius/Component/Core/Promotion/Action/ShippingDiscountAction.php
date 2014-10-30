<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Shipping discount action.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingDiscountAction implements PromotionActionInterface
{
    /**
     * Adjustment repository.
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $adjustment = $this->repository->createNew();
        $adjustment->setAmount(- $subject->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT) * $configuration['percentage']);
        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setDescription($promotion->getDescription());

        $subject->addAdjustment($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $subject->removeAdjustments(AdjustmentInterface::PROMOTION_ADJUSTMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_shipping_discount_configuration';
    }
}
