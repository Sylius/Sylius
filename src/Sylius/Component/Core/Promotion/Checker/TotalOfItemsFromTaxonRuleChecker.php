<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TotalOfItemsFromTaxonRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'total_of_items_from_taxon';

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

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

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_total_of_items_from_taxon_configuration';
    }
}
