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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Grid\Provider\OverrideGridProvider;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class OverrideGridProviderTest extends TestCase
{
    private GridProviderInterface&MockObject $chainProvider;

    private GridProviderInterface&MockObject $arrayProvider;

    private Grid $gridDefinition;

    protected function setUp(): void
    {
        $this->chainProvider = $this->createMock(GridProviderInterface::class);
        $this->arrayProvider = $this->createMock(GridProviderInterface::class);

        $this->gridDefinition = Grid::fromCodeAndDriverConfiguration('app_book', '', []);
    }

    public function test_use_override_for_grid(): void
    {
        $this->arrayProvider
            ->expects($this->never())
            ->method('get')
        ;
        $this->chainProvider
            ->expects($this->once())
            ->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition)
        ;

        $configurableProvider = new OverrideGridProvider(
            [
                'grids' => [
                    'app_book' => ['use_legacy_config' => false],
                ],
            ],
            $this->chainProvider,
            $this->arrayProvider,
        );

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }

    public function test_overriding_for_all_grids(): void
    {
        $this->arrayProvider
            ->expects($this->never())
            ->method('get')
        ;
        $this->chainProvider
            ->expects($this->once())
            ->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition)
        ;

        $configurableProvider = new OverrideGridProvider(
            ['use_legacy_config' => false],
            $this->chainProvider,
            $this->arrayProvider,
        );

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }

    public function test_using_the_array_provider_by_default(): void
    {
        $this->arrayProvider
            ->expects($this->once())
            ->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition)
        ;
        $this->chainProvider
            ->expects($this->never())
            ->method('get')
        ;

        $configurableProvider = new OverrideGridProvider(
            [],
            $this->chainProvider,
            $this->arrayProvider,
        );

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }

    public function test_fallback_if_no_yaml_grid_is_configured(): void
    {
        $this->arrayProvider
            ->expects($this->once())
            ->method('get')
            ->with('app_book')
            ->willThrowException(new UndefinedGridException('app_book'))
        ;
        $this->chainProvider
            ->expects($this->once())
            ->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition)
        ;

        $configurableProvider = new OverrideGridProvider(
            [],
            $this->chainProvider,
            $this->arrayProvider,
        );

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }
}
