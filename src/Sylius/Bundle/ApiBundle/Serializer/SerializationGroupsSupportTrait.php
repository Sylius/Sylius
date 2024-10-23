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

namespace Sylius\Bundle\ApiBundle\Serializer;

trait SerializationGroupsSupportTrait
{
    private function supportsSerializationGroups(array $context, array $serializationGroups): bool
    {
        $groups = $context['groups'] ?? [];
        if (is_string($groups)) {
            $groups = [$groups];
        }

        return (bool) array_intersect($serializationGroups, $groups);
    }
}
