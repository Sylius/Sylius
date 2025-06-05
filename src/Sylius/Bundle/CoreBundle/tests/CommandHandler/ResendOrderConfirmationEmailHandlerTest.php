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

namespace Tests\Sylius\Bundle\CoreBundle\CommandHandler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\CommandHandler\ResendOrderConfirmationEmailHandler;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final class ResendOrderConfirmationEmailHandlerTest extends TestCase
{
    private MockObject&OrderEmailManagerInterface $orderEmailManager;

    private MockObject&RepositoryInterface $orderRepository;

    private ResendOrderConfirmationEmailHandler $handler;

    protected function setUp(): void
    {
        $this->orderEmailManager = $this->createMock(OrderEmailManagerInterface::class);
        $this->orderRepository = $this->createMock(RepositoryInterface::class);

        $this->handler = new ResendOrderConfirmationEmailHandler(
            $this->orderEmailManager,
            $this->orderRepository,
        );
    }

    public function testItIsAMessageHandler(): void
    {
        $reflection = new \ReflectionClass(ResendOrderConfirmationEmailHandler::class);
        $attributes = $reflection->getAttributes(AsMessageHandler::class);

        $this->assertCount(1, $attributes);
    }

    public function testItResendsOrderConfirmationEmail(): void
    {
        $order = new Order();

        $this->orderRepository
            ->method('findOneBy')
            ->with(['tokenValue' => 'TOKEN'])
            ->willReturn($order)
        ;

        $this->orderEmailManager
            ->expects($this->once())
            ->method('resendConfirmationEmail')
            ->with($order)
        ;

        ($this->handler)(new ResendOrderConfirmationEmail('TOKEN'));
    }

    public function testItThrowsNotFoundExceptionWhenOrderNotFound(): void
    {
        $this->orderRepository
            ->method('findOneBy')
            ->with(['tokenValue' => 'NON_EXISTING_TOKEN'])
            ->willReturn(null)
        ;

        $this->expectException(NotFoundHttpException::class);

        ($this->handler)(new ResendOrderConfirmationEmail('NON_EXISTING_TOKEN'));
    }
}
