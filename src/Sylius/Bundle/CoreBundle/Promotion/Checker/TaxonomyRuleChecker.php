<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion\Checker;

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * Checks if order containes products with given taxonomy.
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
            throw new OrderInterfaceNotImplementedException($subject);
        }

        foreach ($subject->getInventoryUnits() as $unit) {
            foreach ($unit->getStockable()->getProduct()->getTaxons() as $taxon) {
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
