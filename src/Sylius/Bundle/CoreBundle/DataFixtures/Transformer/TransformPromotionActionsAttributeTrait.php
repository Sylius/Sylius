<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionActionFactoryInterface;

trait TransformPromotionActionsAttributeTrait
{
    private PromotionActionFactoryInterface $promotionActionFactory;

    private function transformActionsAttribute(array $attributes): array
    {
        $actions = [];
        foreach ($attributes['actions'] as $action) {
            if (\is_array($action)) {
                $action = $this->promotionActionFactory::new()->withAttributes($action)->create();
            }

            $actions[] = $action;
        }

        $attributes['actions'] = $actions;

        return $attributes;
    }
}
