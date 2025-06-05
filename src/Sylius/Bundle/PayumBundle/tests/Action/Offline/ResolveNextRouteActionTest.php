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

namespace Tests\Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Action\Offline\ResolveNextRouteAction;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;

final class ResolveNextRouteActionTest extends TestCase
{
    private ResolveNextRouteAction $resolveNextRouteAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolveNextRouteAction = new ResolveNextRouteAction();
    }

    public function testAPayumAction(): void
    {
        self::assertInstanceOf(ActionInterface::class, $this->resolveNextRouteAction);
    }

    public function testResolvesNextRoute(): void
    {
        /** @var ResolveNextRoute&MockObject $resolveNextRouteRequest */
        $resolveNextRouteRequest = $this->createMock(ResolveNextRoute::class);

        $resolveNextRouteRequest->expects(self::once())
            ->method('setRouteName')
            ->with('sylius_shop_order_thank_you');

        $this->resolveNextRouteAction->execute($resolveNextRouteRequest);
    }
}
