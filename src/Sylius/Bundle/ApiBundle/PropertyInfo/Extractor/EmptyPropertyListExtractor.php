<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\PropertyInfo\Extractor;

use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

final class EmptyPropertyListExtractor implements PropertyListExtractorInterface
{
    public function getProperties($class, array $context = []): ?array
    {
        if (class_exists($class)) {
            return [];
        }

        return null;
    }
}
