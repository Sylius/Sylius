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

use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Applies all registered promotion actions to given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicator implements PromotionApplicatorInterface
{
    protected $registry;

    public function __construct(PromotionActionRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        foreach ($promotion->getActions() as $action) {
            $this->registry
                ->getAction($action->getType())
                ->execute($subject, $action->getConfiguration(), $promotion)
            ;
        }

        $subject->addPromotion($promotion);
    }

    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        foreach ($promotion->getActions() as $action) {
            $this->registry
                ->getAction($action->getType())
                ->revert($subject, $action->getConfiguration(), $promotion)
            ;
        }

        $subject->removePromotion($promotion);
    }
}
