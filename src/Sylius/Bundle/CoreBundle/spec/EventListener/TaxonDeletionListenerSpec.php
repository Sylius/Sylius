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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TaxonDeletionListenerSpec extends ObjectBehavior
{
    function let(
        SessionInterface $session,
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater
    ): void {
        $this->beConstructedWith($session, $hasTaxonRuleUpdater, $totalOfItemsFromTaxonRuleUpdater);
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
        $taxon->getCode()->willReturn('toys');

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn(['christmas', 'holiday']);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn(['christmas']);

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
        $taxon->getCode()->willReturn('toys');

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn([]);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn([]);

        $session->getBag('flashes')->shouldNotBeCalled();

        $this->removeTaxonFromPromotionRules($event);
    }
}
