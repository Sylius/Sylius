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

namespace Tests\Sylius\Bundle\CoreBundle\Grid\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Component\Grid\Provider\ArrayGridProvider;

/**
 * This class is there to ensure that the yaml configuration of the grid is the same as the php configuration
 */
final class OverridenGridsTest extends KernelTestCase
{
    public function testPhpGridsHavingTheSameConfigurationAsYAMLGrids(): void
    {
        $gridConfiguration = self::getContainer()->getParameter('sylius_core.grids_configuration')['grids'] ?? [];

        $container = self::getContainer();
        $container->set(CustomerContextInterface::class, $this->createMock(CustomerContextInterface::class));
        $container->set(ChannelContextInterface::class, $this->createMock(ChannelContextInterface::class));

        $arrayProvider = $container->get(ArrayGridProvider::class);
        $serviceProvider = $container->get(ServiceGridProvider::class);

        foreach (array_keys($gridConfiguration) as $gridName) {
            $yamlVersion = $arrayProvider->get($gridName);
            $serviceVersion = $serviceProvider->get($gridName);

            $this->assertEquals($yamlVersion, $serviceVersion);
        }
    }
}

