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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingDiscountAction implements PromotionActionInterface
{
    const TYPE = 'shipping_discount';

    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $adjustment = $this->createAdjustment($promotion);
        $adjustmentAmount = (int) round($subject->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT) * $configuration['percentage']);
        $adjustment->setAmount(-$adjustmentAmount);

        $subject->addAdjustment($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface && !$subject instanceof OrderItemInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Core\Model\OrderInterface or Sylius\Component\Core\Model\OrderItemInterface'
            );
        }

        foreach ($subject->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_percentage_discount_configuration';
    }

    /**
     * @param PromotionInterface $promotion
     * @param string $type
     *
     * @return AdjustmentInterface
     */
    protected function createAdjustment(
        PromotionInterface $promotion,
        $type = AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT
    ) {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }
}
