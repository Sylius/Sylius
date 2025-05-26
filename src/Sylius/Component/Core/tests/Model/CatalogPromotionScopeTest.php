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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CatalogPromotionScope;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionScopeTest extends TestCase
{
    private CatalogPromotionScope $catalogPromotionScope;

    protected function setUp(): void
    {
        $this->catalogPromotionScope = new CatalogPromotionScope();
    }

    public function testShouldImplementCatalogPromotionScopeInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionScopeInterface::class, $this->catalogPromotionScope);
    }

    public function testShouldExtendBaseCatalogPromotionScope(): void
    {
        $this->assertInstanceOf(CatalogPromotionScope::class, $this->catalogPromotionScope);
    }
}
