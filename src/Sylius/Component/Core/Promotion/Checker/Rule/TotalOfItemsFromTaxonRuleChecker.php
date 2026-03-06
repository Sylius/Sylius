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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TotalOfItemsFromTaxonRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_of_items_from_taxon';

    public function __construct(private TaxonRepositoryInterface $taxonRepository)
    {
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @throws UnsupportedTypeException
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        $configuration = $configuration[$channelCode];

        if (!isset($configuration['taxon']) || !isset($configuration['amount'])) {
            return false;
        }

        $targetTaxon = $this->taxonRepository->findOneBy(['code' => $configuration['taxon']]);
        if (!$targetTaxon instanceof TaxonInterface) {
            return false;
        }

        $includeChildTaxons = $configuration['include_child_taxons'] ?? false;
        $itemsWithTaxonTotal = 0;

        /** @var OrderItemInterface $item */
        foreach ($subject->getItems() as $item) {
            $belongs = $includeChildTaxons
                ? $this->productBelongsToTaxonOrDescendant($item->getProduct(), $targetTaxon)
                : $this->productBelongsToTaxon($item->getProduct(), $targetTaxon);
            if ($belongs) {
                $itemsWithTaxonTotal += $item->getTotal();
            }
        }

        return $itemsWithTaxonTotal >= $configuration['amount'];
    }

    private function productBelongsToTaxon(?ProductInterface $product, TaxonInterface $targetTaxon): bool
    {
        if ($product === null) {
            return false;
        }

        return $product->getTaxons()->contains($targetTaxon);
    }

    private function productBelongsToTaxonOrDescendant(?ProductInterface $product, TaxonInterface $targetTaxon): bool
    {
        if ($product === null) {
            return false;
        }

        foreach ($product->getTaxons() as $taxon) {
            if ($taxon === $targetTaxon || $taxon->getAncestors()->contains($targetTaxon)) {
                return true;
            }
        }

        return false;
    }
}
