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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class SendOrderConfirmationHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&OrderEmailManagerInterface $orderEmailManager;

    private SendOrderConfirmationHandler $handler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->orderEmailManager = $this->createMock(OrderEmailManagerInterface::class);
        $this->handler = new SendOrderConfirmationHandler($this->orderRepository, $this->orderEmailManager);
    }

    public function testSendsOrderConfirmationMessage(): void
    {
        /** @var OrderInterface|MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        $this->orderRepository->expects(self::once())
            ->method('findOneByTokenValue')
            ->with('TOKEN')
            ->willReturn($order);
        $order->method('getLocaleCode')->willReturn('pl_PL');
        $order->method('getCustomer')->willReturn($customer);
        $customer->expects(self::once())->method('getEmail')->willReturn('johnny.bravo@email.com');
        $this->orderEmailManager->expects(self::once())->method('sendConfirmationEmail')->with($order);
        $this->handler->__invoke(new SendOrderConfirmation('TOKEN'));
    }
}
