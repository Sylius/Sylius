<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Loader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ConfigurationProcessingLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $decoratedLoader;

    /**
     * @var ConfigurationProcessorInterface
     */
    private $configurationProcessor;

    /**
     * @param LoaderInterface $decoratedLoader
     * @param ConfigurationProcessorInterface $configurationProcessor
     */
    public function __construct(LoaderInterface $decoratedLoader, ConfigurationProcessorInterface $configurationProcessor)
    {
        $this->decoratedLoader = $decoratedLoader;
        $this->configurationProcessor = $configurationProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function load($identifier)
    {
        $rawConfiguration = $this->decoratedLoader->load($identifier);

        $configurations = [$rawConfiguration];
        if (isset($rawConfiguration['extra']['sylius-theme'])) {
            $configurations[] = $rawConfiguration['extra']['sylius-theme'];
        }

        return $this->configurationProcessor->process($configurations);
    }
}
