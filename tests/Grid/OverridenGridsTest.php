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
use Sylius\Bundle\GridBundle\Provider\ServiceGridProvider;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\ArrayGridProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
            $this->markTestSkipped($gridName . ' is not migrated yet');
        }
        $yamlVersion = $arrayProvider->get($gridName);

        $this->prefillingRepository($yamlVersion);
        $this->prefillingGridFields($yamlVersion);
        $this->prefillingGridActions($yamlVersion);

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

    /**
     * Prefilling the default values for datetime fields as they might not be set in YAML configuration
     */
    private function prefillingGridFields(Grid $grid): void
    {
        foreach ($grid->getFields() as $field) {
            // Prefilling the default values for datetime fields as they might not be set in YAML configuration
            if ($field->getType() === 'datetime') {
                $options = [
                    'format' => 'Y-m-d H:i:s',
                    'timezone' => null,
                    ...$field->getOptions(),
                ];
                $field->setOptions($options);
            }
        }
    }

    /**
     * Prefilling the default values on Grid definitions.
     * PHP Configuration add a default label, not set in YAML configuration
     */
    private function prefillingGridActions(Grid $grid): void
    {
        if ($grid->hasActionGroup('main')) {
            foreach ($grid->getActions('main') as $action) {
                if ('create' === $action->getType() && null === $action->getLabel()) {
                    $action->setLabel('sylius.ui.create');
                }
            }
        }

        if ($grid->hasActionGroup('item')) {
            foreach ($grid->getActions('item') as $action) {
                if ('show' === $action->getType() && null === $action->getLabel()) {
                    $action->setLabel('sylius.ui.show');
                }

                if ('update' === $action->getType() && null === $action->getLabel()) {
                    $action->setLabel('sylius.ui.edit');
                }

                if ('delete' === $action->getType() && null === $action->getLabel()) {
                    $action->setLabel('sylius.ui.delete');
                }
            }
        }

        if ($grid->hasActionGroup('bulk')) {
            foreach ($grid->getActions('bulk') as $action) {
                if ('delete' === $action->getType() && null === $action->getLabel()) {
                    $action->setLabel('sylius.ui.delete');
                }
            }
        }
    }

    private function prefillingRepository(Grid $grid): void
    {
        $config = $grid->getDriverConfiguration();
        if (!array_key_exists('repository', $config)) {
            return;
        }

        if (!array_key_exists('arguments', $config['repository'])) {
            $config['repository']['arguments'] = [];
        }

        $grid->setDriverConfiguration($config);
    }
}
