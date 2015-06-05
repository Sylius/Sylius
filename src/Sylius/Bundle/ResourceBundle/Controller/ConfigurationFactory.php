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
     * @var ParametersParser
     */
    protected $parametersParser;

    /**
     * Default Settings
     *
     * @var array
     */
    protected $settings;

    /**
     * @var string
     */
    protected $configurationClass;

    /**
     * Constructor.
     *
     * @param ParametersParser $parametersParser
     * @param string           $configurationClass
     * @param array            $settings
     */
    public function __construct(ParametersParser $parametersParser, $configurationClass, array $settings)
    {
        $this->settings = $settings;
        $this->parametersParser = $parametersParser;
        $this->configurationClass = $configurationClass;
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
        return new $this->configurationClass(
            $this->parametersParser,
            $bundlePrefix,
            $resourceName,
            $templateNamespace,
            $templatingEngine,
            $this->settings
        );
    }
}
