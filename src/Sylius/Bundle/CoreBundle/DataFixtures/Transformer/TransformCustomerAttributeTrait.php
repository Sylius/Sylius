<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;

trait TransformCustomerAttributeTrait
{
    private CustomerFactoryInterface $customerFactory;

    private function transformCustomerAttribute(array $attributes, string $attributeKey = 'customer'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $attributes[$attributeKey] = $this->customerFactory::findOrCreate(['email' => $attributes[$attributeKey]]);
        }

        return $attributes;
    }
}
