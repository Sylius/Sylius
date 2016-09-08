<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Handler\CartLocaleChangeHandler;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @mixin CartLocaleChangeHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartLocaleChangeHandlerSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, ObjectManager $cartManager)
    {
        $this->beConstructedWith($cartContext, $cartManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartLocaleChangeHandler::class);
    }

    function it_implements_locale_change_handler_interface()
    {
        $this->shouldImplement(LocaleChangeHandlerInterface::class);
    }

    function it_handles_cart_locale_change(
        CartContextInterface $cartContext,
        ObjectManager $cartManager,
        OrderInterface $cart
    ) {
        $cartContext->getCart()->willReturn($cart);
        $cart->setLocaleCode('en_GB')->shouldBeCalled();
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();

        $this->handle('en_GB');
    }

    function it_throws_handle_exception_if_cart_was_not_found(
        CartContextInterface $cartContext,
        ObjectManager $cartManager
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $cartManager->persist(Argument::any())->shouldNotBeCalled();
        $cartManager->flush()->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', ['en_GB']);
    }
}
