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
use Sylius\Core\Model\OrderItemUnitInterface;
use Sylius\Originator\Originator\OriginatorInterface;
use Sylius\Promotion\Action\PromotionActionInterface;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
abstract class DiscountAction implements PromotionActionInterface
{
    /**
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @param OriginatorInterface $originator
     */
    public function __construct(OriginatorInterface $originator)
    {
        $this->originator = $originator;
    }

    /**
     * @param array $configuration
     */
    abstract protected function isConfigurationValid(array $configuration);

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$this->isSubjectValid($subject)) {
            return;
        }

        foreach ($subject->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                $this->removeUnitOrderPromotionAdjustmentsByOrigin($unit, $promotion);
            }
        }
    }

    /**
     * @param PromotionSubjectInterface $subject
     *
     * @return bool
     */
    protected function isSubjectValid(PromotionSubjectInterface $subject)
    {
        Assert::implementsInterface($subject, OrderInterface::class);

        return 0 !== $subject->countItems();
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param PromotionInterface $promotion
     */
    private function removeUnitOrderPromotionAdjustmentsByOrigin(OrderItemUnitInterface $unit, PromotionInterface $promotion)
    {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion === $this->originator->getOrigin($adjustment)) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }
}
