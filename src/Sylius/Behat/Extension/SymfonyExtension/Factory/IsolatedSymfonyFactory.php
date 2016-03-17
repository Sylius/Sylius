<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SymfonyExtension\Factory;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Behat\Symfony2Extension\Driver\KernelDriver;
use Sylius\Behat\Extension\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class IsolatedSymfonyFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'symfony2';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        $this->assertMinkBrowserKitDriverIsAvailable();

        return new Definition(KernelDriver::class, [
            new Reference(SymfonyExtension::DRIVER_KERNEL_ID),
            '%mink.base_url%',
        ]);
    }

    /**
     * @throws \RuntimeException If MinkBrowserKitDriver is not available
     */
    private function assertMinkBrowserKitDriverIsAvailable()
    {
        if (!class_exists(BrowserKitDriver::class)) {
            throw new \RuntimeException('Install MinkBrowserKitDriver in order to use the symfony2 driver.');
        }
    }
}
