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

namespace Sylius\Component\Core\Promotion\Updater\Rule;

use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TotalOfItemsFromTaxonRuleUpdater implements TaxonAwareRuleUpdaterInterface
{
    /** @var RepositoryInterface */
    private $promotionRuleRepository;

    public function __construct(RepositoryInterface $ruleUpdater)
    {
        $this->promotionRuleRepository = $ruleUpdater;
    }

    public function updateAfterDeletingTaxon(string $taxonCode): array
    {
        $updatedPromotionCodes = [];
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => TotalOfItemsFromTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $promotionCode = $this->removePromotionRuleIfNecessary($promotionRule, $taxonCode);

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
