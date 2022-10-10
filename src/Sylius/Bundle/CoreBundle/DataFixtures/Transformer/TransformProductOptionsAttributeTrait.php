<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateProductOptionTrait;

trait TransformProductOptionsAttributeTrait
{
    use FindOrCreateProductOptionTrait;

    private function transformProductOptionsAttribute(EventDispatcherInterface $eventDispatcher, array $attributes): array
    {
        $productOptions = [];
        foreach ($attributes['product_options'] as $productOption) {
            if (\is_string($productOption)) {
                $productOption = $this->findOrCreateProductOption($eventDispatcher, ['code' => $productOption]);
            }
            $productOptions[] = $productOption;
        }
        $attributes['product_options'] = $productOptions;

        return $attributes;
    }
}
