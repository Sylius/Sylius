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

namespace Tests\Sylius\Bundle\ApiBundle\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface;
use Sylius\Bundle\ApiBundle\Provider\LiipImageFiltersProvider;

final class LiipImageFiltersProviderTest extends TestCase
{
    private LiipImageFiltersProvider $liipImageFiltersProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->liipImageFiltersProvider = new LiipImageFiltersProvider([
            'sylius_shop_original' => 'args',
            'sylius_admin_original' => 'args',
            'custom_image' => 'args',
        ]);
    }

    public function testAnImageFiltersProvider(): void
    {
        self::assertInstanceOf(ImageFiltersProviderInterface::class, $this->liipImageFiltersProvider);
    }

    public function testReturnsImageFilters(): void
    {
        self::assertSame([
            'sylius_shop_original',
            'sylius_admin_original',
            'custom_image',
        ], $this->liipImageFiltersProvider->getFilters());
    }
}
