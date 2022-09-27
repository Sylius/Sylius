<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

class AdminUserTransformer implements AdminUserTransformerInterface
{
    public function transform(array $attributes): array
    {
        return $attributes;
    }
}
