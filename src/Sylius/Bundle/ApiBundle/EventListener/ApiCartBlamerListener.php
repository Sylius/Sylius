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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class ApiCartBlamerListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private SectionProviderInterface $uriBasedSectionContext,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function onLoginSuccess(LoginSuccessEvent $loginSuccessEvent): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopApiOrdersSubSection) {
            return;
        }

        $user = $loginSuccessEvent->getAuthenticatedToken()->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $cart = $this->getCart();
        if (null === $cart || !$cart->isCreatedByGuest()) {
            return;
        }

        $this->commandBus->dispatch(new BlameCart($user->getEmail(), $cart->getTokenValue()));
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function getCart(): ?OrderInterface
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException) {
            return null;
        }

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        return $cart;
    }
}
