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

namespace Tests\Sylius\Bundle\ApiBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\BlameCart;
use Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class ApiCartBlamerListenerTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private MockObject&SectionProviderInterface $sectionResolver;

    private MessageBusInterface&MockObject $commandBus;

    private ApiCartBlamerListener $apiCartBlamerListener;

    private MockObject&Request $request;

    private MockObject&TokenInterface $token;

    private AdminApiSection&MockObject $section;

    private AuthenticatorInterface&MockObject $authenticator;

    private MockObject&Passport $passport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->sectionResolver = $this->createMock(SectionProviderInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->apiCartBlamerListener = new ApiCartBlamerListener(
            $this->cartContext,
            $this->sectionResolver,
            $this->commandBus,
        );
        $this->request = $this->createMock(Request::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->section = $this->createMock(AdminApiSection::class);
        $this->authenticator = $this->createMock(AuthenticatorInterface::class);
        $this->passport = $this->createMock(Passport::class);
    }

    public function testThrowsAnExceptionWhenCartDoesNotImplementCoreOrderInterfaceOnInteractiveLogin(): void
    {
        /** @var \Sylius\Component\Order\Model\OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(\Sylius\Component\Order\Model\OrderInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var ShopApiOrdersSubSection|MockObject $shopApiOrdersSubSectionSectionMock */
        $shopApiOrdersSubSectionSectionMock = $this->createMock(ShopApiOrdersSubSection::class);
        /** @var AuthenticatorInterface|MockObject $authenticatorMock */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        /** @var Passport|MockObject $passportMock */
        $passportMock = $this->createMock(Passport::class);

        $this->sectionResolver->expects(self::once())
            ->method('getSection')
            ->willReturn($shopApiOrdersSubSectionSectionMock);

        $this->cartContext->expects(self::once())->method('getCart')->willReturn($orderMock);

        $tokenMock->expects(self::once())->method('getUser')->willReturn($userMock);

        self::expectException(UnexpectedTypeException::class);

        $this->apiCartBlamerListener->onLoginSuccess(new LoginSuccessEvent(
            $authenticatorMock,
            $passportMock,
            $tokenMock,
            $requestMock,
            null,
            'api_shop',
        ));
    }

    public function testBlamesCartOnUserOnInteractiveLogin(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ShopApiOrdersSubSection|MockObject $shopApiOrdersSubSectionSectionMock */
        $shopApiOrdersSubSectionSectionMock = $this->createMock(ShopApiOrdersSubSection::class);
        /** @var AuthenticatorInterface|MockObject $authenticatorMock */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        /** @var Passport|MockObject $passportMock */
        $passportMock = $this->createMock(Passport::class);

        $this->sectionResolver->expects(self::once())
            ->method('getSection')
            ->willReturn($shopApiOrdersSubSectionSectionMock);

        $this->cartContext->expects(self::once())->method('getCart')->willReturn($cartMock);

        $cartMock->expects(self::once())->method('isCreatedByGuest')->willReturn(true);

        $tokenMock->expects(self::once())->method('getUser')->willReturn($userMock);

        $userMock->method('getCustomer')->willReturn($customerMock);

        $userMock->expects(self::once())->method('getEmail')->willReturn('email@sylius.com');

        $cartMock->expects(self::once())->method('getTokenValue')->willReturn('TOKEN');

        $blameCart = new BlameCart('email@sylius.com', 'TOKEN');

        $this->commandBus->expects(self::once())
            ->method('dispatch')
            ->with($blameCart)
            ->willReturn(new Envelope($blameCart));

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticatorMock,
                $passportMock,
                $tokenMock,
                $requestMock,
                null,
                'api_shop',
            ),
        );
    }

    public function testDoesNothingIfGivenCartHasBeenBlamedInPast(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var ShopApiOrdersSubSection|MockObject $shopApiOrdersSubSectionSectionMock */
        $shopApiOrdersSubSectionSectionMock = $this->createMock(ShopApiOrdersSubSection::class);
        /** @var AuthenticatorInterface|MockObject $authenticatorMock */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        /** @var Passport|MockObject $passportMock */
        $passportMock = $this->createMock(Passport::class);

        $this->sectionResolver->expects(self::once())
            ->method('getSection')
            ->willReturn($shopApiOrdersSubSectionSectionMock);

        $this->cartContext->method('getCart')->willReturn($cartMock);

        $cartMock->method('isCreatedByGuest')->willReturn(false);

        $cartMock->expects(self::never())->method('setCustomer');

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticatorMock,
                $passportMock,
                $tokenMock,
                $requestMock,
                null,
                'api_shop',
            ),
        );
    }

    public function testDoesNothingIfGivenUserIsInvalidOnInteractiveLogin(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var ShopApiOrdersSubSection|MockObject $shopApiOrdersSubSectionSectionMock */
        $shopApiOrdersSubSectionSectionMock = $this->createMock(ShopApiOrdersSubSection::class);
        /** @var AuthenticatorInterface|MockObject $authenticatorMock */
        $authenticatorMock = $this->createMock(AuthenticatorInterface::class);
        /** @var Passport|MockObject $passportMock */
        $passportMock = $this->createMock(Passport::class);

        $this->sectionResolver->expects(self::once())
            ->method('getSection')
            ->willReturn($shopApiOrdersSubSectionSectionMock);

        $this->cartContext->method('getCart')->willReturn($cartMock);

        $tokenMock->expects(self::once())->method('getUser')->willReturn(null);

        $cartMock->expects(self::never())->method('setCustomer');

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $authenticatorMock,
                $passportMock,
                $tokenMock,
                $requestMock,
                null,
                'api_shop',
            ),
        );
    }

    public function testDoesNothingIfThereIsNoExistingCartOnInteractiveLogin(): void
    {
        /** @var ShopUserInterface&MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var ShopApiOrdersSubSection&MockObject $shopApiOrdersSubSectionMock */
        $shopApiOrdersSubSectionMock = $this->createMock(ShopApiOrdersSubSection::class);

        $this->sectionResolver->expects(self::once())
            ->method('getSection')
            ->willReturn($shopApiOrdersSubSectionMock);

        $this->cartContext->expects(self::once())
            ->method('getCart')
            ->willThrowException(new CartNotFoundException());

        $this->token->expects(self::once())->method('getUser')->willReturn($userMock);

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $this->authenticator,
                $this->passport,
                $this->token,
                $this->request,
                null,
                'api_shop',
            ),
        );
    }

    public function testDoesNothingIfTheCurrentSectionIsNotShopOnInteractiveLogin(): void
    {
        $this->sectionResolver->expects(self::once())->method('getSection')->willReturn($this->section);

        $this->token->expects(self::never())->method('getUser');

        $this->cartContext->expects(self::never())->method('getCart');

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $this->authenticator,
                $this->passport,
                $this->token,
                $this->request,
                null,
                'api_shop',
            ),
        );
    }

    public function testDoesNothingIfTheCurrentSectionIsNotOrdersSubsection(): void
    {
        $this->sectionResolver->expects(self::once())->method('getSection')->willReturn($this->section);

        $this->token->expects(self::never())->method('getUser');

        $this->cartContext->expects(self::never())->method('getCart');

        $this->apiCartBlamerListener->onLoginSuccess(
            new LoginSuccessEvent(
                $this->authenticator,
                $this->passport,
                $this->token,
                $this->request,
                null,
                'api_shop',
            ),
        );
    }
}
