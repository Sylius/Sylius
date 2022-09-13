<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

class AdminUserFactoryTransformer implements AdminUserFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        return $attributes;
    }
}
