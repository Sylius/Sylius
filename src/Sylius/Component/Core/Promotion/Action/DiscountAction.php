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
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * Base discount action.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
abstract class DiscountAction implements PromotionActionInterface
{
    /**
     * @var DomainManagerInterface
     */
    protected $manager;

    /**
     * @var OriginatorInterface
     */
    protected $originator;

    public function __construct(DomainManagerInterface $manager, OriginatorInterface $originator)
    {
        $this->manager = $manager;
        $this->originator = $originator;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        $subject = $this->supports($subject);

        foreach ($subject->getAdjustments(AdjustmentInterface::PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion === $this->originator->getOrigin($adjustment)) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return AdjustmentInterface
     */
    protected function createAdjustment(PromotionInterface $promotion)
    {
        $adjustment = $this->manager->createNew();
        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setDescription($promotion->getDescription());

        $this->originator->setOrigin($adjustment, $promotion);

        return $adjustment;
    }

    /**
     * @param object $subject
     *
     * @return OrderInterface|OrderItemInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function supports($subject)
    {
        if (!$subject instanceof OrderInterface && !$subject instanceof OrderItemInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Core\Model\OrderInterface or Sylius\Component\Core\Model\OrderItemInterface'
            );
        }

        return $subject;
    }
}
