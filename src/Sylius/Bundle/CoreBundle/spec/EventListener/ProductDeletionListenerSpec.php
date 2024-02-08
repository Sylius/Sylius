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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ProductDeletionListenerSpec extends ObjectBehavior
{
    function let(ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker): void
    {
        $this->beConstructedWith($productInPromotionRuleChecker);
    }

    function it_throws_an_exception_when_subject_is_not_a_product(ResourceControllerEvent $event): void
    {
        $event->getSubject()->willReturn('subject');

        $this->shouldThrow(\InvalidArgumentException::class)->during('protectFromRemovingProductInUseByPromotionRule', [$event]);
    }

    function it_does_nothing_when_product_is_not_assigned_to_rule(
        ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker,
        SessionInterface $session,
        ResourceControllerEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $productInPromotionRuleChecker->isInUse($product)->willReturn(false);

        $event->setMessageType('error')->shouldNotBeCalled();
        $event->setMessage('sylius.product.in_use_by_promotion_rule')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingProductInUseByPromotionRule($event);
    }

    function it_prevents_to_remove_product_if_it_is_assigned_to_rule(
        ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker,
        ResourceControllerEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $productInPromotionRuleChecker->isInUse($product)->willReturn(true);

        $event->setMessageType('error')->shouldBeCalled();
        $event->setMessage('sylius.product.in_use_by_promotion_rule')->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingProductInUseByPromotionRule($event);
    }
}
