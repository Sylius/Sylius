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

        $serviceVersion = $serviceProvider->get($gridName);
        $yamlVersion = $arrayProvider->get($gridName);

        $this->prefillingRepository($yamlVersion);
        $this->prefillingGridFields($yamlVersion);
        $this->prefillingGridActions($yamlVersion);

        $this->assertGridSame($yamlVersion, $serviceVersion);
    }

    public static function dataProviderGrids(): \Generator
    {
        $gridConfiguration = self::getContainer()->getParameter('sylius.grids_definitions');
        self::assertNotEmpty($gridConfiguration, 'No grid configuration found');

        foreach (array_keys($gridConfiguration) as $gridName) {
            // This deprecated grid will not be migrated
            if ($gridName === 'sylius_admin_address_log_entry') {
                continue;
            }
            yield $gridName => [$gridName];
        }

        self::ensureKernelShutdown();
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

    public function assertGridSame(Grid $yamlGrid, Grid $serviceGrid): void
    {
        self::assertSame($yamlGrid->getCode(), $serviceGrid->getCode());
        self::assertSame($yamlGrid->getDriver(), $serviceGrid->getDriver());
        self::assertSameWithIgnoredOrder($yamlGrid->getDriverConfiguration(), $serviceGrid->getDriverConfiguration());
        self::assertSame($yamlGrid->getProvider(), $serviceGrid->getProvider());
        self::assertSame($yamlGrid->getSorting(), $serviceGrid->getSorting());
        self::assertSame($yamlGrid->getLimits(), $serviceGrid->getLimits());
        self::assertSame($yamlGrid->getSorting(), $serviceGrid->getSorting());

        $serviceFields = $serviceGrid->getFields();
        $this->assertSame(
            array_keys($serviceGrid->getFields()),
            array_keys($yamlGrid->getFields()),
            'Field keys in grid do not match',
        );
        foreach ($yamlGrid->getFields() as $fieldName => $yamlField) {
            $this->assertArrayHasKey($fieldName, $serviceFields);
            $this->assertEquals($yamlField, $serviceFields[$fieldName]);

            $serviceField = $serviceFields[$fieldName];
            $this->assertSame($yamlField->getName(), $serviceField->getName(), 'Name of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->getType(), $serviceField->getType(), 'Type of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->getPath(), $serviceField->getPath(), 'Path of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->getLabel(), $serviceField->getLabel(), 'Label of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->isEnabled(), $serviceField->isEnabled(), 'Enabled state of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->isSortable(), $serviceField->isSortable(), 'Sortable state of field '.$fieldName.' does not match');
            $this->assertSame($yamlField->getSortable(), $serviceField->getSortable(), 'Sortable path of field '.$fieldName.' does not match');
            $this->assertSameWithIgnoredOrder($yamlField->getOptions(), $serviceField->getOptions(), 'Options of field '.$fieldName.' do not match');
            $this->assertSame($yamlField->getPosition(), $serviceField->getPosition(), 'Position of field '.$fieldName.' does not match');
        }

        $serviceFilters = $serviceGrid->getFilters();
        $this->assertSame(
            array_keys($serviceGrid->getFilters()),
            array_keys($yamlGrid->getFilters()),
            'Filter keys in grid do not match',
        );
        foreach ($yamlGrid->getFilters() as $filterName => $yamlFilter) {
            $this->assertArrayHasKey($filterName, $serviceFilters);

            $serviceFilter = $serviceFilters[$filterName];
            $this->assertSame($yamlFilter->isEnabled(), $serviceFilter->isEnabled(), 'Enabled state of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getName(), $serviceFilter->getName(), 'Name of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getType(), $serviceFilter->getType(), 'Type of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getLabel(), $serviceFilter->getLabel(), 'Label of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getTemplate(), $serviceFilter->getTemplate(), 'Template of filter '.$filterName. ' does not match');
            $this->assertSameWithIgnoredOrder($yamlFilter->getOptions(), $serviceFilter->getOptions(), 'Options of filter '.$filterName. ' does not match');
            $this->assertSameWithIgnoredOrder($yamlFilter->getFormOptions(), $serviceFilter->getFormOptions(), 'Form Options of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getCriteria(), $serviceFilter->getCriteria(), 'Criteria of filter '.$filterName. ' does not match');
            $this->assertSame($yamlFilter->getPosition(), $serviceFilter->getPosition(), 'Position of filter '.$filterName. ' does not match');
        }

        // Assert actions are the same
        $this->assertSame(
            array_keys($serviceGrid->getActionGroups()),
            array_keys($yamlGrid->getActionGroups()),
            'Action keys in action groups do not match',
        );
        $serviceActions = $serviceGrid->getActionGroups();
        foreach ($yamlGrid->getActionGroups() as $actionGroupName => $yamlActionGroup) {
            $this->assertArrayHasKey($actionGroupName, $serviceActions);
            $this->assertEquals($yamlActionGroup, $serviceActions[$actionGroupName]);
            $serviceActionGroup = $serviceActions[$actionGroupName];

            $this->assertSame(
                array_keys($serviceActionGroup->getActions()),
                array_keys($yamlActionGroup->getActions()),
                'Action keys in action group '.$actionGroupName.' do not match',
            );
            foreach ($serviceActionGroup->getActions() as $name => $expectedAction) {
                $actualAction = $yamlActionGroup->getAction($name);

                $this->assertSame($expectedAction->getName(), $actualAction->getName(), 'Name of action '.$actionGroupName.' does not match');
                $this->assertSame($expectedAction->getType(), $actualAction->getType(), 'Type of action '.$actionGroupName.' does not match');
                $this->assertSame($expectedAction->getLabel(), $actualAction->getLabel(), 'Label of action '.$actionGroupName.' does not match');
                $this->assertSame($expectedAction->isEnabled(), $actualAction->isEnabled(), 'Enabled state of action '.$actionGroupName.' does not match');
                $this->assertSame($expectedAction->getTemplate(), $actualAction->getTemplate(), 'Template of action '.$actionGroupName.' does not match');
                $this->assertSame($expectedAction->getIcon(), $actualAction->getIcon(), 'Icon of action '.$actionGroupName.' does not match');
                $this->assertSameWithIgnoredOrder($expectedAction->getOptions(), $actualAction->getOptions(), 'Options of action '.$actionGroupName.' do not match');
                $this->assertSame($expectedAction->getPosition(), $actualAction->getPosition(), 'Position of action '.$actionGroupName.' does not match');
            }
        }
    }

    private function assertSameWithIgnoredOrder(array $expected, array $actual): void
    {
        ksort($expected);
        ksort($actual);

        $this->assertSame($expected, $actual);
    }

}
