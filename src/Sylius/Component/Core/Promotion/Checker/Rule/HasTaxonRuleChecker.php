<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class HasTaxonRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'has_taxon';

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!isset($configuration['taxons'])) {
            return;
        }

        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        /* @var $item OrderItemInterface */
        foreach ($subject->getItems() as $item) {
            foreach ($item->getProduct()->getProductTaxons() as $productTaxon) {
                if (in_array($productTaxon->getTaxon()->getCode(), $configuration['taxons'], true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
