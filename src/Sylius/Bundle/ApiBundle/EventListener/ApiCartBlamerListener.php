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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class ApiCartBlamerListener
{
    /** @var CartContextInterface */
    private $cartContext;

    /** @var SectionProviderInterface */
    private $uriBasedSectionContext;

    public function __construct(
        CartContextInterface $cartContext,
        SectionProviderInterface $uriBasedSectionContext
    ) {
        $this->cartContext = $cartContext;
        $this->uriBasedSectionContext = $uriBasedSectionContext;
    }

    public function onImplicitLogin(UserEvent $userEvent): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopApiOrdersSubSection) {
            return;
        }

        $user = $userEvent->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->blame($user);
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopApiOrdersSubSection) {
            return;
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->blame($user);
    }

    private function blame(ShopUserInterface $user): void
    {
        $cart = $this->getCart();
        if (null === $cart || null !== $cart->getCustomer()) {
            return;
        }

        $cart->setCustomer($user->getCustomer());
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function getCart(): ?OrderInterface
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return null;
        }

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        return $cart;
    }
}
