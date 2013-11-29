<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Action\Registry\PromotionActionRegistryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

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
    }
}
