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

final class TaxonDeletionListenerSpec extends ObjectBehavior
{
    function let(
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater
    ): void {
        $this->beConstructedWith($hasTaxonRuleUpdater, $totalOfItemsFromTaxonRuleUpdater);
    }

    function it_adds_flash_that_promotions_have_been_updated(
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        GenericEvent $event,
        TaxonInterface $taxon
    ): void {
        $event->getSubject()->willReturn($taxon);
        $taxon->getCode()->willReturn('toys');

        $hasTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn(['christmas', 'holiday']);
        $totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon('toys')->willReturn(['christmas']);

        $this->removeTaxonFromPromotionRules($event);
    }
}
