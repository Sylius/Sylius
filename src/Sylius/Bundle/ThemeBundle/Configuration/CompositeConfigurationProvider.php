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

namespace Sylius\Bundle\ThemeBundle\Configuration;

final class CompositeConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var ConfigurationProviderInterface[]
     */
    private $configurationProviders;

    /**
     * @param ConfigurationProviderInterface[] $configurationProviders
     */
    public function __construct(array $configurationProviders)
    {
        $this->configurationProviders = $configurationProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurations(): array
    {
        $configurations = [];
        foreach ($this->configurationProviders as $configurationProvider) {
            $configurations = array_merge(
                $configurations,
                $configurationProvider->getConfigurations()
            );
        }

        return $configurations;
    }
}
