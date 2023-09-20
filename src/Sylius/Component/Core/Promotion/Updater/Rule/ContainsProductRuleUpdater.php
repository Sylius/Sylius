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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @deprecated since Sylius 1.13 ane will be removed in Sylius 2.0
 */
final class ContainsProductRuleUpdater implements ProductAwareRuleUpdaterInterface
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

    public function updateAfterProductDeletion(ProductInterface $product): array
    {
        $updatedPromotionCodes = [];
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => ContainsProductRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $configuration = $promotionRule->getConfiguration();

            if ($product->getCode() === $configuration['product_code']) {
                $this->promotionRuleRepository->remove($promotionRule);
                $updatedPromotionCodes[] = $promotionRule->getPromotion()->getCode();
            }
        }

        return $updatedPromotionCodes;
    }
}
