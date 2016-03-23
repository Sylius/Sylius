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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ItemsFromTaxonTotalRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'items_from_taxon_total';

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

        $itemsWithTaxonTotal = 0;
        /** @var OrderItemInterface $item */
        foreach ($subject->getItems() as $item) {
            if ($item->getProduct()->hasTaxon($configuration['taxon'])) {
                $itemsWithTaxonTotal += $item->getTotal();
            }
        }

        if ($itemsWithTaxonTotal < $configuration['amount']) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        // it will be implemented after moving promotions to new backend UI
        return null;
    }
}
