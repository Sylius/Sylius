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

namespace spec\Sylius\Bundle\ApiBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class ApiCartBlamerListenerSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        MessageBusInterface $commandBus,
    ): void {
        $this->beConstructedWith($cartContext, $sectionResolver, $commandBus);
    }

    function it_throws_an_exception_when_cart_does_not_implement_core_order_interface_on_interactive_login(
        BaseOrderInterface $order,
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        ShopUserInterface $user,
        Request $request,
        TokenInterface $token,
        ShopApiOrdersSubSection $shopApiOrdersSubSectionSection,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($shopApiOrdersSubSectionSection);
        $cartContext->getCart()->willReturn($order);
        $token->getUser()->willReturn($user);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('onLoginSuccess', [
                new LoginSuccessEvent(
                    $authenticator->getWrappedObject(),
                    $passport->getWrappedObject(),
                    $token->getWrappedObject(),
                    $request->getWrappedObject(),
                    null,
                    'new_api_shop_user',
                ),
            ])
        ;
    }

    function it_blames_cart_on_user_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ShopApiOrdersSubSection $shopApiOrdersSubSectionSection,
        MessageBusInterface $commandBus,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($shopApiOrdersSubSectionSection);
        $cartContext->getCart()->willReturn($cart);
        $cart->isCreatedByGuest()->willReturn(true);
        $token->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $user->getEmail()->willReturn('email@sylius.com');
        $cart->getTokenValue()->willReturn('TOKEN');

        $blameCart = new BlameCart('email@sylius.com', 'TOKEN');

        $commandBus
            ->dispatch($blameCart)
            ->shouldBeCalled()
            ->willReturn(new Envelope($blameCart))
        ;

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }

    function it_does_nothing_if_given_cart_has_been_blamed_in_past(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        CustomerInterface $customer,
        ShopApiOrdersSubSection $shopApiOrdersSubSectionSection,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($shopApiOrdersSubSectionSection);
        $cartContext->getCart()->willReturn($cart);
        $cart->isCreatedByGuest()->willReturn(false);

        $cart->setCustomer(Argument::any())->shouldNotBeCalled();

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }

    function it_does_nothing_if_given_user_is_invalid_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        OrderInterface $cart,
        Request $request,
        TokenInterface $token,
        ShopApiOrdersSubSection $shopApiOrdersSubSectionSection,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($shopApiOrdersSubSectionSection);
        $cartContext->getCart()->willReturn($cart);
        $token->getUser()->willReturn(null);

        $cart->setCustomer(Argument::any())->shouldNotBeCalled();

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }

    function it_does_nothing_if_there_is_no_existing_cart_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        Request $request,
        TokenInterface $token,
        ShopUserInterface $user,
        ShopApiOrdersSubSection $shopApiOrdersSubSection,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($shopApiOrdersSubSection);
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $token->getUser()->willReturn($user);

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }

    function it_does_nothing_if_the_current_section_is_not_shop_on_interactive_login(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        Request $request,
        TokenInterface $token,
        SectionInterface $section,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($section);

        $token->getUser()->shouldNotBeCalled();
        $cartContext->getCart()->shouldNotBeCalled();

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }

    function it_does_nothing_if_the_current_section_is_not_orders_subsection(
        CartContextInterface $cartContext,
        SectionProviderInterface $sectionResolver,
        Request $request,
        TokenInterface $token,
        AdminApiSection $section,
        AuthenticatorInterface $authenticator,
        Passport $passport,
    ): void {
        $sectionResolver->getSection()->willReturn($section);

        $token->getUser()->shouldNotBeCalled();
        $cartContext->getCart()->shouldNotBeCalled();

        $this->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticator->getWrappedObject(),
                $passport->getWrappedObject(),
                $token->getWrappedObject(),
                $request->getWrappedObject(),
                null,
                'new_api_shop_user',
            ),
        );
    }
}
