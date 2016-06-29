<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Action;

use Sylius\Core\Model\AdjustmentInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemInterface;
use Sylius\Originator\Originator\OriginatorInterface;
use Sylius\Promotion\Action\PromotionActionInterface;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

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
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param OriginatorInterface $originator
     */
    public function __construct(FactoryInterface $adjustmentFactory, OriginatorInterface $originator)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->originator = $originator;
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
                'Sylius\Core\Model\OrderInterface or Sylius\Core\Model\OrderItemInterface'
            );
        }

        foreach ($subject->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion === $this->originator->getOrigin($adjustment)) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_shipping_discount_configuration';
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
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());

        $this->originator->setOrigin($adjustment, $promotion);

        return $adjustment;
    }
}
