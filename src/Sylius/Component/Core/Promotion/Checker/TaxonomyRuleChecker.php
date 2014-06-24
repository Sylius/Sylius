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

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if order contains products with given taxonomy.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonomyRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            return false;
        }

        /* @var $item OrderItemInterface */
        foreach ($subject->getItems() as $item) {
            foreach ($item->getProduct()->getTaxons() as $taxon) {
                if ($configuration['taxons']->contains($taxon->getId())) {
                    return !$configuration['exclude'];
                }
            }
        }

        return (Boolean) $configuration['exclude'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_taxonomy_configuration';
    }
}
