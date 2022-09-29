<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Component\Core\Formatter\StringInflector;

trait TransformNameToCodeAttributeTrait
{
    private function transformNameToCodeAttribute(array $attributes): array
    {
        $attributes['code'] = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);

        return $attributes;
    }
}
