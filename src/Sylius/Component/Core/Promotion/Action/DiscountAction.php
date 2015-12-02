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

use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\CoreBundle\EventListener\AdjustmentSubscriber;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base discount action.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
abstract class DiscountAction implements PromotionActionInterface
{
    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param OriginatorInterface $originator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->originator = $originator;
        $this->eventDispatcher = $eventDispatcher;
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

        foreach ($subject->getAdjustments(AdjustmentInterface::PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion === $this->originator->getOrigin($adjustment)) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return AdjustmentDTO
     */
    protected function createAdjustmentDTO(PromotionInterface $promotion)
    {
        $adjustmentDTO = new AdjustmentDTO();
        $adjustmentDTO->type = AdjustmentInterface::PROMOTION_ADJUSTMENT;
        $adjustmentDTO->description = $promotion->getDescription();
        $adjustmentDTO->originId = $promotion->getId();
        $adjustmentDTO->originType = get_class($promotion);

        return $adjustmentDTO;
    }

    /**
     * @param AdjustableInterface $subject
     * @param AdjustmentDTO       $adjustmentDTO
     */
    protected function addAdjustmentTo(AdjustableInterface $subject, AdjustmentDTO $adjustmentDTO)
    {
        $type = AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT;

        if ($subject instanceof OrderInterface) {
            $type = AdjustmentEvent::ADJUSTMENT_ADDING_ORDER;
        }

        $adjustmentEvent = new AdjustmentEvent($subject, [AdjustmentSubscriber::EVENT_ARGUMENT_DATA_KEY => $adjustmentDTO]);

        $this->eventDispatcher->dispatch($type, $adjustmentEvent);
    }
}
