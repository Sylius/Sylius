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

namespace Tests\Sylius\Bundle\PayumBundle\Request;

use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRouteInterface;

final class ResolveNextRouteTest extends TestCase
{
    private MockObject&TokenInterface $token;

    private ResolveNextRoute $resolveNextRoute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->createMock(TokenInterface::class);
        $this->resolveNextRoute = new ResolveNextRoute($this->token);
    }

    public function testResolveNextRouteRequest(): void
    {
        self::assertInstanceOf(ResolveNextRouteInterface::class, $this->resolveNextRoute);
    }

    public function testHasNextRouteName(): void
    {
        $this->resolveNextRoute->setRouteName('route_name');

        self::assertSame('route_name', $this->resolveNextRoute->getRouteName());
    }

    public function testHasNextRouteParameters(): void
    {
        $this->resolveNextRoute->setRouteParameters(['id' => 1]);

        self::assertSame(['id' => 1], $this->resolveNextRoute->getRouteParameters());
    }

    public function testDoesNotHaveRouteNameByDefault(): void
    {
        self::assertNull($this->resolveNextRoute->getRouteName());
    }

    public function testDoesNotHaveRouteParametersByDefault(): void
    {
        self::assertSame([], $this->resolveNextRoute->getRouteParameters());
    }
}
