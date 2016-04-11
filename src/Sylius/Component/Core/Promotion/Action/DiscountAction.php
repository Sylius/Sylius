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
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

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
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$this->isSubjectValid($subject)) {
            return;
        }

        foreach ($subject->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
                    if ($promotion === $this->originator->getOrigin($adjustment)) {
                        $unit->removeAdjustment($adjustment);
                    }
                }
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
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        if (0 === $subject->countItems()) {
            return false;
        }

        return true;
    }

    /**
     * @param array $configuration
     */
    protected abstract function isConfigurationValid(array $configuration);
}
