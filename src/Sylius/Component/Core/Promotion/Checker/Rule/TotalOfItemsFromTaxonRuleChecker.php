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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
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
        if (null === $targetTaxon) {
            return false;
        }

        $itemsWithTaxonTotal = 0;

        /** @var OrderItemInterface $item */
        foreach ($subject->getItems() as $item) {
            if ($item->getProduct()->hasTaxon($targetTaxon)) {
                $itemsWithTaxonTotal += $item->getTotal();
            }
        }

        return $itemsWithTaxonTotal >= $configuration['amount'];
    }
}
