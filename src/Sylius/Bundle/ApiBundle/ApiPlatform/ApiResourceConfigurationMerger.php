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

namespace Sylius\Bundle\ApiBundle\ApiPlatform;

final class ApiResourceConfigurationMerger implements ApiResourceConfigurationMergerInterface
{
    public function mergeConfigs(...$configs): array
    {
        $resultingConfig = [];

        foreach ($configs as $config) {
            foreach ($config as $newKey => $newValue) {
                if ($this->isDisabled($newKey, $newValue)) {
                    unset($resultingConfig[$newKey]);
                    if (['enabled' => false] === $newValue) {
                        continue;
                    }

                    unset($newValue['enabled']);
                }

                if (is_int($newKey)) {
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

    private function isDisabled($key, $values): bool
    {
        return is_string($key) && is_array($values) && isset($values['enabled']) && $values['enabled'] === false;
    }
}
