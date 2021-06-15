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
class ConfigMergeManager
{
    public function mergeConfigs(...$configs): array
    {
        $resultingConfig = [];

        foreach ($configs as $config) {
            foreach ($config as $newKey => $newValue) {
                $unsetNewKey = false;
                if (is_string($newKey) && 1 === preg_match('/^(.*[^ ]) +\\(unset\\)$/', $newKey, $matches)) {
                    [, $newKey] = $matches;
                    $unsetNewKey = true;
                }

                if ($unsetNewKey) {
                    unset($resultingConfig[$newKey]);

                    if (null === $newValue) {
                        continue;
                    }
                }

                if (is_integer($newKey)) {
                    $resultingConfig[] = $newValue;
                } elseif (isset($resultingConfig[$newKey]) && is_array($resultingConfig[$newKey]) && is_array($newValue)) {
                    $resultingConfig[$newKey] = $this->mergeConfigs($resultingConfig[$newKey], $newValue);
                } else {
                    $resultingConfig[$newKey] = $newValue;
                }
            }
        }

        return $resultingConfig;
    }
}
