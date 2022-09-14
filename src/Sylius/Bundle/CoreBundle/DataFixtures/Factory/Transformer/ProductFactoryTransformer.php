<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;

final class ProductFactoryTransformer implements ProductFactoryTransformerInterface
{
    public function __construct(private SlugGeneratorInterface $slugGenerator)
    {
    }

    public function transform(array $attributes): array
    {
        $attributes['code'] = $attributes['code'] ?: StringInflector::nameToCode($attributes['name']);
        $attributes['slug'] = $attributes['slug'] ?: $this->slugGenerator->generate($attributes['name']);

        return $attributes;
    }
}
