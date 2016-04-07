<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class CartBlamerListener
{
    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * @param ObjectManager $cartManager
     * @param CartProviderInterface $cartProvider
     */
    public function __construct(ObjectManager $cartManager, CartProviderInterface $cartProvider)
    {
        $this->cartManager = $cartManager;
        $this->cartProvider = $cartProvider;
    }

    /**
     * @param UserEvent $userEvent
     */
    public function onImplicitLogin(UserEvent $userEvent)
    {
        $this->blame($userEvent->getUser());
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     */
    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent)
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $this->blame($user);
    }

    /**
     * @param UserInterface $user
     */
    private function blame(UserInterface $user)
    {
        $cart = $this->getCart();

        if (null === $cart) {
            return;
        }

        $cart->setCustomer($user->getCustomer());

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }

    /**
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    private function getCart()
    {
        if (!$this->cartProvider->hasCart()) {
            return null;
        }

        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        return $cart;
    }
}
