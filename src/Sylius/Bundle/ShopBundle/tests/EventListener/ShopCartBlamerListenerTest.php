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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\EventListener\ShopCartBlamerListener;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class ShopCartBlamerListenerTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private MockObject&SectionProviderInterface $sectionResolver;

    private ShopCartBlamerListener $shopCartBlamerListener;

    protected function setUp(): void
    {
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->sectionResolver = $this->createMock(SectionProviderInterface::class);

        $this->shopCartBlamerListener = new ShopCartBlamerListener($this->cartContext, $this->sectionResolver);
    }

    public function testThrowsAnExceptionWhenCartDoesNotImplementCoreOrderInterfaceOnImplicitLogin(): void
    {
        /** @var \Sylius\Component\Order\Model\OrderInterface&MockObject $order */
        $order = $this->createMock(\Sylius\Component\Order\Model\OrderInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var UserEvent&MockObject $userEvent */
        $userEvent = $this->createMock(UserEvent::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $userEvent->expects($this->once())->method('getUser')->willReturn($user);

        $this->expectException(UnexpectedTypeException::class);

        $this->shopCartBlamerListener->onImplicitLogin($userEvent);
    }

    public function testThrowsAnExceptionWhenCartDoesNotImplementCoreOrderInterfaceOnInteractiveLogin(): void
    {
        /** @var \Sylius\Component\Order\Model\OrderInterface&MockObject $order */
        $order = $this->createMock(\Sylius\Component\Order\Model\OrderInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $token->expects($this->once())->method('getUser')->willReturn($user);

        $this->expectException(UnexpectedTypeException::class);

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testBlamesCartOnUserOnImplicitLogin(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var UserEvent&MockObject $userEvent */
        $userEvent = $this->createMock(UserEvent::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($cart);
        $cart->expects($this->once())->method('getCustomer')->willReturn(null);
        $userEvent->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getCustomer')->willReturn($customer);
        $cart->expects($this->once())->method('setCustomerWithAuthorization')->with($customer);

        $this->shopCartBlamerListener->onImplicitLogin($userEvent);
    }

    public function testBlamesCartOnUserOnInteractiveLogin(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($cart);
        $cart->expects($this->once())->method('getCustomer')->willReturn(null);
        $token->expects($this->once())->method('getUser')->willReturn($user);
        $user->expects($this->once())->method('getCustomer')->willReturn($customer);
        $cart->expects($this->once())->method('setCustomerWithAuthorization')->with($customer);

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingIfGivenCartHasBeenBlamedInPast(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($cart);
        $cart->expects($this->once())->method('getCustomer')->willReturn($customer);
        $token->expects($this->once())->method('getUser')->willReturn($user);
        $cart->expects($this->never())->method('setCustomerWithAuthorization');

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingIfGivenUserIsInvalidOnInteractiveLogin(): void
    {
        /** @var OrderInterface&MockObject $cart */
        $cart = $this->createMock(OrderInterface::class);
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->never())->method('getCart');
        $token->expects($this->once())->method('getUser')->willReturn(null);
        $cart->expects($this->never())->method('setCustomerWithAuthorization');

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingIfThereIsNoExistingCartOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $userEvent */
        $userEvent = $this->createMock(UserEvent::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());
        $userEvent->expects($this->once())->method('getUser')->willReturn($user);

        $this->shopCartBlamerListener->onImplicitLogin($userEvent);
    }

    public function testDoesNothingIfThereIsNoExistingCartOnInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopUserInterface&MockObject $user */
        $user = $this->createMock(ShopUserInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());
        $token->expects($this->once())->method('getUser')->willReturn($user);

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingIfTheCurrentSectionIsNotShopOnImplicitLogin(): void
    {
        /** @var UserEvent&MockObject $userEvent */
        $userEvent = $this->createMock(UserEvent::class);
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($section);
        $userEvent->expects($this->never())->method('getUser');
        $this->cartContext->expects($this->never())->method('getCart');

        $this->shopCartBlamerListener->onImplicitLogin($userEvent);
    }

    public function testDoesNothingIfTheCurrentSectionIsNotShopOnInteractiveLogin(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var SectionInterface&MockObject $section */
        $section = $this->createMock(SectionInterface::class);

        $this->sectionResolver->expects($this->once())->method('getSection')->willReturn($section);
        $token->expects($this->never())->method('getUser');
        $this->cartContext->expects($this->never())->method('getCart');

        $this->shopCartBlamerListener->onInteractiveLogin(new InteractiveLoginEvent($request, $token));
    }
}
