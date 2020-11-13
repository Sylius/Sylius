<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\GuestSChangePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Changer\CommandPaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class GuestSChangePaymentMethodHandler implements MessageHandlerInterface
{
    /** @var CommandPaymentMethodChangerInterface */
    private $commandPaymentMethodChanger;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ) {
        $this->commandPaymentMethodChanger = $commandPaymentMethodChanger;
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
    }

    public function __invoke(GuestSChangePaymentMethod $guestSChangePaymentMethod): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $guestSChangePaymentMethod->orderTokenValue]);

        Assert::notNull($order, 'Cart has not been found.');

        $user = $this->userContext->getUser();

        if ($user !== null && $order->getUser() !== null) {
            throw new \InvalidArgumentException('User is not null');
        }

        return $this->commandPaymentMethodChanger->changePaymentMethod($guestSChangePaymentMethod, $order);
    }
}
