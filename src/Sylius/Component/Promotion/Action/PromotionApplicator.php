<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class PromotionApplicator implements PromotionApplicatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        $applyPromotion = false;
        foreach ($promotion->getActions() as $action) {
            $result = $this->getActionCommandByType($action->getType())->execute($subject, $action->getConfiguration(), $promotion);
            $applyPromotion |= $result;
        }

        if ($applyPromotion) {
            $subject->addPromotion($promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion): void
    {
        foreach ($promotion->getActions() as $action) {
            $this->getActionCommandByType($action->getType())->revert($subject, $action->getConfiguration(), $promotion);
        }

        $subject->removePromotion($promotion);
    }

    /**
     * @param string $type
     *
     * @return PromotionActionCommandInterface
     */
    private function getActionCommandByType(string $type): PromotionActionCommandInterface
    {
        return $this->registry->get($type);
    }
}
