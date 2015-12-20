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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CartBlamerListener
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
    public function blame(UserEvent $userEvent)
    {
        if (!$this->cartProvider->hasCart()) {
            return;
        }

        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        $customer = $userEvent->getUser()->getCustomer();
        $cart->setCustomer($customer);

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     */
    public function interactiveBlame(InteractiveLoginEvent $interactiveLoginEvent)
    {
        if (!$this->cartProvider->hasCart()) {
            return;
        }

        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();

        if (null === $user) {
            return;
        }
        $cart->setCustomer($user->getCustomer());

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }
}
