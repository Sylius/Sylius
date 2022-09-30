<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionActionFactoryInterface;

trait TransformCatalogPromotionActionsAttributeTrait
{
    private CatalogPromotionActionFactoryInterface $catalogPromotionActionFactory;

    private function transformActionsAttribute(array $attributes): array
    {
        $actions = [];
        foreach ($attributes['actions'] as $action) {
            if (\is_array($action)) {
                $action = $this->catalogPromotionActionFactory::new()->withAttributes($action)->create();
            }

            $actions[] = $action;
        }

        $attributes['actions'] = $actions;

        return $attributes;
    }
}
