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

namespace Sylius\Bundle\CoreBundle\Tests\Grid\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sylius\Bundle\CoreBundle\Grid\Provider\ConfigurableProvider;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class ConfigurableProviderTest extends TestCase
{
    /** @var GridProviderInterface|MockObject */
    private $fooProvider;

    /** @var GridProviderInterface|MockObject */
    private $barProvider;

    /** @var Grid|MockObject */
    private $gridDefinition;

    protected function setUp(): void
    {
        $this->fooProvider = $this->createMock(GridProviderInterface::class);
        $this->barProvider = $this->createMock(GridProviderInterface::class);

        $this->gridDefinition = $this->createMock(Grid::class);
    }

    /** @test */
    public function it_can_use_the_configured_provider_for_a_specific_grid(): void
    {
        $providers = $this->createMock(ContainerInterface::class);

        $providers->method('get')
            ->with('bar')
            ->willReturn($this->barProvider);

        $this->barProvider->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition);

        $configurableProvider = new ConfigurableProvider($providers, [
            'default_type' => 'foo',
            'grids' => [
                'app_book' => ['type' => 'bar'],
            ],
        ]);

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }

    /** @test */
    public function it_can_use_the_default_configured_provider(): void
    {
        $providers = $this->createMock(ContainerInterface::class);

        $providers->method('get')
            ->with('foo')
            ->willReturn($this->fooProvider);

        $this->fooProvider->method('get')
            ->with('app_book')
            ->willReturn($this->gridDefinition);

        $configurableProvider = new ConfigurableProvider($providers, [
            'default_type' => 'foo',
        ]);

        $gridDefinition = $configurableProvider->get('app_book');

        self::assertEquals($this->gridDefinition, $gridDefinition);
    }

    /** @test */
    public function it_throws_an_exception_when_the_default_configured_provider_does_not_exist(): void
    {
        $providers = $this->createMock(ContainerInterface::class);

        $configurableProvider = new ConfigurableProvider($providers, [
            'default_type' => 'unknown',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Provider with type "unknown" was not found but it should.');

         $configurableProvider->get('app_book');
    }

    /** @test */
    public function it_throws_an_exception_when_the_configured_provider_for_specific_grid_does_not_exist(): void
    {
        $providers = $this->createMock(ContainerInterface::class);

        $configurableProvider = new ConfigurableProvider($providers, [
            'default_type' => 'foo',
            'grids' => [
                'app_book' => ['type' => 'unknown'],
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Provider with type "unknown" was not found but it should.');

        $configurableProvider->get('app_book');
    }
}
