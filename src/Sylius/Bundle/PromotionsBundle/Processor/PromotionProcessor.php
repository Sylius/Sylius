<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Processor;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface;
use Sylius\Bundle\PromotionsBundle\Checker\PromotionEliglibilityCheckerInterface;
use Sylius\Bundle\PromotionsBundle\Action\PromotionApplicatorInterface;

/**
 * Process all active promotions.
 *
 * Checks all rules and applies configured actions if rules are eigible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessor implements PromotionProcessorInterface
{
    protected $repository;
    protected $checker;
    protected $applicator;

    public function __construct(PromotionRepositoryInterface $repository, PromotionEliglibilityCheckerInterface $checker, PromotionApplicatorInterface $applicator)
    {
        $this->repository = $repository;
        $this->checker = $checker;
        $this->applicator = $applicator;
    }

    public function process(OrderInterface $order)
    {
        foreach ($this->repository->findActive() as $promotion) {
            if ($this->checker->isEligible($order, $promotion)) {
                $this->applicator->apply($order, $promotion);
            }
        }
    }
}
