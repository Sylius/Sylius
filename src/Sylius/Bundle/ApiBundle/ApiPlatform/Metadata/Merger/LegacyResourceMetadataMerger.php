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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger;

final class LegacyResourceMetadataMerger implements MetadataMergerInterface
{
    public function merge(array $oldMetadata, array $newMetadata): array
    {
        if ([] === $newMetadata || [] === $oldMetadata) {
            return [] === $newMetadata ? $oldMetadata : $newMetadata;
        }

        foreach ($newMetadata as $key => $value) {
            if ('properties' === $key) {
                foreach ($value as $keyProperty => $property) {
                    $oldMetadata[$key][$keyProperty] = $property;
                }

                continue;
            }

            if (is_array($value) && str_contains($key, 'Operations')) {
                foreach ($value as $operationKey => $operationData) {
                    if (isset($operationData['enabled']) && false === $operationData['enabled']) {
                        unset($oldMetadata[$key][$operationKey]);

                        continue;
                    }

                    $oldMetadata[$key][$operationKey] = array_merge(
                        $oldMetadata[$key][$operationKey] ?? [],
                        $operationData,
                    );
                }

                continue;
            }

            $oldMetadata[$key] = $value;
        }

        return $oldMetadata;
    }
}
