<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

/**
 * Resource controller configuration factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ConfigurationFactory
{
    /**
     * Default Settings.
     *
     * @var array
     */
    protected $settings;

    /**
     * Constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Create configuration for given parameters.
     *
     * @param string $bundlePrefix
     * @param string $resourceName
     * @param string $templateNamespace
     * @param string $templatingEngine
     *
     * @return Configuration
     */
    public function createConfiguration($bundlePrefix, $resourceName, $templateNamespace, $templatingEngine = 'twig')
    {
        return new Configuration(
            $bundlePrefix,
            $resourceName,
            $templateNamespace,
            $templatingEngine,
            $this->settings
        );
    }
}
