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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ProductCannotBeRemoved;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ProductDeletionEventSubscriberSpec extends ObjectBehavior
{
    function let(ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker): void
    {
        $this->beConstructedWith($productInPromotionRuleChecker);
    }

    function it_does_not_throw_exception_when_product_is_not_being_deleted(
        ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker,
        ProductInterface $product,
        Request $request,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $product->getWrappedObject(),
        );

        $productInPromotionRuleChecker->isInUse($product)->willReturn(false);

        $this->shouldNotThrow()->during('protectFromRemovingProductInUseByPromotionRule', [$event]);
    }

    function it_throws_an_exception_when_trying_to_delete_product_assigned_to_promotion_rule(
        ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker,
        ProductInterface $product,
        Request $request,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $product->getWrappedObject(),
        );

        $productInPromotionRuleChecker->isInUse($product)->willReturn(true);

        $this->shouldThrow(ProductCannotBeRemoved::class)->during('protectFromRemovingProductInUseByPromotionRule', [$event]);
    }
}
