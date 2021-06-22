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

namespace Sylius\Bundle\ApiBundle\ApiPlatform;

/** @experimental */
final class ApiResourceConfigurationMerger implements ApiResourceConfigurationMergerInterface
{
    public function mergeConfigs(...$configs): array
    {
        $resultingConfig = [];

        foreach ($configs as $config) {
            foreach ($config as $newKey => $newValue) {
                if ($this->containsUnset($newKey, $matches)) {
                    [, $newKey] = $matches;

                    unset($resultingConfig[$newKey]);
                    if (null === $newValue) {
                        continue;
                    }
                }

                if (is_integer($newKey)) {
                    $resultingConfig[] = $newValue;

                    continue;
                }

                if (isset($resultingConfig[$newKey]) && is_array($resultingConfig[$newKey]) && is_array($newValue)) {
                    $resultingConfig[$newKey] = $this->mergeConfigs($resultingConfig[$newKey], $newValue);

                    continue;
                }

                $resultingConfig[$newKey] = $newValue;
            }
        }

        return $resultingConfig;
    }

    private function containsUnset($key, &$matches): bool
    {
        return is_string($key) && 1 === preg_match('/^(.*[^ ]) +\\(unset\\)$/', $key, $matches);
    }
}
