<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Component\Product\Generator\SlugGeneratorInterface;

trait TransformNameToSlugAttributeTrait
{
    private SlugGeneratorInterface $slugGenerator;

    private function transformNameToSlugAttribute(array $attributes): array
    {
        $attributes['slug'] = $attributes['slug'] ?: $this->slugGenerator->generate($attributes['name']);

        return $attributes;
    }
}
