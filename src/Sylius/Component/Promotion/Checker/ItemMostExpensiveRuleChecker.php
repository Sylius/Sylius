<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Checks if subject’s total exceeds (or at least equal) to the configured amount.
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 */
class ItemMostExpensiveRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderItemInterface) {
            throw new UnsupportedTypeException($subject, 'Sylius\Component\Core\Model\OrderItemInterface');
        }

        /* @var $item OrderItemInterface */
        foreach ($subject->getOrder()->getItems() as $item) {
            if ($item->getUnitPrice() > $subject->getUnitPrice()) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_item_most_expensive_configuration';
    }
}

