<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\FindOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactoryInterface;

trait TransformProductOptionsAttributeTrait
{
    private EventDispatcherInterface $eventDispatcher;

    private function transformProductOptionsAttribute(array $attributes): array
    {
        $productOptions = [];
        foreach ($attributes['product_options'] as $productOption) {
            if (\is_string($productOption)) {
                /** @var FindOrCreateResourceEvent $event */
                $event = $this->eventDispatcher->dispatch(
                    new FindOrCreateResourceEvent(ProductOptionFactoryInterface::class, ['code' => $productOption])
                );

                $productOption = $event->getResource();
            }
            $productOptions[] = $productOption;
        }
        $attributes['product_options'] = $productOptions;

        return $attributes;
    }
}
