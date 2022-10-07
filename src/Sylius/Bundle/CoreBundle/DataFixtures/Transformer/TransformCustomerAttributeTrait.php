<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateCustomerTrait;

trait TransformCustomerAttributeTrait
{
    use FindOrCreateCustomerTrait;

    private function transformCustomerAttribute(array $attributes, string $attributeKey = 'customer'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $attributes[$attributeKey] = $this->findOrCreateCustomer(['email' => $attributes[$attributeKey]]);
        }

        return $attributes;
    }
}
