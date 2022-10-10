<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\CreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionActionFactoryInterface;

trait TransformPromotionActionsAttributeTrait
{
    private PromotionActionFactoryInterface $promotionActionFactory;

    private function transformActionsAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        $actions = [];
        foreach ($attributes['actions'] as $action) {
            if (\is_array($action)) {
                /** @var CreateResourceEvent $event */
                $event = $eventDispatcher->dispatch(
                    new CreateResourceEvent(PromotionActionFactoryInterface::class, $action)
                );

                $action = $event->getResource();
            }

            $actions[] = $action;
        }

        $attributes['actions'] = $actions;

        return $attributes;
    }
}
