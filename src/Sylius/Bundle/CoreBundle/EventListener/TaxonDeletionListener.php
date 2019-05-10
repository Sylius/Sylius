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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class TaxonDeletionListener
{
    /** @var TaxonAwareRuleUpdaterInterface */
    private $hasTaxonRuleUpdater;

    /** @var TaxonAwareRuleUpdaterInterface */
    private $totalOfItemsFromTaxonRuleUpdater;

    public function __construct(
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater
    ) {
        $this->hasTaxonRuleUpdater = $hasTaxonRuleUpdater;
        $this->totalOfItemsFromTaxonRuleUpdater = $totalOfItemsFromTaxonRuleUpdater;
    }

    public function removeTaxonFromPromotionRules(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $this->hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon->getCode());
        $this->totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon->getCode());
    }
}
