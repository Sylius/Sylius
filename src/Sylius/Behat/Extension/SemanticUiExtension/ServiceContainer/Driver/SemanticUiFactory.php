<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SemanticUiExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SemanticUiFactory implements DriverFactory
{
    /**
     * @var Selenium2Factory
     */
    private $selenium2Factory;

    /**
     * @param Selenium2Factory $selenium2Factory
     */
    public function __construct(Selenium2Factory $selenium2Factory)
    {
        $this->selenium2Factory = $selenium2Factory;
    }

    /**
     * Gets the name of the driver being configured.
     *
     * This will be the key of the configuration for the driver.
     *
     * @return string
     */
    public function getDriverName()
    {
        return 'semantic_ui';
    }

    /**
     * Defines whether a session using this driver is eligible as default javascript session
     *
     * @return boolean
     */
    public function supportsJavascript()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $this->selenium2Factory->configure($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        return new Definition(
            'Sylius\Behat\Extension\SemanticUiExtension\ServiceContainer\Driver\SemanticUiDriver',
            [
                $this->selenium2Factory->buildDriver($config)
            ]
        );
    }
}
