<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/*
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CartBlamerListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $cartManager, CartProviderInterface $cartProvider)
    {
        $this->beConstructedWith($cartManager, $cartProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener');
    }

    function it_throws_exception_when_cart_does_not_implement_core_order_interface($cartManager, $cartProvider, CartInterface $cart, UserEvent $userEvent)
    {
        $cartProvider->getCart()->willReturn($cart);

        $cartManager->persist($cart)->shouldNotBeCalled();
        $cartManager->flush($cart)->shouldNotBeCalled();

        $this->shouldThrow('Sylius\Component\Resource\Exception\UnexpectedTypeException')->during('blame', array($userEvent));
    }

    function it_blames_cart_on_user($cartManager, $cartProvider, OrderInterface $cart, UserEvent $userEvent, UserInterface $user, CustomerInterface $customer)
    {
        $cartProvider->getCart()->willReturn($cart);
        $userEvent->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush($cart)->shouldBeCalled();

        $this->blame($userEvent);
    }
}
