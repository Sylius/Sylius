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

namespace Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;

final class ProcessingConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var ConfigurationLoaderInterface */
    private $decoratedLoader;

    /** @var ConfigurationProcessorInterface */
    private $configurationProcessor;

    public function __construct(ConfigurationLoaderInterface $decoratedLoader, ConfigurationProcessorInterface $configurationProcessor)
    {
        $this->decoratedLoader = $decoratedLoader;
        $this->configurationProcessor = $configurationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $identifier): array
    {
        $rawConfiguration = $this->decoratedLoader->load($identifier);

        $configurations = [$rawConfiguration];
        if (isset($rawConfiguration['extra']['sylius-theme'])) {
            $configurations[] = $rawConfiguration['extra']['sylius-theme'];
        }

        return $this->configurationProcessor->process($configurations);
    }
}
