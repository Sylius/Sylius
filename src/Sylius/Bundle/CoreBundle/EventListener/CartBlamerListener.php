<?php

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class CartBlamerListener
{
    private $cartManager;
    private $cartProvider;

    public function __construct(ObjectManager $cartManager, CartProviderInterface $cartProvider)
    {
        $this->cartManager = $cartManager;
        $this->cartProvider = $cartProvider;
    }

    public function blame(UserEvent $userEvent)
    {
        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $customer = $userEvent->getUser()->getCustomer();
        $cart->setCustomer($customer);

        $this->cartManager->persist($cart);
        $this->cartManager->flush($cart);
    }

    public function interactiveBlame(InteractiveLoginEvent $interactiveLoginEvent)
    {
        $cart = $this->cartProvider->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();

        if (null === $user) {
            return;
        }
        $cart->setCustomer($user->getCustomer());

        $this->cartManager->persist($cart);
        $this->cartManager->flush($cart);
    }
}
