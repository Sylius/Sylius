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

namespace Tests\Sylius\Bundle\CoreBundle\Routing;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Routing\RequestContext;
use Symfony\Component\Routing\RequestContext as BaseRequestContext;

final class RequestContextTest extends TestCase
{
    public function testRoutingBcLayerIsEnabledByDefault(): void
    {
        $requestContext = new RequestContext($this->createMock(BaseRequestContext::class), []);

        $this->assertTrue($requestContext->isSyliusRoutingBcLayerEnabled('admin_product'));
    }

    public function testRoutingBcLayerCanBeEnabled(): void
    {
        $requestContext = new RequestContext($this->createMock(BaseRequestContext::class), ['enabled' => true]);

        $this->assertTrue($requestContext->isSyliusRoutingBcLayerEnabled('admin_product'));
    }

    public function testRoutingBcLayerCanBeDisabled(): void
    {
        $requestContext = new RequestContext($this->createMock(BaseRequestContext::class), ['enabled' => false]);

        $this->assertFalse($requestContext->isSyliusRoutingBcLayerEnabled('admin_product'));
    }

    public function testRoutingBcLayerCanBeDisabledForSpecificRoutes(): void
    {
        $requestContext = new RequestContext($this->createMock(BaseRequestContext::class), ['routes' => [
            'admin_product' => ['enabled' => false],
        ]]);

        $this->assertFalse($requestContext->isSyliusRoutingBcLayerEnabled('admin_product'));
    }
}
