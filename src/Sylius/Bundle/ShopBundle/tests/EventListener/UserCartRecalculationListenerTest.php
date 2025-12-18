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
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\EventListener\UserCartRecalculationListener;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class UserCartRecalculationListenerTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private MockObject&OrderProcessorInterface $orderProcessor;

    private MockObject&SectionProviderInterface $uriBasedSectionContext;

    private UserCartRecalculationListener $userCartRecalculationListener;

    protected function setUp(): void
    {
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->uriBasedSectionContext = $this->createMock(SectionProviderInterface::class);

        $this->userCartRecalculationListener = new UserCartRecalculationListener(
            $this->cartContext,
            $this->orderProcessor,
            $this->uriBasedSectionContext,
        );
    }

    public function testRecalculatesCartForLoggedInUserAndInteractiveLoginEvent(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->uriBasedSectionContext->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $this->orderProcessor->expects($this->once())->method('process')->with($order);

        $this->userCartRecalculationListener->recalculateCartWhileLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testRecalculatesCartForLoggedInUserAndUserEvent(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->uriBasedSectionContext->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $this->orderProcessor->expects($this->once())->method('process')->with($order);

        $this->userCartRecalculationListener->recalculateCartWhileLogin($event);
    }

    public function testDoesNothingIfCannotFindCartForInteractiveLoginEvent(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);
        /** @var TokenInterface&MockObject $token */
        $token = $this->createMock(TokenInterface::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->uriBasedSectionContext->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());
        $this->orderProcessor->expects($this->never())->method('process');

        $this->userCartRecalculationListener->recalculateCartWhileLogin(new InteractiveLoginEvent($request, $token));
    }

    public function testDoesNothingIfCannotFindCartForUserEvent(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);
        /** @var ShopSection&MockObject $shopSection */
        $shopSection = $this->createMock(ShopSection::class);

        $this->uriBasedSectionContext->expects($this->once())->method('getSection')->willReturn($shopSection);
        $this->cartContext->expects($this->once())->method('getCart')->willThrowException(new CartNotFoundException());
        $this->orderProcessor->expects($this->never())->method('process');

        $this->userCartRecalculationListener->recalculateCartWhileLogin($event);
    }

    public function testDoesNothingIfSectionIsDifferentThanShopSection(): void
    {
        /** @var UserEvent&MockObject $event */
        $event = $this->createMock(UserEvent::class);

        $this->uriBasedSectionContext->expects($this->once())->method('getSection')->willReturn(null);
        $this->cartContext->expects($this->never())->method('getCart');
        $this->orderProcessor->expects($this->never())->method('process');

        $this->userCartRecalculationListener->recalculateCartWhileLogin($event);
    }
}
