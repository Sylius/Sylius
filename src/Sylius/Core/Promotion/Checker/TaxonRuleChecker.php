<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Checker;

use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemInterface;
use Sylius\Promotion\Checker\RuleCheckerInterface;
use Sylius\Promotion\Exception\UnsupportedTypeException;
use Sylius\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'taxon';

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
            foreach ($item->getProduct()->getTaxons() as $taxon) {
                if (in_array($taxon->getCode(), $configuration['taxons'], true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_taxon_configuration';
    }
}
