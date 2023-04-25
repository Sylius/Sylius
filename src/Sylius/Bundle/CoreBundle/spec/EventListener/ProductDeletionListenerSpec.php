<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ProductDeletionListenerSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        ProductAwareRuleUpdaterInterface $firstUpdater,
        ProductAwareRuleUpdaterInterface $secondUpdater,
    ): void {
        $this->beConstructedWith($requestStack, $firstUpdater, $secondUpdater);
    }

    function it_throws_an_exception_when_subject_is_not_a_product(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('subject');

        $this->shouldThrow(\InvalidArgumentException::class)->during('removeProductFromPromotionRules', [$event]);
    }

    function it_does_nothing_when_rule_updaters_do_not_return_any_codes(
        RequestStack $requestStack,
        ProductAwareRuleUpdaterInterface $firstUpdater,
        ProductAwareRuleUpdaterInterface $secondUpdater,
        SessionInterface $session,
        GenericEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $firstUpdater->updateAfterProductDeletion($product)->willReturn([]);
        $secondUpdater->updateAfterProductDeletion($product)->willReturn([]);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->shouldNotBeCalled();

        $this->removeProductFromPromotionRules($event);
    }

    function it_returns_a_list_of_unique_updated_rules_codes(
        RequestStack $requestStack,
        ProductAwareRuleUpdaterInterface $firstUpdater,
        ProductAwareRuleUpdaterInterface $secondUpdater,
        SessionInterface $session,
        FlashBagInterface $flashes,
        GenericEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);

        $firstUpdater->updateAfterProductDeletion($product)->willReturn(['first_rule', 'second_rule']);
        $secondUpdater->updateAfterProductDeletion($product)->willReturn(['second_rule', 'third_rule']);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashes);
        $flashes
            ->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => 'first_rule, second_rule, third_rule'],
            ])
            ->shouldBeCalled()
        ;

        $this->removeProductFromPromotionRules($event);
    }
}
