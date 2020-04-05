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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TaxonDeletionListenerSpec extends ObjectBehavior
{
    function let(
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater
    ): void {
        $this->beConstructedWith(
            $session,
            $channelRepository,
            $hasTaxonRuleUpdater,
            $totalOfItemsFromTaxonRuleUpdater
        );
    }

    function it_does_not_allow_to_remove_taxon_if_any_channel_has_it_as_a_menu_taxon(
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        GenericEvent $event,
        TaxonInterface $taxon,
        ChannelInterface $channel,
        FlashBagInterface $flashes
    ): void {
        $event->getSubject()->willReturn($taxon);

        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn($channel);

        $session->getBag('flashes')->willReturn($flashes);
        $flashes->add('error', 'sylius.taxon.menu_taxon_delete')->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $this->protectFromRemovingMenuTaxon($event);
    }

    function it_does_nothing_if_taxon_is_not_a_menu_taxon_of_any_channel(
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        GenericEvent $event,
        TaxonInterface $taxon,
        ChannelInterface $channel
    ): void {
        $event->getSubject()->willReturn($taxon);

        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn(null);
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
        SessionInterface $session,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        FlashBagInterface $flashes,
        GenericEvent $event,
        TaxonInterface $taxon
    ): void {
        $event->getSubject()->willReturn($taxon);

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn(['christmas', 'holiday']);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn(['christmas']);

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
        SessionInterface $session,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        GenericEvent $event,
        TaxonInterface $taxon
    ): void {
        $event->getSubject()->willReturn($taxon);

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn([]);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon)->willReturn([]);

        $session->getBag('flashes')->shouldNotBeCalled();

        $this->removeTaxonFromPromotionRules($event);
    }
}
