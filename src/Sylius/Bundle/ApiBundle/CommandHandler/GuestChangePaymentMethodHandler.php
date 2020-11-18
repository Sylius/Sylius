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

use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\GuestChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class GuestChangePaymentMethodHandler implements MessageHandlerInterface
{
    /** @var PaymentMethodChangerInterface */
    private $paymentMethodChanger;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(
        PaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ) {
        $this->paymentMethodChanger = $commandPaymentMethodChanger;
        $this->orderRepository = $orderRepository;
        $this->userContext = $userContext;
    }

    public function __invoke(GuestChangePaymentMethod $changePaymentMethod): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $changePaymentMethod->orderTokenValue]);

        Assert::notNull($order, 'Cart has not been found.');

        $user = $this->userContext->getUser();

        if ($user !== null && $order->getUser() !== null) {
            throw new \InvalidArgumentException('User is not null');
        }

        return $this->paymentMethodChanger->changePaymentMethod(
            $changePaymentMethod->paymentMethodCode,
            $changePaymentMethod->paymentId,
            $order
        );
    }
}
