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

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @deprecated since Sylius 1.13 and will be removed in Sylius 2.0.
 */
final class TotalOfItemsFromTaxonRuleUpdater implements TaxonAwareRuleUpdaterInterface
{
    public function __construct(private RepositoryInterface $promotionRuleRepository)
    {
        trigger_deprecation(
            'sylius/core',
            '1.13',
            'The "%s" class is deprecated and will be removed in Sylius 2.0.',
            self::class,
        );
    }

    public function updateAfterDeletingTaxon(TaxonInterface $taxon): array
    {
        $updatedPromotionCodes = [];
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => TotalOfItemsFromTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $promotionCode = $this->removePromotionRuleIfNecessary($promotionRule, $taxon->getCode());

            if (null !== $promotionCode) {
                $updatedPromotionCodes[] = $promotionRule->getPromotion()->getCode();
            }
        }

        return $updatedPromotionCodes;
    }

    private function removePromotionRuleIfNecessary(PromotionRuleInterface $promotionRule, string $taxonCode): ?string
    {
        foreach ($promotionRule->getConfiguration() as $configuration) {
            if ($taxonCode === $configuration['taxon']) {
                $this->promotionRuleRepository->remove($promotionRule);

                return $promotionRule->getPromotion()->getCode();
            }
        }

        return null;
    }
}
