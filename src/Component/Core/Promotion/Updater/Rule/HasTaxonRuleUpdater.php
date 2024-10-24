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

namespace Sylius\Component\Core\Promotion\Updater\Rule;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class HasTaxonRuleUpdater implements TaxonAwareRuleUpdaterInterface
{
    /** @param RepositoryInterface<PromotionRuleInterface> $promotionRuleRepository */
    public function __construct(
        private RepositoryInterface $promotionRuleRepository,
        private EntityManagerInterface $manager,
    ) {
    }

    public function updateAfterDeletingTaxon(TaxonInterface $taxon): array
    {
        $updatedPromotionCodes = [];
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => HasTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $configuration = $promotionRule->getConfiguration();
            if (in_array($taxon->getCode(), $configuration['taxons'])) {
                $configuration['taxons'] = array_values(array_diff($configuration['taxons'], [$taxon->getCode()]));
                $promotionRule->setConfiguration($configuration);

                $updatedPromotionCodes[] = $promotionRule->getPromotion()->getCode();
            }
        }

        $this->manager->flush();

        return $updatedPromotionCodes;
    }
}
