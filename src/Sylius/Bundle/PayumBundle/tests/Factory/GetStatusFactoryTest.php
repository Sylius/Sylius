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

namespace Tests\Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactory;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;

final class GetStatusFactoryTest extends TestCase
{
    private GetStatusFactory $getStatusFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getStatusFactory = new GetStatusFactory();
    }

    public function testGetStatusFactory(): void
    {
        self::assertInstanceOf(GetStatusFactoryInterface::class, $this->getStatusFactory);
    }

    public function testCreatesGetStatusRequest(): void
    {
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);

        $result = $this->getStatusFactory->createNewWithModel($token);

        self::assertEquals(new GetStatus($token), $result);
    }
}
