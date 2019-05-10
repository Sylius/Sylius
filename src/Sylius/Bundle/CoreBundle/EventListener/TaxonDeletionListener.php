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
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class TaxonDeletionListener
{
    /** @var TaxonAwareRuleUpdaterInterface */
    private $hasTaxonRuleUpdater;

    /** @var TaxonAwareRuleUpdaterInterface */
    private $totalOfItemsFromTaxonRuleUpdater;

    /** @var SessionInterface */
    private $session;

    public function __construct(
        TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater,
        TaxonAwareRuleUpdaterInterface $totalOfItemsFromTaxonRuleUpdater,
        SessionInterface $session
    ) {
        $this->hasTaxonRuleUpdater = $hasTaxonRuleUpdater;
        $this->totalOfItemsFromTaxonRuleUpdater = $totalOfItemsFromTaxonRuleUpdater;
        $this->session = $session;
    }

    public function removeTaxonFromPromotionRules(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $firstUpdatedPromotionCodes = $this->hasTaxonRuleUpdater->updateAfterDeletingTaxon($taxon->getCode());
        $secondUpdatedPromotionCodes = $this->totalOfItemsFromTaxonRuleUpdater->updateAfterDeletingTaxon($taxon->getCode());
        $updatedPromotionCodes = array_unique(array_merge($firstUpdatedPromotionCodes, $secondUpdatedPromotionCodes));

        if (!empty($updatedPromotionCodes)) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->session->getBag('flashes');
            $flashes->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => implode(', ', $updatedPromotionCodes)],
            ]);
        }
    }
}
