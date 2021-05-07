<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\BlameCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BlameCartHandler implements MessageHandlerInterface
{
    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        UserRepositoryInterface $shopUserRepository,
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->shopUserRepository = $shopUserRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(BlameCart $blameCart): void
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->shopUserRepository->findOneByEmail($blameCart->shopUserEmail);

        if ($user === null) {
            throw new \InvalidArgumentException('There is currently no customer with given email');
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($blameCart->orderTokenValue);

        if ($cart === null) {
            throw new \InvalidArgumentException('Cart with given token value could not be found');
        }

        if (null !== $cart->getCustomer()) {
            throw new \InvalidArgumentException('There is an assigned customer to this cart');
        }

        $cart->setCustomer($user->getCustomer());

        $this->orderProcessor->process($cart);
    }
}
