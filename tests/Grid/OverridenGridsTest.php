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

namespace Sylius\Tests\Grid;

use PHPUnit\Framework\Attributes\DataProvider;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Component\Grid\Provider\ArrayGridProvider;

/**
 * This class is there to ensure that the yaml configuration of the grid is the same as the php configuration
 */
final class OverridenGridsTest extends KernelTestCase
{
    #[DataProvider('dataProviderGrids')]
    public function testPhpGridsHavingTheSameConfigurationAsYAMLGrids(string $gridName): void
    {
        $container = self::getContainer();

        $arrayProvider = $container->get(ArrayGridProvider::class);
        $serviceProvider = $container->get(ServiceGridProvider::class);

        try {
            $serviceVersion = $serviceProvider->get($gridName);
        } catch (UndefinedGridException) {
            $this->markTestSkipped($gridName. ' is not migrated yet');
        }
        $yamlVersion = $arrayProvider->get($gridName);

        $this->assertEquals($yamlVersion, $serviceVersion);
    }

    public static function dataProviderGrids(): \Generator
    {
        $gridConfiguration = self::getContainer()->getParameter('sylius.grids_definitions');
        self::assertNotEmpty($gridConfiguration, 'No grid configuration found');

        foreach (array_keys($gridConfiguration) as $gridName) {
            yield $gridName => [$gridName];
        }
    }
}

