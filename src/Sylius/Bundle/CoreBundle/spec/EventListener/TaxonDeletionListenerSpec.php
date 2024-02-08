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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TaxonDeletionListenerSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        ChannelRepositoryInterface $channelRepository,
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
    ): void {
        $this->beConstructedWith(
            $requestStack,
            $channelRepository,
            $taxonInPromotionRuleChecker,
            $hasTaxonRuleUpdater,
            $totalOfItemsFromTaxonRuleUpdater,
        );
    }

    function it_throws_an_exception_when_subject_is_not_a_taxon(ResourceControllerEvent $event): void
    {
        $event->getSubject()->willReturn('subject');

        $this->shouldThrow(\InvalidArgumentException::class)->during('protectFromRemovingTaxonInUseByPromotionRule', [$event]);
    }

    function it_does_not_allow_to_remove_taxon_if_any_channel_has_it_as_a_menu_taxon(
        RequestStack $requestStack,
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        GenericEvent $event,
        TaxonInterface $taxon,
        ChannelInterface $channel,
        FlashBagInterface $flashes,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn($channel);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashes);
        $flashes->add('error', 'sylius.taxon.menu_taxon_delete')->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingMenuTaxon($event);
    }

    function it_does_nothing_if_taxon_is_not_a_menu_taxon_of_any_channel(
        RequestStack $requestStack,
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        GenericEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn(null);
        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->shouldNotBeCalled();

        $this->protectFromRemovingMenuTaxon($event);
    }

    function it_throws_an_exception_if_an_event_subject_is_not_taxon(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('wrongSubject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('protectFromRemovingMenuTaxon', [$event])
        ;
    }

    function it_adds_flash_that_promotions_have_been_updated(
        RequestStack $requestStack,
        SessionInterface $session,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        FlashBagInterface $flashes,
        GenericEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn(['christmas', 'holiday']);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn(['christmas']);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashes);
        $flashes
            ->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => 'christmas, holiday'],
            ])
            ->shouldBeCalled()
        ;

        $this->removeTaxonFromPromotionRules($event);
    }

    function it_does_nothing_if_no_promotion_has_been_updated(
        RequestStack $requestStack,
        SessionInterface $session,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        GenericEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn([]);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn([]);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->shouldNotBeCalled();

        $this->removeTaxonFromPromotionRules($event);
    }

    function it_changes_taxon_position_to_minus_one_if_base_position_is_zero(
        GenericEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);
        $taxon->getPosition()->willReturn(0);
        $taxon->setPosition(-1)->shouldBeCalled();

        $this->handleRemovingRootTaxonAtPositionZero($event);
    }

    function it_does_nothing_when_product_is_not_assigned_to_rule(
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        ResourceControllerEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $taxonInPromotionRuleChecker->isInUse($taxon)->willReturn(false);

        $event->setMessageType('error')->shouldNotBeCalled();
        $event->setMessage('sylius.taxon.in_use_by_promotion_rule')->shouldNotBeCalled();
        $event->stopPropagation()->shouldNotBeCalled();

        $this->protectFromRemovingTaxonInUseByPromotionRule($event);
    }

    function it_prevents_to_remove_product_if_it_is_assigned_to_rule(
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        ResourceControllerEvent $event,
        TaxonInterface $taxon,
    ): void {
        $event->getSubject()->willReturn($taxon);

        $taxonInPromotionRuleChecker->isInUse($taxon)->willReturn(true);

        $event->setMessageType('error')->shouldBeCalled();
        $event->setMessage('sylius.taxon.in_use_by_promotion_rule')->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingTaxonInUseByPromotionRule($event);
    }
}
