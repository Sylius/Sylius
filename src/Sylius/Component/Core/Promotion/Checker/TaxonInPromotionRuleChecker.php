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

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class TaxonInPromotionRuleChecker implements TaxonInPromotionRuleCheckerInterface
{
    /** @param RepositoryInterface<PromotionRuleInterface> $promotionRuleRepository */
    public function __construct(private RepositoryInterface $promotionRuleRepository)
    {
    }

    public function isInUse(TaxonInterface $taxon): bool
    {
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => TotalOfItemsFromTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $configuration = $promotionRule->getConfiguration();

            foreach ($configuration as $key => $value) {
                if (isset($value['taxon']) && $taxon->getCode() === $value['taxon']) {
                    return true;
                }
            }
        }

        return false;
    }
}
