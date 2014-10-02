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
use Sylius\Component\Core\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Base discount action.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
abstract class DiscountAction implements PromotionActionInterface
{
    protected $adjustmentRepository;
    protected $originator;

    public function __construct(RepositoryInterface $adjustmentRepository, OriginatorInterface $originator)
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->originator = $originator;
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
            if ($promotion  === $this->originator->getOrigin($adjustment)) {
                $subject->removeAdjustment($adjustment);
            }
        }
    }

    protected function createAdjustment(PromotionInterface $promotion)
    {
        $adjustment = $this->adjustmentRepository->createNew();

        $adjustment->setLabel(AdjustmentInterface::PROMOTION_ADJUSTMENT);
        $adjustment->setDescription($promotion->getDescription());

        $this->originator->setOrigin($adjustment, $promotion);

        return $adjustment;
    }
}
